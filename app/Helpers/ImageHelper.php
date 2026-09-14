<?php

namespace App\Helpers;

use App\Services\LocalFileStorageService;

class ImageHelper
{
    /**
     * Transform a local or Cloudinary image path to a displayable URL.
     *
     * Behavior:
     * - Local paths (uploads/...) → /storage/uploads/...
     * - Paths already with /storage/ → unchanged
     * - Cloudinary URLs → unchanged
     * - null/empty → null
     *
     * @param  ?string  $path  The image path or URL
     * @return ?string The displayable URL or null
     */
    public static function imageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        $path = trim($path);

        if ($path === '') {
            return null;
        }

        // Already has /storage/ prefix
        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        // Cloudinary URL - leave unchanged
        if (str_starts_with($path, 'https://') || str_starts_with($path, 'http://')) {
            return $path;
        }

        // Local path without /storage/ prefix
        if (str_starts_with($path, 'uploads/')) {
            return '/storage/'.$path;
        }

        // Fallback: return as-is
        return $path;
    }

    /**
     * Determine if a value is a local storage path.
     *
     * @param  ?string  $value  The value to check
     * @return bool True if it's a local storage path
     */
    public static function isLocalPath(?string $value): bool
    {
        $service = app(LocalFileStorageService::class);

        return $service->isLocalPath($value);
    }

    /**
     * Determine if a value is a Cloudinary URL.
     *
     * @param  ?string  $value  The value to check
     * @return bool True if it's a Cloudinary URL
     */
    public static function isCloudinaryUrl(?string $value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        return str_starts_with($value, 'https://res.cloudinary.com') || str_starts_with($value, 'http://res.cloudinary.com');
    }
}
