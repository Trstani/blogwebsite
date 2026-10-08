<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class SearchController extends Controller
{
    /**
     * Get autocomplete suggestions for articles and tags.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * Query parameters:
     * - q: Search query (minimum 2 characters, required)
     *
     * Response schema:
     * {
     *   "articles": [
     *     {
     *       "id": int,
     *       "title": string,
     *       "slug": string,
     *       "category": string,
     *       "thumbnail": string
     *     }
     *   ],
     *   "tags": [
     *     {
     *       "id": int,
     *       "name": string,
     *       "slug": string
     *     }
     *   ]
     * }
     */
    public function suggestions(Request $request)
    {
        $query = $request->query('q', '');

        // Validate query length (minimum 2 characters)
        if (strlen(trim($query)) < 2) {
            return response()->json([
                'articles' => [],
                'tags' => [],
            ]);
        }

        $searchTerm = '%' . $query . '%';

        /*
        |--------------------------------------------------------------------------
        | Article candidates
        |--------------------------------------------------------------------------
        | Published only, maximum 5 results.
        |
        | Ranking:
        | 1. Title match
        | 2. Tag name match
        | 3. Description match
        | 4. Slug match
        | 5. Category match
        |--------------------------------------------------------------------------
        */
        $articles = Article::where('status', 'published')
            ->with('category')
            ->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)

                    // Tag name matches
                    ->orWhereHas('tags', function ($tagQ) use ($searchTerm) {
                        $tagQ->where('name', 'like', $searchTerm);
                    })

                    // Description matches
                    ->orWhere('description', 'like', $searchTerm)

                    // Slug matches
                    ->orWhere('slug', 'like', $searchTerm)

                    // Category matches
                    ->orWhereHas('category', function ($catQ) use ($searchTerm) {
                        $catQ->where('name', 'like', $searchTerm);
                    });
            })
            ->orderByRaw("
                CASE
                    WHEN title LIKE ? THEN 1

                    WHEN EXISTS (
                        SELECT 1
                        FROM article_tag
                        INNER JOIN tags ON tags.id = article_tag.tag_id
                        WHERE article_tag.article_id = articles.id
                        AND tags.name LIKE ?
                    ) THEN 2

                    WHEN description LIKE ? THEN 3
                    WHEN slug LIKE ? THEN 4

                    ELSE 5
                END
            ", [
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm,
            ])
            ->take(3)
            ->get()
            ->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'slug' => $a->slug,
                'category' => $a->category->slug ?? '',
                'thumbnail' => $a->cover_image ? Storage::disk('public')->url($a->cover_image): '',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Tag candidates
        |--------------------------------------------------------------------------
        | Only tags used by at least one published article.
        | Maximum 5 results.
        |--------------------------------------------------------------------------
        */
        $tags = Tag::whereHas('articles', function ($q) {
                $q->where('status', 'published');
            })
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('slug', 'like', $searchTerm);
            })
            ->orderByRaw("
                CASE
                    WHEN name LIKE ? THEN 1
                    ELSE 2
                END
            ", [$searchTerm])
            ->take(3)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
            ]);

        return response()->json([
            'articles' => $articles,
            'tags' => $tags,
        ]);
    }
}