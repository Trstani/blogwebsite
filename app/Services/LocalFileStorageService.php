<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalFileStorageService
{
    /**
     * Allowed MIME types for uploads (non-GIF images only).
     */
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Allowed MIME types for GIF uploads.
     */
    private const ALLOWED_GIF_MIME_TYPES = [
        'image/gif',
    ];

    /**
     * Allowed file extensions.
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Allowed GIF file extensions.
     */
    private const ALLOWED_GIF_EXTENSIONS = ['gif'];

    /**
     * Maximum file size in bytes (10 MB).
     */
    private const MAX_FILE_SIZE = 10 * 1024 * 1024;

    /**
     * Root directory for all uploads.
     */
    private const UPLOADS_ROOT = 'uploads';

    /**
     * Valid upload categories and their subcategories.
     */
    private const VALID_PATHS = [
        'articles' => ['covers', 'sections', 'gifs'],
        'users' => ['avatars'],
    ];

    /**
     * Store an uploaded file with validation and unique filename generation.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  string  $category  Top-level category (e.g., 'articles', 'users')
     * @param  string  $subcategory  Subcategory (e.g., 'covers', 'avatars', 'gifs')
     * @param  bool  $isGif  Whether this is a GIF file (default: false)
     * @return array{path: string, url: string}
     *
     * @throws \InvalidArgumentException
     */
    public function store(UploadedFile $file, string $category, string $subcategory, bool $isGif = false): array
    {
        // Validate category and subcategory
        $this->validatePath($category, $subcategory);

        // Validate file
        $this->validateFile($file, $isGif);

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file);

        // Build storage path
        $storagePath = $this->buildStoragePath($category, $subcategory, $filename);

        // Store file
        Storage::disk('public')->putFileAs(
            dirname($storagePath),
            $file,
            $filename,
            'public'
        );

        // Generate public URL
        $publicUrl = Storage::disk('public')->url($storagePath);

        return [
            'path' => $storagePath,
            'url' => $publicUrl,
        ];
    }

    /**
     * Delete a file from local storage.
     *
     * @param  string  $path  Relative storage path (e.g., 'uploads/articles/covers/1750000000-abc.webp')
     * @return bool True if file was deleted, false if it didn't exist or path was invalid
     */
    public function delete(string $path): bool
    {
        \Log::info('LocalFileStorageService::delete() called', [
            'original_path' => $path,
        ]);

        // Normalize the path
        $normalizedPath = $this->normalizePath($path);

        \Log::info('Path normalization result', [
            'original' => $path,
            'normalized' => $normalizedPath,
            'is_null' => $normalizedPath === null,
        ]);

        if ($normalizedPath === null) {
            \Log::warning('Path normalization returned null', ['path' => $path]);

            return false;
        }

        // Prevent path traversal
        if (! $this->isValidStoragePath($normalizedPath)) {
            \Log::warning('Path failed security validation', [
                'normalized_path' => $normalizedPath,
            ]);

            return false;
        }

        \Log::info('Path passed security validation', [
            'normalized_path' => $normalizedPath,
        ]);

        // Check if file exists
        if (! Storage::disk('public')->exists($normalizedPath)) {
            \Log::warning('File does not exist in storage', [
                'path' => $normalizedPath,
            ]);

            return false;
        }

        \Log::info('File exists, attempting deletion', [
            'path' => $normalizedPath,
        ]);

        // Delete the file
        $result = Storage::disk('public')->delete($normalizedPath);

        \Log::info('Delete operation completed', [
            'path' => $normalizedPath,
            'success' => $result,
        ]);

        return $result;
    }

    /**
     * Determine if a value is a local storage path.
     *
     * @param  ?string  $value  The value to check
     */
    public function isLocalPath(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        // Reject Cloudinary URLs
        if (Str::startsWith($value, ['https://res.cloudinary.com', 'http://res.cloudinary.com'])) {
            return false;
        }

        // Recognize local storage paths
        $normalizedPath = $this->normalizePath($value);

        return $normalizedPath !== null && $this->isValidStoragePath($normalizedPath);
    }

    /**
     * Normalize a storage path or public URL to the relative storage path.
     *
     * @param  string  $value  A public URL or relative storage path
     * @return ?string The normalized relative storage path, or null if invalid
     */
    public function normalizePath(string $value): ?string
    {
        // Strip leading/trailing whitespace
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        // If it starts with /storage/, strip that prefix
        if (Str::startsWith($value, '/storage/')) {
            $value = Str::substr($value, 9); // Remove '/storage/'
        }

        // If it starts with just /, strip that
        if (Str::startsWith($value, '/') && ! Str::startsWith($value, '//')) {
            $value = Str::substr($value, 1);
        }

        // Should now be something like 'uploads/...'
        if (! Str::startsWith($value, self::UPLOADS_ROOT.'/')) {
            return null;
        }

        return $value;
    }

    /**
     * Validate the file before storage.
     *
     * @param  bool  $isGif  Whether this is a GIF file
     *
     * @throws \InvalidArgumentException
     */
    private function validateFile(UploadedFile $file, bool $isGif = false): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \InvalidArgumentException(
                'File size exceeds maximum of '.(self::MAX_FILE_SIZE / 1024 / 1024).' MB'
            );
        }

        if ($isGif) {
            // GIF validation
            $mimeType = $file->getMimeType();
            if (! in_array($mimeType, self::ALLOWED_GIF_MIME_TYPES, true)) {
                throw new \InvalidArgumentException(
                    "File type '{$mimeType}' is not allowed. Allowed types: GIF"
                );
            }

            $extension = $file->getClientOriginalExtension();
            if (! in_array(strtolower($extension), self::ALLOWED_GIF_EXTENSIONS, true)) {
                throw new \InvalidArgumentException(
                    "File extension '{$extension}' is not allowed. Only GIF files are allowed."
                );
            }
        } else {
            // Regular image validation (JPEG, PNG, WebP)
            $mimeType = $file->getMimeType();
            if (! in_array($mimeType, self::ALLOWED_MIME_TYPES, true)) {
                throw new \InvalidArgumentException(
                    "File type '{$mimeType}' is not allowed. Allowed types: JPEG, PNG, WebP"
                );
            }

            $extension = $file->getClientOriginalExtension();
            if (! in_array(strtolower($extension), self::ALLOWED_EXTENSIONS, true)) {
                throw new \InvalidArgumentException(
                    "File extension '{$extension}' is not allowed"
                );
            }
        }
    }

    /**
     * Validate category and subcategory.
     *
     *
     * @throws \InvalidArgumentException
     */
    private function validatePath(string $category, string $subcategory): void
    {
        if (! isset(self::VALID_PATHS[$category])) {
            throw new \InvalidArgumentException(
                "Invalid category: '{$category}'. Valid categories: ".implode(', ', array_keys(self::VALID_PATHS))
            );
        }

        if (! in_array($subcategory, self::VALID_PATHS[$category], true)) {
            throw new \InvalidArgumentException(
                "Invalid subcategory: '{$subcategory}' for category '{$category}'"
            );
        }
    }

    /**
     * Generate a unique, collision-resistant filename.
     *
     * Format: {timestamp}-{random12}.{extension}
     * Example: 1750000000-aBcDeF123456.webp
     */
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $timestamp = now()->timestamp;
        $random = Str::random(12);
        $extension = $file->getClientOriginalExtension();

        return "{$timestamp}-{$random}.{$extension}";
    }

    /**
     * Build the full storage path.
     */
    private function buildStoragePath(string $category, string $subcategory, string $filename): string
    {
        $root = self::UPLOADS_ROOT;

        return "{$root}/{$category}/{$subcategory}/{$filename}";
    }

    /**
     * Check if a path is within the uploads directory and safe.
     */
    private function isValidStoragePath(string $path): bool
    {
        // Must start with uploads/
        if (! Str::startsWith($path, self::UPLOADS_ROOT.'/')) {
            return false;
        }

        // Reject path traversal attempts
        if (Str::contains($path, ['..', '\\', "\0"])) {
            return false;
        }

        // Extract the directory structure
        $parts = explode('/', $path);

        // Should be: uploads / category / subcategory / filename
        if (count($parts) < 4) {
            return false;
        }

        $category = $parts[1];
        $subcategory = $parts[2];

        // Validate against known paths
        return isset(self::VALID_PATHS[$category]) &&
               in_array($subcategory, self::VALID_PATHS[$category], true);
    }
}
