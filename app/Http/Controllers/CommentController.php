<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Store a newly created comment for an article.
     */
    public function store(Request $request, Article $article): JsonResponse
    {
        // Must be authenticated
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to comment.',
            ], 403);
        }

        // Article must be published
        if ($article->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Comments are only available for published articles.',
            ], 403);
        }

        // Validate comment content and optional parent_id
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|integer|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $parentId = $request->input('parent_id');

            // If parent_id is provided, validate it
            if ($parentId) {
                $parentComment = Comment::find($parentId);

                // Parent must exist
                if (! $parentComment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent comment not found.',
                    ], 404);
                }

                // Parent must belong to the same article
                if ($parentComment->article_id !== $article->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent comment must belong to the same article.',
                    ], 403);
                }

                // Parent must be a root comment (not a reply itself)
                if ($parentComment->parent_id !== null) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot reply to a reply. Replies can only be one level deep.',
                    ], 403);
                }
            }

            // Create comment or reply with authenticated user
            $comment = Comment::create([
                'article_id' => $article->id,
                'user_id' => auth()->id(),
                'parent_id' => $parentId ?: null,
                'content' => trim($request->input('content')),
            ]);

            // Load user relationship for response
            $comment->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Comment created successfully.',
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'name' => $comment->user->name,
                        'avatar' => $comment->user->avatar,
                    ],
                    'created_at' => $comment->created_at->format('M d, Y \a\t g:i A'),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the comment.',
            ], 500);
        }
    }
}
