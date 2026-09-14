<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteCloudinaryImageJob;
use App\Models\Article;
use App\Services\LocalFileStorageService;
use App\Services\RichTextSanitizer;
use App\Services\VideoUrlHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    private function uploadToCloudinary($file, $folder = 'blog')
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');

        $url = "https://api.cloudinary.com/v1_1/{$cloudName}/image/upload";

        $fileContent = file_get_contents($file->getRealPath());

        \Log::info('Uploading to Cloudinary', [
            'file_size' => strlen($fileContent),
            'file_name' => $file->getClientOriginalName(),
        ]);

        $response = Http::attach(
            'file',
            $fileContent,
            $file->getClientOriginalName()
        )->post($url, [
            'api_key' => $apiKey,
            'folder' => $folder,
            'quality' => 'auto',
            'fetch_format' => 'auto',
            'resource_type' => 'auto',
        ]);

        \Log::info('Cloudinary response', [
            'status' => $response->status(),
            'has_url' => isset($response['secure_url']),
        ]);

        return $response->json();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string',
            'cover_image_public_id' => 'nullable|string',
        ]);

        $article = new Article;
        $article->title = $request->title;
        $article->slug = Str::slug($request->title).'-'.Str::random(8);
        $article->description = $request->description;
        $article->category_id = $request->category_id;
        $article->author_id = auth()->id();
        $article->status = 'draft';

        if ($request->cover_image) {
            $article->cover_image = $request->cover_image;
        }

        if ($request->cover_image_public_id) {
            $article->cover_image_public_id = $request->cover_image_public_id;
        }

        $article->save();

        return response()->json([
            'success' => true,
            'article_id' => $article->id,
            'message' => 'Article saved as draft.',
        ]);
    }

    public function uploadCover(Request $request)
    {
        try {
            // Browser sudah upload ke Cloudinary, kita cuma receive URL
            $request->validate([
                'image_url' => 'required|url',
                'public_id' => 'required|string',
            ]);

            \Log::info('Cover URL received from Cloudinary', [
                'url' => $request->image_url,
            ]);

            return response()->json([
                'success' => true,
                'url' => $request->image_url,
                'path' => $request->public_id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Cover error: '.$e->getMessage());

            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            // Browser sudah upload langsung ke Cloudinary,
            // kita cuma receive dan validate URL-nya
            $request->validate([
                'image_url' => 'required|url',
                'public_id' => 'required|string',
            ]);

            \Log::info('Image URL received from Cloudinary', [
                'url' => $request->image_url,
                'public_id' => $request->public_id,
            ]);

            return response()->json([
                'success' => true,
                'url' => $request->image_url,
                'path' => $request->public_id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload error: '.$e->getMessage());

            return response()->json(['error' => 'Server error'], 500);
        }
    }

   public function saveSections(Request $request, Article $article)
    {
        if ($article->author_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'sections' => 'required|array|min:1|max:20',
            'sections.*.type' => 'required|in:text,image,video,gif',
            'sections.*.content' => 'required|string|max:10000',
            'sections.*.public_id' => 'nullable|string',
        ]);

        \Log::info('Saving sections', [
            'article_id' => $article->id,
            'section_count' => count($request->sections),
            'sections_preview' => array_map(fn ($s) => [
                'type' => $s['type'],
                'has_public_id' => isset($s['public_id']) && ! empty($s['public_id']),
                'public_id_value' => $s['public_id'] ?? null,
            ], $request->sections),
        ]);

        $sanitizer = app(RichTextSanitizer::class);

        $article->sections()->delete();

        foreach ($request->sections as $index => $section) {
            $content = $section['content'];

            /*
            * Only text sections contain rich HTML.
            * Image, video and GIF content remain unchanged.
            */
            if ($section['type'] === 'text') {
                $content = $sanitizer->sanitize($content);
            }

            $article->sections()->create([
                'type' => $section['type'],
                'content' => $content,
                'image_public_id' => $section['public_id'] ?? null,
                'order' => $index + 1,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function previewVideo(Request $request, Article $article)
    {
        // Verify user owns this article
        if ($article->author_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'url' => 'required|string|max:500',
        ]);

        $url = trim($request->url);
        $videoHelper = app(VideoUrlHelper::class);

        // Check if URL is valid
        if (! $videoHelper->isValidVideoUrl($url)) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or unsupported video URL',
            ]);
        }

        // Generate embed HTML
        $embedHtml = $videoHelper->generateEmbedHtml($url);

        if ($embedHtml === null) {
            return response()->json([
                'valid' => false,
                'message' => 'Could not generate preview for this URL',
            ]);
        }

        // Return embed HTML for display
        return response()->json([
            'valid' => true,
            'provider' => $videoHelper->getProviderType($url),
            'embedHtml' => $embedHtml,
        ]);
    }

    public function deleteUnsavedImage(Request $request, Article $article)
    {
        // Verify user owns this article
        if ($article->author_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Accept either public_id (Cloudinary) or local_path (local storage)
        $request->validate([
            'public_id' => 'nullable|string',
            'local_path' => 'nullable|string',
        ]);

        $publicId = $request->public_id;
        $localPath = $request->local_path;

        if (! $publicId && ! $localPath) {
            return response()->json([
                'error' => 'Either public_id or local_path must be provided',
            ], 422);
        }

        \Log::info('Cleanup request for unsaved image', [
            'article_id' => $article->id,
            'has_public_id' => ! empty($publicId),
            'has_local_path' => ! empty($localPath),
            'user_id' => auth()->id(),
        ]);

        // Handle local file deletion
        if ($localPath) {
            $fileService = app(LocalFileStorageService::class);
            $deleted = $fileService->delete($localPath);
            \Log::info('Deleted unsaved local image', [
                'article_id' => $article->id,
                'path' => $localPath,
                'success' => $deleted,
            ]);
        }

        // Handle Cloudinary deletion
        if ($publicId) {
            dispatch(new DeleteCloudinaryImageJob($publicId, 'unsaved_section_image'));
            \Log::info('Queued Cloudinary deletion for unsaved image', [
                'article_id' => $article->id,
                'public_id' => $publicId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Image cleanup queued.',
        ]);
    }

    public function submit(Request $request, Article $article)
    {
        if ($article->author_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $article->status = 'pending';
        $article->save();

        return response()->json([
            'success' => true,
            'message' => 'Article submitted for review.',
        ]);
    }

    public function getArticle($id)
    {
        $article = Article::findOrFail($id);

        if ($article->author_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $sections = $article->sections->sortBy('order')->values();

        return response()->json([
            'success' => true,
            'article_id' => $article->id,
            'title' => $article->title,
            'description' => $article->description,
            'category' => $article->category->name ?? '',
            'category_id' => $article->category_id,
            'cover_image' => $article->cover_image,
            'status' => $article->status,
            'sections' => $sections->map(fn ($s) => [
                'id' => $s->id,
                'type' => $s->type,
                'content' => $s->content,
                'public_id' => $s->image_public_id,
            ])->toArray(),
        ]);
    }
}
