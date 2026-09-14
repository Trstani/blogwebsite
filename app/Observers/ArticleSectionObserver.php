<?php

namespace App\Observers;

use App\Jobs\DeleteCloudinaryImageJob;
use App\Models\ArticleSection;
use App\Services\LocalFileStorageService;

class ArticleSectionObserver
{
    public function __construct(private LocalFileStorageService $fileService) {}

    /**
     * Handle the ArticleSection "deleted" event.
     *
     * When a section is deleted:
     * - If it's a local image/GIF, delete from local storage
     * - If it's a Cloudinary image/GIF, queue async deletion
     */
    public function deleted(ArticleSection $section): void
    {
        \Log::info('ArticleSectionObserver::deleted() called', [
            'section_id' => $section->id,
            'article_id' => $section->article_id,
            'type' => $section->type,
            'content' => $section->content ? substr($section->content, 0, 50) : null,
            'has_content' => ! empty($section->content),
            'public_id' => $section->image_public_id ? substr($section->image_public_id, 0, 30) : null,
        ]);

        // Handle image and GIF sections (both store files that need cleanup)
        if (($section->type === 'image' || $section->type === 'gif') && $section->content) {
            \Log::info('Section is image/GIF with content, checking if local', [
                'section_id' => $section->id,
                'content' => $section->content,
            ]);

            if ($this->fileService->isLocalPath($section->content)) {
                \Log::info('Path is local, attempting delete', [
                    'section_id' => $section->id,
                    'path' => $section->content,
                ]);

                // Local file (image or GIF): delete immediately
                $deleted = $this->fileService->delete($section->content);

                \Log::info("Deletion result for local section {$section->type}", [
                    'section_id' => $section->id,
                    'article_id' => $section->article_id,
                    'type' => $section->type,
                    'path' => $section->content,
                    'deleted' => $deleted,
                ]);
            } elseif ($section->image_public_id) {
                // Cloudinary file (image or GIF): queue async deletion
                dispatch(new DeleteCloudinaryImageJob($section->image_public_id, 'section_image'));
                \Log::info("Queued Cloudinary section {$section->type} deletion", [
                    'section_id' => $section->id,
                    'article_id' => $section->article_id,
                    'type' => $section->type,
                    'public_id' => $section->image_public_id,
                ]);
            } else {
                \Log::info('Section has content but is neither local nor Cloudinary', [
                    'section_id' => $section->id,
                    'content' => $section->content,
                    'public_id' => $section->image_public_id,
                ]);
            }
        } else {
            \Log::info('Section skipped - not image/gif or no content', [
                'section_id' => $section->id,
                'type' => $section->type,
                'has_content' => ! empty($section->content),
            ]);
        }
    }

    /**
     * Handle the ArticleSection "force deleted" event.
     */
    public function forceDeleted(ArticleSection $section): void
    {
        $this->deleted($section);
    }
}
