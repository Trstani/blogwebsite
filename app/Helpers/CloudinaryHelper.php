<?php

namespace App\Helpers;

class CloudinaryHelper
{
    /**
     * Extract public_id from Cloudinary URL
     *
     * Format: https://res.cloudinary.com/{cloud}/image/upload/v{version}/{public_id}.{ext}
     * or: https://res.cloudinary.com/{cloud}/image/upload/{public_id}.{ext}
     *
     * Returns: public_id (without extension)
     */
    public static function extractPublicId(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        // Check if valid Cloudinary URL
        if (! str_contains($url, 'res.cloudinary.com')) {
            return null;
        }

        try {
            // Parse URL
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';

            // Format: /v1_1/kjkvm9vj/image/upload/v1234567890/public_id.jpg
            // or: /v1_1/kjkvm9vj/image/upload/public_id.jpg

            if (preg_match('/image\/upload\/(v\d+\/)?(.+)\./', $path, $matches)) {
                return $matches[2];
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to extract public_id from Cloudinary URL', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Delete image from Cloudinary using API
     */
    public static function deleteImage(string $publicId): bool
    {
        try {
            $cloudName = config('services.cloudinary.cloud_name');
            $apiKey = config('services.cloudinary.api_key');
            $apiSecret = config('services.cloudinary.api_secret');

            if (! $cloudName || ! $apiKey || ! $apiSecret) {
                \Log::error('Cloudinary credentials missing in config');

                return false;
            }

            // Create request signature - parameters must be in alphabetical order for signature calculation
            $timestamp = time();
            $params = [
                'invalidate' => 'true',
                'public_id' => $publicId,
                'timestamp' => $timestamp,
            ];

            // Sort by key for signature calculation (required by Cloudinary)
            ksort($params);

            // Build string to sign: key=value&key=value&...{api_secret}
            $toSign = http_build_query($params).$apiSecret;
            $signature = sha1($toSign);

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => "https://api.cloudinary.com/v1_1/{$cloudName}/image/destroy",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query(array_merge($params, [
                    'api_key' => $apiKey,
                    'signature' => $signature,
                ])),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                \Log::info('Cloudinary image deleted successfully', ['public_id' => $publicId]);

                return true;
            } else {
                \Log::warning('Failed to delete Cloudinary image', [
                    'public_id' => $publicId,
                    'http_code' => $httpCode,
                    'response' => $response,
                ]);

                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting Cloudinary image', [
                'public_id' => $publicId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
