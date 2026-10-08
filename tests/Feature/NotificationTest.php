<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\ArticleSubmittedForReviewNotification;
use App\Notifications\ArticleApprovedNotification;
use App\Notifications\ArticleRejectedNotification;
use App\Notifications\NewCommentNotification;
use App\Notifications\CommentReplyNotification;
use Illuminate\Support\Facades\Notification;

// ===== DATABASE NOTIFICATIONS TABLE TESTS =====

test('notifications table exists and can store data', function () {
    $user = User::factory()->create();

    $user->notify(new ArticleSubmittedForReviewNotification(
        Article::factory()->create(['author_id' => $user->id])
    ));

    expect($user->notifications)->toHaveCount(1);
    expect($user->notifications->first()->type)
        ->toBe('App\Notifications\ArticleSubmittedForReviewNotification');
});

test('notification has required fields', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleSubmittedForReviewNotification($article));

    $notification = $user->notifications->first();

    expect($notification->id)->toBeTruthy();
    expect($notification->notifiable_id)->toBe($user->id);
    expect($notification->notifiable_type)->toBe('App\Models\User');
    expect($notification->read_at)->toBeNull();
    expect($notification->created_at)->toBeTruthy();
    expect($notification->updated_at)->toBeTruthy();
});

test('notification read_at is nullable and updateable', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleSubmittedForReviewNotification($article));

    $notification = $user->notifications->first();
    expect($notification->read_at)->toBeNull();

    $notification->markAsRead();

    expect($notification->read_at)->not->toBeNull();
});

// ===== ARTICLE SUBMITTED FOR REVIEW NOTIFICATION =====

test('ArticleSubmittedForReviewNotification has correct data structure', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $author = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['author_id' => $author->id]);

    $admin->notify(new ArticleSubmittedForReviewNotification($article));

    $notification = $admin->notifications->first();
    $data = $notification->data;

    expect($data)->toHaveKeys(['article_id', 'article_title', 'article_slug', 'author_id', 'author_name']);
    expect($data['article_id'])->toBe($article->id);
    expect($data['article_title'])->toBe($article->title);
    expect($data['article_slug'])->toBe($article->slug);
    expect($data['author_id'])->toBe($author->id);
    expect($data['author_name'])->toBe($author->name);
});

test('ArticleSubmittedForReviewNotification uses database channel', function () {
    $notification = new ArticleSubmittedForReviewNotification(Article::factory()->create());
    $admin = User::factory()->create(['role' => 'admin']);

    expect($notification->via($admin))->toBe(['database']);
});

// ===== ARTICLE APPROVED NOTIFICATION =====

test('ArticleApprovedNotification has correct data structure', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['author_id' => $author->id]);

    $author->notify(new ArticleApprovedNotification($article));

    $notification = $author->notifications->first();
    $data = $notification->data;

    expect($data)->toHaveKeys(['article_id', 'article_title', 'article_slug', 'published_at']);
    expect($data['article_id'])->toBe($article->id);
    expect($data['article_title'])->toBe($article->title);
    expect($data['article_slug'])->toBe($article->slug);
});

test('ArticleApprovedNotification uses database channel', function () {
    $notification = new ArticleApprovedNotification(Article::factory()->create());
    $author = User::factory()->create(['role' => 'writer']);

    expect($notification->via($author))->toBe(['database']);
});

// ===== ARTICLE REJECTED NOTIFICATION =====

test('ArticleRejectedNotification has correct data structure', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create([
        'author_id' => $author->id,
        'admin_notes' => 'This article needs revision.',
    ]);

    $author->notify(new ArticleRejectedNotification($article));

    $notification = $author->notifications->first();
    $data = $notification->data;

    expect($data)->toHaveKeys(['article_id', 'article_title', 'article_slug', 'admin_notes']);
    expect($data['article_id'])->toBe($article->id);
    expect($data['article_title'])->toBe($article->title);
    expect($data['article_slug'])->toBe($article->slug);
    expect($data['admin_notes'])->toBe('This article needs revision.');
});

test('ArticleRejectedNotification uses database channel', function () {
    $notification = new ArticleRejectedNotification(Article::factory()->create());
    $author = User::factory()->create(['role' => 'writer']);

    expect($notification->via($author))->toBe(['database']);
});

// ===== NEW COMMENT NOTIFICATION =====

test('NewCommentNotification has correct data structure', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create(['role' => 'writer']);
    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'content' => 'This is a great article!',
    ]);

    $author->notify(new NewCommentNotification($comment, $article));

    $notification = $author->notifications->first();
    $data = $notification->data;

    expect($data)->toHaveKeys([
        'article_id', 'article_title', 'article_slug',
        'comment_id', 'commenter_id', 'commenter_name', 'comment_preview',
    ]);
    expect($data['article_id'])->toBe($article->id);
    expect($data['comment_id'])->toBe($comment->id);
    expect($data['commenter_id'])->toBe($commenter->id);
    expect($data['commenter_name'])->toBe($commenter->name);
    expect($data['comment_preview'])->toBe('This is a great article!');
});

test('NewCommentNotification truncates long comment preview', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create(['role' => 'writer']);
    $longComment = str_repeat('This is a very long comment. ', 10); // Over 100 chars
    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'content' => $longComment,
    ]);

    $author->notify(new NewCommentNotification($comment, $article));

    $notification = $author->notifications->first();
    $data = $notification->data;

    expect(strlen($data['comment_preview']))->toBeLessThanOrEqual(100);
});

test('NewCommentNotification uses database channel', function () {
    $notification = new NewCommentNotification(
        Comment::factory()->create(),
        Article::factory()->create()
    );
    $user = User::factory()->create();

    expect($notification->via($user))->toBe(['database']);
});

// ===== COMMENT REPLY NOTIFICATION =====

test('CommentReplyNotification has correct data structure', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Original comment author
    $commentAuthor = User::factory()->create(['role' => 'writer']);
    $parentComment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commentAuthor->id,
        'parent_id' => null,
    ]);

    // Reply author
    $replier = User::factory()->create(['role' => 'writer']);
    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier->id,
        'parent_id' => $parentComment->id,
        'content' => 'Thanks for your comment!',
    ]);

    $commentAuthor->notify(new CommentReplyNotification($reply, $article));

    $notification = $commentAuthor->notifications->first();
    $data = $notification->data;

    expect($data)->toHaveKeys([
        'article_id', 'article_title', 'article_slug',
        'comment_id', 'replier_id', 'replier_name', 'reply_preview',
    ]);
    expect($data['article_id'])->toBe($article->id);
    expect($data['comment_id'])->toBe($reply->id);
    expect($data['replier_id'])->toBe($replier->id);
    expect($data['replier_name'])->toBe($replier->name);
    expect($data['reply_preview'])->toBe('Thanks for your comment!');
});

test('CommentReplyNotification truncates long reply preview', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    $commentAuthor = User::factory()->create(['role' => 'writer']);
    $parentComment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commentAuthor->id,
        'parent_id' => null,
    ]);

    $replier = User::factory()->create(['role' => 'writer']);
    $longReply = str_repeat('This is a very long reply. ', 10); // Over 100 chars
    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier->id,
        'parent_id' => $parentComment->id,
        'content' => $longReply,
    ]);

    $commentAuthor->notify(new CommentReplyNotification($reply, $article));

    $notification = $commentAuthor->notifications->first();
    $data = $notification->data;

    expect(strlen($data['reply_preview']))->toBeLessThanOrEqual(100);
});

test('CommentReplyNotification uses database channel', function () {
    $notification = new CommentReplyNotification(
        Comment::factory()->create(),
        Article::factory()->create()
    );
    $user = User::factory()->create();

    expect($notification->via($user))->toBe(['database']);
});

// ===== NOTIFICATION SECURITY TESTS =====

test('notifications do not include sensitive user data', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleSubmittedForReviewNotification($article));

    $notification = $user->notifications->first();
    $data = $notification->data;

    expect(isset($data['password']))->toBeFalse();
    expect(isset($data['email']))->toBeFalse();
    expect(isset($data['token']))->toBeFalse();
});

test('user can only see their own notifications', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user1->id]);

    $user1->notify(new ArticleSubmittedForReviewNotification($article));

    expect($user1->notifications)->toHaveCount(1);
    expect($user2->notifications)->toHaveCount(0);
});
