<?php

namespace App\Observers;

use App\Helpers\CloudinaryHelper;
use App\Jobs\DeleteCloudinaryImageJob;
use App\Models\User;
use App\Services\LocalFileStorageService;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    public function __construct(private LocalFileStorageService $fileService) {}

    /**
     * Handle the User "updating" event.
     *
     * When user updates avatar (changes old avatar to new one):
     * - Delete old local avatar from storage immediately
     * - Queue Cloudinary deletion for old Cloudinary avatar
     */
    public function updating(User $user): void
    {
        // Get original avatar from database (before update)
        $originalAvatar = $user->getOriginal('avatar');
        $newAvatar = $user->getAttribute('avatar');

        // If avatar changed and old one exists, delete appropriately
        if ($originalAvatar && $originalAvatar !== $newAvatar) {
            if ($this->fileService->isLocalPath($originalAvatar)) {
                // Local file - delete immediately
                $path = str_replace('/storage/', '', $originalAvatar);
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    \Log::info('Deleted local avatar on update', [
                        'user_id' => $user->id,
                        'path' => $path,
                    ]);
                }
            } else {
                // Cloudinary - queue deletion
                $publicId = $user->getOriginal('avatar_public_id');
                if (! $publicId) {
                    $publicId = CloudinaryHelper::extractPublicId($originalAvatar);
                }

                if ($publicId) {
                    dispatch(new DeleteCloudinaryImageJob($publicId, 'user_avatar'));
                    \Log::info('Queued Cloudinary image deletion for old avatar', [
                        'user_id' => $user->id,
                        'public_id' => $publicId,
                        'source' => 'avatar_update',
                    ]);
                }
            }
        }
    }

    /**
     * Handle the User "deleted" event.
     *
     * When user is deleted:
     * - Delete local avatar from storage immediately
     * - Queue Cloudinary deletion for Cloudinary avatar
     */
    public function deleted(User $user): void
    {
        if ($user->avatar) {
            if ($this->fileService->isLocalPath($user->avatar)) {
                // Local file - delete immediately
                $path = str_replace('/storage/', '', $user->avatar);
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    \Log::info('Deleted local avatar on user delete', [
                        'user_id' => $user->id,
                        'path' => $path,
                    ]);
                }
            } else {
                // Cloudinary - queue deletion
                $publicId = $user->avatar_public_id;
                if (! $publicId) {
                    $publicId = CloudinaryHelper::extractPublicId($user->avatar);
                }

                if ($publicId) {
                    dispatch(new DeleteCloudinaryImageJob($publicId, 'user_avatar'));
                    \Log::info('Queued Cloudinary image deletion for user avatar on delete', [
                        'user_id' => $user->id,
                        'public_id' => $publicId,
                    ]);
                }
            }
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->deleted($user);
    }
}
