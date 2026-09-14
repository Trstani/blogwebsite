<?php

use App\Helpers\ImageHelper;
use Carbon\Carbon;

/**
 * Transform a local or Cloudinary image path to a displayable URL.
 *
 * @param  ?string  $path  The image path or URL
 * @return ?string The displayable URL or null
 */
function imageUrl(?string $path): ?string
{
    return ImageHelper::imageUrl($path);
}

/**
 * Determine if a value is a local storage path.
 *
 * @param  ?string  $value  The value to check
 * @return bool True if it's a local storage path
 */
function isLocalPath(?string $value): bool
{
    return ImageHelper::isLocalPath($value);
}

/**
 * Determine if a value is a Cloudinary URL.
 *
 * @param  ?string  $value  The value to check
 * @return bool True if it's a Cloudinary URL
 */
function isCloudinaryUrl(?string $value): bool
{
    return ImageHelper::isCloudinaryUrl($value);
}

/**
 * Format a date for display.
 *
 * @param  ?string  $date  The date to format
 * @return string The formatted date
 */
function fmtDate(?string $date): string
{
    return $date ? Carbon::parse($date)->format('M d, Y') : '';
}
