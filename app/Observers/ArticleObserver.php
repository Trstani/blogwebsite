<?php

namespace App\Observers;

use App\Helpers\CloudinaryHelper;
use App\Jobs\DeleteCloudinaryImageJob;
use App\Models\Article;
use App\Services\LocalFileStorageService;

class ArticleObserver
{
    public function __construct(private LocalFileStorageService $fileService) {}

    /**
     * Handle the Article "updating" event.
     *
     * When article is updated:
     * - Check if cover_image is being replaced
     * - If yes, delete the old cover (from appropriate storage)
     */
    public function updating(Article $article): void
    {
        // Get original cover_image from database (before update)
        $originalCover = $article->getOriginal('cover_image');
        $newCover = $article->getAttribute('cover_image');

        // If cover changed and old one exists, queue deletion
        if ($originalCover && $originalCover !== $newCover) {
            // Check if original is local or Cloudinary
            if ($this->fileService->isLocalPath($originalCover)) {
                // Local image: delete from local storage
                $this->fileService->delete($originalCover);
                \Log::info('Deleted local article cover image', [
                    'article_id' => $article->id,
                    'path' => $originalCover,
                    'source' => 'cover_update',
                ]);
            } else {
                // Cloudinary image: queue async deletion
                $originalPublicId = $article->getOriginal('cover_image_public_id');
                if (! $originalPublicId) {
                    $originalPublicId = CloudinaryHelper::extractPublicId($originalCover);
                }

                if ($originalPublicId) {
                    dispatch(new DeleteCloudinaryImageJob($originalPublicId, 'article_cover'));
                    \Log::info('Queued Cloudinary article cover deletion', [
                        'article_id' => $article->id,
                        'old_public_id' => $originalPublicId,
                        'source' => 'cover_update',
                    ]);
                }
            }
        }
    }

    /**
     * Handle the Article "deleted" event.
     *
     * When article is deleted:
     * 1. Delete cover_image (local or Cloudinary)
     * 2. Delete all section images (each type handled appropriately)
     */
    public function deleted(Article $article): void
    {
        // Delete cover image
        if ($article->cover_image) {
            if ($this->fileService->isLocalPath($article->cover_image)) {
                // Local image
                $this->fileService->delete($article->cover_image);
                \Log::info('Deleted local article cover on article deletion', [
                    'article_id' => $article->id,
                    'path' => $article->cover_image,
                ]);
            } else {
                // Cloudinary image
                $publicId = $article->cover_image_public_id;
                if (! $publicId) {
                    $publicId = CloudinaryHelper::extractPublicId($article->cover_image);
                }

                if ($publicId) {
                    dispatch(new DeleteCloudinaryImageJob($publicId, 'article_cover'));
                    \Log::info('Queued Cloudinary article cover deletion', [
                        'article_id' => $article->id,
                        'public_id' => $publicId,
                    ]);
                }
            }
        }

        // Delete section images
        // Use loaded relationship if available, otherwise query
        $sections = $article->relationLoaded('sections')
            ? $article->sections
            : ($article->sections()->exists() ? $article->sections : []);

        if (count($sections) > 0) {
            foreach ($sections as $section) {
                if ($section->type === 'image' && $section->content) {
                    if ($this->fileService->isLocalPath($section->content)) {
                        // Local section image
                        $this->fileService->delete($section->content);
                        \Log::info('Deleted local section image on article deletion', [
                            'article_id' => $article->id,
                            'section_id' => $section->id,
                            'path' => $section->content,
                        ]);
                    } elseif ($section->image_public_id) {
                        // Cloudinary section image
                        dispatch(new DeleteCloudinaryImageJob($section->image_public_id, 'section_image'));
                        \Log::info('Queued Cloudinary section image deletion', [
                            'article_id' => $article->id,
                            'section_id' => $section->id,
                            'public_id' => $section->image_public_id,
                        ]);
                    }
                }
            }
        }

        \Log::info('Article deleted, queued cleanups for all images', [
            'article_id' => $article->id,
            'article_title' => $article->title,
        ]);
    }

    /**
     * Handle the Article "force deleted" event.
     * Same as deleted() for our use case
     */
    public function forceDeleted(Article $article): void
    {
        $this->deleted($article);
    }
}
