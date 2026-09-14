<?php

namespace App\Services;

use Illuminate\Support\Str;

class VideoUrlHelper
{
    /**
     * Supported video providers and their URL patterns.
     */
    private const PROVIDERS = [
        'youtube' => [
            'patterns' => [
                '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/',
                '/youtu\.be\/([a-zA-Z0-9_-]{11})/',
                '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            ],
            'embed_url' => 'https://www.youtube.com/embed/{id}',
        ],
        'vimeo' => [
            'patterns' => [
                '/vimeo\.com\/(\d+)/',
                '/player\.vimeo\.com\/video\/(\d+)/',
            ],
            'embed_url' => 'https://player.vimeo.com/video/{id}',
        ],
    ];

    /**
     * Allowed direct video file extensions.
     */
    private const ALLOWED_DIRECT_EXTENSIONS = ['mp4', 'webm', 'ogg'];

    /**
     * Validate a video URL.
     *
     * @param  ?string  $url  The URL to validate
     * @return bool True if valid, false otherwise
     */
    public function isValidVideoUrl(?string $url): bool
    {
        if ($url === null || $url === '') {
            return false;
        }

        $url = trim($url);

        // Must be HTTP/HTTPS
        if (! Str::startsWith($url, ['http://', 'https://'])) {
            return false;
        }

        // Reject dangerous schemes
        if (Str::contains($url, ['javascript:', 'data:', 'file:'])) {
            return false;
        }

        // Check if it's a supported provider
        if ($this->extractYouTubeId($url) !== null) {
            return true;
        }

        if ($this->extractVimeoId($url) !== null) {
            return true;
        }

        // Check if it's a direct video URL
        if ($this->isDirectVideoUrl($url)) {
            return true;
        }

        return false;
    }

    /**
     * Extract YouTube video ID from URL.
     *
     * @param  string  $url  The video URL
     * @return ?string The video ID, or null if not a YouTube URL
     */
    public function extractYouTubeId(string $url): ?string
    {
        foreach (self::PROVIDERS['youtube']['patterns'] as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Extract Vimeo video ID from URL.
     *
     * @param  string  $url  The video URL
     * @return ?string The video ID, or null if not a Vimeo URL
     */
    public function extractVimeoId(string $url): ?string
    {
        foreach (self::PROVIDERS['vimeo']['patterns'] as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Determine if URL is a direct video file.
     *
     * @param  string  $url  The URL to check
     * @return bool True if it appears to be a direct video URL
     */
    public function isDirectVideoUrl(string $url): bool
    {
        // Extract path from URL
        $path = parse_url($url, PHP_URL_PATH);

        if ($path === null || $path === '') {
            return false;
        }

        // Get file extension
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, self::ALLOWED_DIRECT_EXTENSIONS, true);
    }

    /**
     * Get the provider type for a video URL.
     *
     * @param  string  $url  The video URL
     * @return ?string 'youtube', 'vimeo', 'direct', or null if unknown
     */
    public function getProviderType(string $url): ?string
    {
        if ($this->extractYouTubeId($url) !== null) {
            return 'youtube';
        }

        if ($this->extractVimeoId($url) !== null) {
            return 'vimeo';
        }

        if ($this->isDirectVideoUrl($url)) {
            return 'direct';
        }

        return null;
    }

    /**
     * Generate an embed URL for a video.
     *
     * @param  string  $url  The original video URL
     * @return ?string The embed URL, or null if cannot be generated
     */
    public function getEmbedUrl(string $url): ?string
    {
        if ($youtubeId = $this->extractYouTubeId($url)) {
            return str_replace('{id}', $youtubeId, self::PROVIDERS['youtube']['embed_url']);
        }

        if ($vimeoId = $this->extractVimeoId($url)) {
            return str_replace('{id}', $vimeoId, self::PROVIDERS['vimeo']['embed_url']);
        }

        // Direct video URLs are used as-is
        if ($this->isDirectVideoUrl($url)) {
            return $url;
        }

        return null;
    }

    /**
     * Generate HTML for embedding a video.
     *
     * @param  string  $url  The original video URL
     * @return ?string Safe HTML for embedding, or null if cannot be generated
     */
    public function generateEmbedHtml(string $url): ?string
    {
        $provider = $this->getProviderType($url);
        $embedUrl = $this->getEmbedUrl($url);

        if ($embedUrl === null) {
            return null;
        }

        if ($provider === 'youtube' || $provider === 'vimeo') {
            return sprintf(
                '<iframe width="100%%" height="600" src="%s" frameborder="0" allowfullscreen style="border-radius: 8px;"></iframe>',
                htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8')
            );
        }

        if ($provider === 'direct') {
            return sprintf(
                '<video width="100%%" height="600" controls style="border-radius: 8px;"><source src="%s" type="video/%s"></video>',
                htmlspecialchars($embedUrl, ENT_QUOTES, 'UTF-8'),
                $this->getMimeTypeFromUrl($embedUrl)
            );
        }

        return null;
    }

    /**
     * Get MIME type from video URL.
     *
     * @param  string  $url  The video URL
     * @return string The MIME type (e.g., 'mp4', 'webm')
     */
    private function getMimeTypeFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'mp4' => 'mp4',
            'webm' => 'webm',
            'ogg' => 'ogg',
            default => 'mp4',
        };
    }
}
