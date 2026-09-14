<?php

namespace App\Jobs;

use App\Helpers\CloudinaryHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteCloudinaryImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $publicId,
        private ?string $resourceType = null,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info('Processing delete job for Cloudinary image', [
            'public_id' => $this->publicId,
            'resource_type' => $this->resourceType,
            'attempt' => $this->attempts(),
        ]);

        $success = CloudinaryHelper::deleteImage($this->publicId);

        if (! $success && $this->attempts() < $this->tries) {
            // Retry with exponential backoff
            $this->release($this->backoff ** ($this->attempts() - 1));
            \Log::warning('Retrying Cloudinary image deletion', [
                'public_id' => $this->publicId,
                'attempt' => $this->attempts(),
                'next_retry_in_seconds' => $this->backoff ** ($this->attempts() - 1),
            ]);
        } elseif (! $success) {
            \Log::error('Failed to delete Cloudinary image after max retries', [
                'public_id' => $this->publicId,
                'max_attempts' => $this->tries,
            ]);
        }
    }

    /**
     * Handle failed job
     */
    public function failed(\Throwable $exception): void
    {
        \Log::error('DeleteCloudinaryImageJob failed', [
            'public_id' => $this->publicId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
