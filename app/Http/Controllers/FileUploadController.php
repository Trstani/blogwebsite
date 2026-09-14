<?php

namespace App\Http\Controllers;

use App\Services\LocalFileStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    public function __construct(private LocalFileStorageService $storageService) {}

    /**
     * Upload an image for article sections.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,webp|max:10240',
        ]);

        try {
            $result = $this->storageService->store(
                $request->file('file'),
                'articles',
                'sections'
            );

            return response()->json([
                'success' => true,
                'path' => $result['path'],
                'url' => $result['url'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     * Upload a cover image for articles.
     */
    public function uploadCover(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,webp|max:10240',
        ]);

        try {
            $result = $this->storageService->store(
                $request->file('file'),
                'articles',
                'covers'
            );

            return response()->json([
                'success' => true,
                'path' => $result['path'],
                'url' => $result['url'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     * Upload an avatar for users.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,webp|max:10240',
        ]);

        try {
            $result = $this->storageService->store(
                $request->file('file'),
                'users',
                'avatars'
            );

            return response()->json([
                'success' => true,
                'path' => $result['path'],
                'url' => $result['url'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the file.',
            ], 500);
        }
    }

    /**
     * Upload a GIF for article sections.
     */
    public function uploadGif(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:gif|max:10240',
        ]);

        try {
            $result = $this->storageService->store(
                $request->file('file'),
                'articles',
                'gifs',
                true
            );

            return response()->json([
                'success' => true,
                'path' => $result['path'],
                'url' => $result['url'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the GIF.',
            ], 500);
        }
    }
}
