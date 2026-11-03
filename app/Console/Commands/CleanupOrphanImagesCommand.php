<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\User;
use App\Services\LocalFileStorageService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CleanupOrphanImagesCommand extends Command
{
    protected $signature = 'cleanup:orphan-images
                            {--dry-run : Preview orphan images without deleting them}
                            {--min-age-hours=24 : Minimum age of orphan files in hours}';

    protected $description = 'Delete unreferenced local images older than the configured minimum age';

    private const DIRECTORIES = [
        'uploads/articles/covers',
        'uploads/articles/sections',
        'uploads/articles/gifs',
        'uploads/users/avatars',
    ];

    private const ALLOWED_EXTENSIONS = [
        'jpg',
        'jpeg',
        'png',
        'webp',
        'gif',
    ];

    public function handle(LocalFileStorageService $storageService): int
    {
        $disk = Storage::disk('public');

        $minAgeHours = (int) $this->option('min-age-hours');

        if ($minAgeHours < 1) {
            $this->error('Minimum age must be at least 1 hour.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        $this->info('Starting orphan image cleanup...');
        $this->line("Minimum file age: {$minAgeHours} hours");
        $this->line('Mode: '.($dryRun ? 'DRY RUN' : 'DELETE'));

        /*
         * Step 1: Collect all image references from database.
         *
         * If any query fails, abort the entire cleanup.
         */
        try {
            $referencedPaths = collect();

            $referencedPaths = $referencedPaths->merge(
                Article::query()
                    ->whereNotNull('cover_image')
                    ->pluck('cover_image')
            );

            $referencedPaths = $referencedPaths->merge(
                ArticleSection::query()
                    ->whereIn('type', ['image', 'gif'])
                    ->whereNotNull('content')
                    ->pluck('content')
            );

            $referencedPaths = $referencedPaths->merge(
                User::query()
                    ->whereNotNull('avatar')
                    ->pluck('avatar')
            );

            $referencedPaths = $referencedPaths
                ->map(fn ($path) => $storageService->normalizePath($path))
                ->filter()
                ->unique()
                ->flip();

        } catch (Throwable $e) {
            Log::critical('Orphan image cleanup aborted: database reference query failed.', [
                'error' => $e->getMessage(),
            ]);

            $this->error('Database reference collection failed. Cleanup aborted.');

            return self::FAILURE;
        }

        $this->info('Referenced local images: '.$referencedPaths->count());

        /*
         * Step 2: Scan approved directories.
         */
        $totalScanned = 0;
        $totalOrphans = 0;
        $totalDeleted = 0;
        $totalFailed = 0;
        $totalBytesFreed = 0;

        $cutoffTime = now()->subHours($minAgeHours)->timestamp;

        foreach (self::DIRECTORIES as $directory) {
            $this->newLine();
            $this->info("Scanning: {$directory}/");

            try {
                $files = $disk->files($directory);

                foreach ($files as $path) {
                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

                    if (! in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
                        continue;
                    }

                    $totalScanned++;

                    /*
                     * Normalize and validate path.
                     */
                    $normalizedPath = $storageService->normalizePath($path);

                    if (
                        $normalizedPath === null ||
                        $normalizedPath !== $path ||
                        ! str_starts_with($normalizedPath, $directory.'/')
                    ) {
                        continue;
                    }

                    /*
                     * Ignore files referenced by database.
                     */
                    if ($referencedPaths->has($normalizedPath)) {
                        continue;
                    }

                    /*
                     * Check file age.
                     */
                    $lastModified = $disk->lastModified($path);

                    if ($lastModified >= $cutoffTime) {
                        continue;
                    }

                    $totalOrphans++;

                    $fileSize = $disk->size($path);

                    if ($dryRun) {
                        $this->warn("[DRY RUN] Would delete: {$path}");

                        continue;
                    }

                    /*
                     * Delete orphan file.
                     */
                    try {
                        if (! $disk->exists($path)) {
                            continue;
                        }

                        $deleted = $disk->delete($path);

                        if ($deleted) {
                            $totalDeleted++;
                            $totalBytesFreed += $fileSize;

                            Log::info('Orphan image deleted.', [
                                'path' => $path,
                                'size' => $fileSize,
                            ]);

                            $this->line("Deleted: {$path}");
                        } else {
                            $totalFailed++;

                            Log::warning('Failed to delete orphan image.', [
                                'path' => $path,
                            ]);
                        }

                    } catch (Throwable $e) {
                        $totalFailed++;

                        Log::error('Orphan image deletion exception.', [
                            'path' => $path,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }

            } catch (Throwable $e) {
                Log::critical('Orphan image scan failed.', [
                    'directory' => $directory,
                    'error' => $e->getMessage(),
                ]);

                $this->error("Failed scanning {$directory}. Cleanup aborted.");

                return self::FAILURE;
            }
        }

        /*
         * Step 3: Report results.
         */
        $this->newLine();
        $this->info('========== CLEANUP SUMMARY ==========');

        $this->line("Scanned files: {$totalScanned}");
        $this->line("Referenced files: ".$referencedPaths->count());
        $this->line("Orphan files detected: {$totalOrphans}");
        $this->line("Files deleted: {$totalDeleted}");
        $this->line("Failed deletions: {$totalFailed}");
        $this->line('Disk space freed: '.round($totalBytesFreed / 1024 / 1024, 2).' MB');

        Log::info('Orphan image cleanup completed.', [
            'dry_run' => $dryRun,
            'min_age_hours' => $minAgeHours,
            'scanned' => $totalScanned,
            'referenced' => $referencedPaths->count(),
            'orphans' => $totalOrphans,
            'deleted' => $totalDeleted,
            'failed' => $totalFailed,
            'bytes_freed' => $totalBytesFreed,
        ]);

        $this->info('Orphan image cleanup completed.');

        return self::SUCCESS;
    }
}