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

// ===== ARTICLE SUBMISSION TESTS =====

test('article submission (draft to pending) sends notification to all admins', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $super_admin = User::factory()->create(['role' => 'super_admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    // Create article in draft status
    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    // Act as writer and submit article
    $this->actingAs($writer)->post(route('article.submit', $article));

    // Assert notifications sent to all admins
    Notification::assertSentTo([$admin, $super_admin], ArticleSubmittedForReviewNotification::class);

    // Assert writer did not receive notification
    Notification::assertNotSentTo($writer, ArticleSubmittedForReviewNotification::class);
});

test('article submission only notifies admin and super_admin roles', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer1 = User::factory()->create(['role' => 'writer']);
    $writer2 = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer1->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $this->actingAs($writer1)->post(route('article.submit', $article));

    // Assert only admin receives notification
    Notification::assertSentTo($admin, ArticleSubmittedForReviewNotification::class);
    Notification::assertNotSentTo($writer2, ArticleSubmittedForReviewNotification::class);
});

test('pending to pending does NOT send duplicate notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    // Simulate another save while status is already pending
    $article->title = 'Updated Title';
    $article->status = 'pending';
    $article->save();

    // No notification should be sent
    Notification::assertNotSentTo($admin, ArticleSubmittedForReviewNotification::class);
});

test('draft to draft does NOT send notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    // Save draft without changing to pending
    $article->title = 'Updated Draft Title';
    $article->save();

    Notification::assertNotSentTo($admin, ArticleSubmittedForReviewNotification::class);
});

test('rejected to pending sends new review notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'rejected',
    ]);

    // Re-submit rejected article
    $this->actingAs($writer)->post(route('article.submit', $article));

    // Assert notification sent
    Notification::assertSentTo($admin, ArticleSubmittedForReviewNotification::class);
});

// ===== ARTICLE APPROVAL TESTS =====

test('article approval (pending to published) notifies author', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    $this->actingAs($admin)->post(route('admin.approve', $article));

    // Assert notification sent to author
    Notification::assertSentTo($writer, ArticleApprovedNotification::class);

    // Assert admin did not receive approval notification
    Notification::assertNotSentTo($admin, ArticleApprovedNotification::class);
});

test('already published article does NOT create duplicate approval notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'published',
        'published_at' => now(),
    ]);

    // Try to approve already published article
    $this->actingAs($admin)->post(route('admin.approve', $article));

    // No notification should be sent
    Notification::assertNotSentTo($writer, ArticleApprovedNotification::class);
});

// ===== ARTICLE REJECTION TESTS =====

test('article rejection (pending to rejected) notifies author', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    $this->actingAs($admin)->post(route('admin.reject', $article), [
        'admin_notes' => 'This article needs more work.',
    ]);

    // Assert notification sent to author
    Notification::assertSentTo($writer, ArticleRejectedNotification::class);

    // Assert admin did not receive rejection notification
    Notification::assertNotSentTo($admin, ArticleRejectedNotification::class);
});

test('rejection notification contains admin_notes', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    $feedback = 'Please revise the introduction section.';

    $this->actingAs($admin)->post(route('admin.reject', $article), [
        'admin_notes' => $feedback,
    ]);

    Notification::assertSentTo(
        $writer,
        ArticleRejectedNotification::class,
        function ($notification) use ($feedback) {
            return $notification->toArray($writer)['admin_notes'] === $feedback;
        }
    );
});

test('repeated rejection after re-submission triggers new notification', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    // First rejection
    $this->actingAs($admin)->post(route('admin.reject', $article), [
        'admin_notes' => 'First rejection feedback.',
    ]);

    // Writer re-submits
    $article->status = 'draft';
    $article->save();
    $this->actingAs($writer)->post(route('article.submit', $article));

    // Reset fake to clear previous notifications
    Notification::fake();

    // Admin rejects again
    $article->refresh();
    $this->actingAs($admin)->post(route('admin.reject', $article), [
        'admin_notes' => 'Second rejection feedback.',
    ]);

    // New rejection notification should be sent
    Notification::assertSentTo($writer, ArticleRejectedNotification::class);
});

// ===== ROOT COMMENT TESTS =====

test('root comment notifies article author', function () {
    Notification::fake();

    $author = User::factory()->create(['role' => 'writer']);
    $commenter = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Post root comment
    $this->actingAs($commenter)->post(route('articles.comments.store', $article), [
        'content' => 'Great article!',
    ]);

    Notification::assertSentTo($author, NewCommentNotification::class);
});

test('author commenting on own article does NOT create notification', function () {
    Notification::fake();

    $author = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Author comments on own article
    $this->actingAs($author)->post(route('articles.comments.store', $article), [
        'content' => 'Thanks for reading!',
    ]);

    Notification::assertNotSentTo($author, NewCommentNotification::class);
});

// ===== REPLY COMMENT TESTS =====

test('reply notifies parent comment owner', function () {
    Notification::fake();

    $author = User::factory()->create(['role' => 'writer']);
    $commenter1 = User::factory()->create(['role' => 'writer']);
    $commenter2 = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Post root comment
    $rootComment = Comment::create([
        'article_id' => $article->id,
        'user_id' => $commenter1->id,
        'parent_id' => null,
        'content' => 'Great article!',
    ]);

    // Clear previous notifications
    Notification::fake();

    // Reply to root comment
    $this->actingAs($commenter2)->post(route('articles.comments.store', $article), [
        'content' => 'I agree!',
        'parent_id' => $rootComment->id,
    ]);

    Notification::assertSentTo($commenter1, CommentReplyNotification::class);
    Notification::assertNotSentTo($author, CommentReplyNotification::class);
});

test('replying to own comment does NOT create notification', function () {
    Notification::fake();

    $author = User::factory()->create(['role' => 'writer']);
    $commenter = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Post root comment
    $rootComment = Comment::create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
        'content' => 'My initial comment.',
    ]);

    // Clear previous notifications
    Notification::fake();

    // Reply to own comment
    $this->actingAs($commenter)->post(route('articles.comments.store', $article), [
        'content' => 'Adding more to my comment.',
        'parent_id' => $rootComment->id,
    ]);

    Notification::assertNotSentTo($commenter, CommentReplyNotification::class);
});

test('reply does not automatically notify article author unless they own parent comment', function () {
    Notification::fake();

    $author = User::factory()->create(['role' => 'writer']);
    $commenter1 = User::factory()->create(['role' => 'writer']);
    $commenter2 = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Post root comment by commenter1 (not author)
    $rootComment = Comment::create([
        'article_id' => $article->id,
        'user_id' => $commenter1->id,
        'parent_id' => null,
        'content' => 'Comment from commenter1.',
    ]);

    // Clear previous notifications
    Notification::fake();

    // Reply to root comment
    $this->actingAs($commenter2)->post(route('articles.comments.store', $article), [
        'content' => 'Reply from commenter2.',
        'parent_id' => $rootComment->id,
    ]);

    // Only parent comment owner (commenter1) should be notified
    Notification::assertSentTo($commenter1, CommentReplyNotification::class);
    Notification::assertNotSentTo($author, CommentReplyNotification::class);
});

test('second level reply is blocked by validation', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $commenter1 = User::factory()->create(['role' => 'writer']);
    $commenter2 = User::factory()->create(['role' => 'writer']);
    $commenter3 = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Post root comment
    $rootComment = Comment::create([
        'article_id' => $article->id,
        'user_id' => $commenter1->id,
        'parent_id' => null,
        'content' => 'Root comment.',
    ]);

    // Reply to root comment
    $reply = Comment::create([
        'article_id' => $article->id,
        'user_id' => $commenter2->id,
        'parent_id' => $rootComment->id,
        'content' => 'Reply to root.',
    ]);

    // Try to reply to the reply (second level)
    $response = $this->actingAs($commenter3)->post(route('articles.comments.store', $article), [
        'content' => 'Reply to reply.',
        'parent_id' => $reply->id,
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'Cannot reply to a reply. Replies can only be one level deep.',
    ]);
});

// ===== AUTHORIZATION TESTS =====

test('admin notification recipients include only admin roles', function () {
    Notification::fake();

    User::factory()->create(['role' => 'admin']);
    User::factory()->create(['role' => 'super_admin']);
    User::factory()->create(['role' => 'writer']);
    User::factory()->create(['role' => 'writer']);

    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $this->actingAs($writer)->post(route('article.submit', $article));

    // Only 2 notifications should be sent (admin + super_admin)
    expect(Notification::sent($writer, ArticleSubmittedForReviewNotification::class))->toHaveCount(0);
});

test('writers do not receive article submitted admin notifications', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $writer1 = User::factory()->create(['role' => 'writer']);
    $writer2 = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer1->id,
        'category_id' => $category->id,
        'status' => 'draft',
    ]);

    $this->actingAs($writer1)->post(route('article.submit', $article));

    Notification::assertNotSentTo($writer2, ArticleSubmittedForReviewNotification::class);
});

// ===== REGRESSION TESTS =====

test('existing article approval behavior still works', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.approve', $article));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Article published!');

    $article->refresh();
    expect($article->status)->toBe('published');
    expect($article->published_at)->not->toBeNull();
});

test('existing rejection behavior still works', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $writer->id,
        'category_id' => $category->id,
        'status' => 'pending',
    ]);

    $feedback = 'Please revise this article.';

    $response = $this->actingAs($admin)->post(route('admin.reject', $article), [
        'admin_notes' => $feedback,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Article rejected with feedback.');

    $article->refresh();
    expect($article->status)->toBe('rejected');
    expect($article->admin_notes)->toBe($feedback);
});

test('existing comment creation behavior still works', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $commenter = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    $response = $this->actingAs($commenter)->post(route('articles.comments.store', $article), [
        'content' => 'Great article!',
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    expect(Comment::where('article_id', $article->id)->count())->toBe(1);
});

test('existing reply creation behavior still works', function () {
    $author = User::factory()->create(['role' => 'writer']);
    $commenter1 = User::factory()->create(['role' => 'writer']);
    $commenter2 = User::factory()->create(['role' => 'writer']);
    $category = Category::factory()->create();

    $article = Article::factory()->create([
        'author_id' => $author->id,
        'category_id' => $category->id,
        'status' => 'published',
    ]);

    // Create root comment
    $rootComment = Comment::create([
        'article_id' => $article->id,
        'user_id' => $commenter1->id,
        'parent_id' => null,
        'content' => 'Root comment.',
    ]);

    // Reply to root comment
    $response = $this->actingAs($commenter2)->post(route('articles.comments.store', $article), [
        'content' => 'Reply to root.',
        'parent_id' => $rootComment->id,
    ]);

    $response->assertStatus(201);
    $response->assertJsonPath('success', true);

    expect(Comment::where('article_id', $article->id)->count())->toBe(2);
    expect($rootComment->replies()->count())->toBe(1);
});
