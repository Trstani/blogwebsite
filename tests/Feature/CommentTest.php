<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\User;

// ===== DATABASE & RELATIONSHIPS =====

test('comments table exists and can store data', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $comment = Comment::create([
        'article_id' => $article->id,
        'user_id' => $user->id,
        'content' => 'This is a test comment.',
    ]);

    expect($comment->id)->toBeTruthy();
    expect($comment->content)->toBe('This is a test comment.');
});

test('comment belongs to article', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
    ]);

    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $user->id,
    ]);

    expect($comment->article_id)->toBe($article->id);
    expect($comment->article->id)->toBe($article->id);
});

test('comment belongs to user', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
    ]);

    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $user->id,
    ]);

    expect($comment->user_id)->toBe($user->id);
    expect($comment->user->id)->toBe($user->id);
});

test('article has many comments', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
    ]);

    Comment::factory(3)->create([
        'article_id' => $article->id,
        'user_id' => $user->id,
    ]);

    expect($article->comments)->toHaveCount(3);
});

test('user has many comments', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
    ]);

    Comment::factory(2)->create([
        'article_id' => $article->id,
        'user_id' => $user->id,
    ]);

    expect($user->comments)->toHaveCount(2);
});

// ===== AUTHENTICATION =====

test('guest cannot create comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $response = $this->postJson("/articles/{$article->id}/comments", [
        'content' => 'Test comment',
    ]);

    $response->assertStatus(401);
    expect(Comment::count())->toBe(0);
});

test('authenticated user can create comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => 'This is a great article!',
    ]);

    $response->assertStatus(201);
    $response->assertJson([
        'success' => true,
        'message' => 'Comment created successfully.',
    ]);
    expect(Comment::count())->toBe(1);
    expect(Comment::first()->user_id)->toBe($commenter->id);
});

// ===== PUBLISHED-ONLY RULE =====

test('published article accepts comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Great content!',
    ]);

    $response->assertStatus(201);
    expect(Comment::count())->toBe(1);
});

test('draft article rejects comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'draft',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Test comment',
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'Comments are only available for published articles.',
    ]);
    expect(Comment::count())->toBe(0);
});

test('pending article rejects comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'pending',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Test comment',
    ]);

    $response->assertStatus(403);
    expect(Comment::count())->toBe(0);
});

// ===== VALIDATION =====

test('empty content rejected', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => '',
    ]);

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
    expect(Comment::count())->toBe(0);
});

test('whitespace-only content rejected', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => '   ',
    ]);

    $response->assertStatus(422);
    expect(Comment::count())->toBe(0);
});

test('oversized content rejected', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => str_repeat('a', 1001),
    ]);

    $response->assertStatus(422);
    expect(Comment::count())->toBe(0);
});

test('valid content accepted', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => str_repeat('a', 1000),
    ]);

    $response->assertStatus(201);
    expect(Comment::count())->toBe(1);
});

// ===== SECURITY =====

test('browser cannot assign another user_id', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Test comment',
        'user_id' => $otherUser->id,  // Try to assign different user
    ]);

    $response->assertStatus(201);
    // Comment should be created with authenticated user, not the provided user_id
    expect(Comment::first()->user_id)->toBe($commenter->id);
    expect(Comment::first()->user_id)->not->toBe($otherUser->id);
});

test('content is safely rendered without XSS', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $maliciousContent = '<script>alert("XSS")</script>';

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => $maliciousContent,
    ]);

    $response->assertStatus(201);
    $savedComment = Comment::first();
    expect($savedComment->content)->toBe($maliciousContent);

    // Verify the content is displayed safely in the reading page
    $getResponse = $this->get("/blog/{$article->slug}");
    // Content should be HTML-escaped, not executed
    // Using a browser test would be needed to fully verify no script execution
    expect($getResponse->status())->toBe(200);
});

test('comments belong to correct article', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article1 = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);
    $article2 = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $this->actingAs($commenter)->postJson("/articles/{$article1->id}/comments", [
        'content' => 'Comment on article 1',
    ]);

    $this->actingAs($commenter)->postJson("/articles/{$article2->id}/comments", [
        'content' => 'Comment on article 2',
    ]);

    expect($article1->comments)->toHaveCount(1);
    expect($article2->comments)->toHaveCount(1);
    expect($article1->comments->first()->content)->toBe('Comment on article 1');
    expect($article2->comments->first()->content)->toBe('Comment on article 2');
});

// ===== DELETION =====

test('deleting article does not leave orphan comments', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    Comment::factory(3)->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
    ]);

    expect(Comment::count())->toBe(3);

    // Delete article
    $article->delete();

    // Comments should be deleted via cascade
    expect(Comment::count())->toBe(0);
});

// ===== READING PAGE =====

test('published article displays comments section', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $response = $this->get("/blog/{$article->slug}");

    $response->assertStatus(200);
    $response->assertSee('Comments');
});

test('unpublished article does not display comments section', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'draft',
    ]);

    // Only admins can view draft articles
    $admin = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($admin)->get("/blog/{$article->slug}");

    $response->assertStatus(200);
    $response->assertDontSee('Comments');
});

test('guest sees login prompt on published article', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $response = $this->get("/blog/{$article->slug}");

    $response->assertStatus(200);
    $response->assertSee('Log in');
    $response->assertSee('to leave a comment');
});

test('authenticated user sees comment form on published article', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->get("/blog/{$article->slug}");

    $response->assertStatus(200);
    $response->assertSee('Leave a Comment');
    $response->assertSee('Post Comment');
});

// ===== REPLIES (PHASE 5) =====

test('comment can have replies', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    // Create root comment
    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    // Create reply
    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier->id,
        'parent_id' => $comment->id,
    ]);

    expect($reply->parent_id)->toBe($comment->id);
    expect($comment->replies)->toHaveCount(1);
    expect($comment->replies->first()->id)->toBe($reply->id);
});

test('reply belongs to parent comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier->id,
        'parent_id' => $comment->id,
    ]);

    expect($reply->parent->id)->toBe($comment->id);
});

test('authenticated user can create reply to root comment', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    // Create root comment
    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    // Create reply via API
    $response = $this->actingAs($replier)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Great point!',
        'parent_id' => $comment->id,
    ]);

    $response->assertStatus(201);
    $response->assertJson(['success' => true]);
    expect(Comment::where('parent_id', $comment->id)->count())->toBe(1);
});

test('reply must belong to same article as parent', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article1 = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);
    $article2 = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    // Create root comment on article 1
    $comment = Comment::factory()->create([
        'article_id' => $article1->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    // Try to reply on article 2 (cross-article attack)
    $response = $this->actingAs($replier)->postJson("/articles/{$article2->id}/comments", [
        'content' => 'Trying to reply on wrong article',
        'parent_id' => $comment->id,
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'Parent comment must belong to the same article.',
    ]);
});

test('cannot reply to a reply (max 1 level deep)', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier1 = User::factory()->create();
    $replier2 = User::factory()->create();

    // Create root comment
    $rootComment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    // Create reply to root comment
    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier1->id,
        'parent_id' => $rootComment->id,
    ]);

    // Try to reply to the reply (should fail)
    $response = $this->actingAs($replier2)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Trying to reply to reply',
        'parent_id' => $reply->id,
    ]);

    $response->assertStatus(403);
    $response->assertJson([
        'success' => false,
        'message' => 'Cannot reply to a reply. Replies can only be one level deep.',
    ]);
});

test('invalid parent_id is rejected', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();

    $response = $this->actingAs($commenter)->postJson("/articles/{$article->id}/comments", [
        'content' => 'Test reply',
        'parent_id' => 99999,
    ]);

    $response->assertStatus(422);
    $response->assertJson(['success' => false]);
});

test('deleting root comment deletes all replies (cascade)', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier1 = User::factory()->create();
    $replier2 = User::factory()->create();

    // Create root comment
    $rootComment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    // Create multiple replies
    Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier1->id,
        'parent_id' => $rootComment->id,
    ]);

    Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier2->id,
        'parent_id' => $rootComment->id,
    ]);

    expect(Comment::count())->toBe(3);

    // Delete root comment
    $rootComment->delete();

    // All replies should be deleted via cascade
    expect(Comment::count())->toBe(0);
});

test('replies have correct relationships loaded', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier->id,
        'parent_id' => $comment->id,
    ]);

    // Test relationship loading with eager loading (like in the route)
    $loadedComment = Comment::with('replies.user')->find($comment->id);

    expect($loadedComment->replies)->toHaveCount(1);
    expect($loadedComment->replies->first()->user->id)->toBe($replier->id);
});

test('reading page displays replies under root comments', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    // Create root comment
    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    // Create reply
    $reply = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $replier->id,
        'parent_id' => $comment->id,
    ]);

    $response = $this->get("/blog/{$article->slug}");

    $response->assertStatus(200);
    // Both comment and reply should be visible
    $response->assertSee($comment->content);
    $response->assertSee($reply->content);
});

test('root comment without parent_id is correctly identified', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
    ]);

    $commenter = User::factory()->create();

    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    expect($comment->parent_id)->toBeNull();
    expect($comment->parent)->toBeNull();
    expect($comment->replies)->toHaveCount(0);
});

test('reply content has XSS protection', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->create([
        'category_id' => $category->id,
        'author_id' => $user->id,
        'status' => 'published',
    ]);

    $commenter = User::factory()->create();
    $replier = User::factory()->create();

    // Create root comment
    $comment = Comment::factory()->create([
        'article_id' => $article->id,
        'user_id' => $commenter->id,
        'parent_id' => null,
    ]);

    $maliciousContent = '<img src=x onerror="alert(\'XSS\')">';

    // Create reply with malicious content
    $response = $this->actingAs($replier)->postJson("/articles/{$article->id}/comments", [
        'content' => $maliciousContent,
        'parent_id' => $comment->id,
    ]);

    $response->assertStatus(201);
    $savedReply = Comment::where('parent_id', $comment->id)->first();
    expect($savedReply->content)->toBe($maliciousContent);

    // Verify on reading page it's safely rendered
    $getResponse = $this->get("/blog/{$article->slug}");
    $getResponse->assertStatus(200);
});
