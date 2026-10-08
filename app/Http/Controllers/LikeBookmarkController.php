<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleLike;
use App\Models\ArticleBookmark;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeBookmarkController extends Controller
{
    /**
     * Toggle like on an article.
     * If the user has liked the article, remove the like.
     * If not, create a new like.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function toggleLike(Article $article): JsonResponse
    {
        $user = auth()->user();

        // Check if user already liked this article
        $existingLike = ArticleLike::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existingLike) {
            // Remove the like
            $existingLike->delete();
            $liked = false;
        } else {
            // Create a new like
            ArticleLike::create([
                'user_id' => $user->id,
                'article_id' => $article->id,
            ]);
            $liked = true;
        }

        // Get current like count
        $likeCount = $article->likes()->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'count' => $likeCount,
            'message' => $liked ? 'Article liked' : 'Like removed',
        ]);
    }

    /**
     * Toggle bookmark on an article.
     * If the user has bookmarked the article, remove the bookmark.
     * If not, create a new bookmark.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function toggleBookmark(Article $article): JsonResponse
    {
        $user = auth()->user();

        // Check if user already bookmarked this article
        $existingBookmark = ArticleBookmark::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->first();

        if ($existingBookmark) {
            // Remove the bookmark
            $existingBookmark->delete();
            $bookmarked = false;
        } else {
            // Create a new bookmark
            ArticleBookmark::create([
                'user_id' => $user->id,
                'article_id' => $article->id,
            ]);
            $bookmarked = true;
        }

        // Get current bookmark count
        $bookmarkCount = $article->bookmarks()->count();

        return response()->json([
            'success' => true,
            'bookmarked' => $bookmarked,
            'count' => $bookmarkCount,
            'message' => $bookmarked ? 'Article bookmarked' : 'Bookmark removed',
        ]);
    }

    /**
     * Get like/bookmark status and counts for an article.
     *
     * @param Article $article
     * @return JsonResponse
     */
    public function getStatus(Article $article): JsonResponse
    {
        $user = auth()->user();

        $liked = ArticleLike::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->exists();

        $bookmarked = ArticleBookmark::where('user_id', $user->id)
            ->where('article_id', $article->id)
            ->exists();

        $likeCount = $article->likes()->count();
        $bookmarkCount = $article->bookmarks()->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'bookmarked' => $bookmarked,
            'likeCount' => $likeCount,
            'bookmarkCount' => $bookmarkCount,
        ]);
    }
}
