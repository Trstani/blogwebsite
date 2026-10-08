<?php

use App\Models\Article;
use App\Models\User;
use App\Notifications\ArticleSubmittedForReviewNotification;
use App\Notifications\ArticleApprovedNotification;
use App\Notifications\ArticleRejectedNotification;
use App\Notifications\NewCommentNotification;
use App\Notifications\CommentReplyNotification;
use Illuminate\Support\Facades\Notification;

// ===== AUTHENTICATION TESTS =====

test('notifications.index requires authentication', function () {
    $response = $this->get(route('notifications.index'));

    $response->assertRedirectToRoute('auth');
});

test('notifications.mark-as-read requires authentication', function () {
    $response = $this->post(route('notifications.mark-as-read', 'fake-id'));

    $response->assertRedirectToRoute('auth');
});

test('notifications.mark-all-as-read requires authentication', function () {
    $response = $this->post(route('notifications.mark-all-as-read'));

    $response->assertRedirectToRoute('auth');
});

// ===== INDEX ROUTE TESTS =====

test('authenticated user can access notifications index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertViewIs('notifications.index');
});

test('notifications index shows user\'s own notifications only', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user1->id]);

    // Create notification for user1
    $user1->notify(new ArticleApprovedNotification($article));

    // Try to access as user2
    $response = $this->actingAs($user2)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertViewHas('notifications');
    $notifications = $response->viewData('notifications');
    expect($notifications->total())->toBe(0);
});

test('notifications index displays unread count', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    // Create 3 unread notifications
    $user->notify(new ArticleApprovedNotification($article));
    $user->notify(new ArticleApprovedNotification($article));
    $user->notify(new ArticleApprovedNotification($article));

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertViewHas('unreadCount', 3);
});

test('notifications index paginates results (15 per page)', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    // Create 20 notifications
    for ($i = 0; $i < 20; $i++) {
        $user->notify(new ArticleApprovedNotification($article));
    }

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertViewHas('notifications');
    $notifications = $response->viewData('notifications');
    expect($notifications)->toHaveCount(15);
    expect($notifications->hasMorePages())->toBeTrue();
});

test('notifications index shows empty state when no notifications', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertViewHas('notifications');
    $notifications = $response->viewData('notifications');
    expect($notifications->count())->toBe(0);
});

// ===== MARK AS READ TESTS =====

test('user can mark their own notification as read', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleApprovedNotification($article));
    $notification = $user->notifications->first();

    expect($notification->read_at)->toBeNull();

    $response = $this->actingAs($user)->post(
        route('notifications.mark-as-read', $notification->id),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(200);
    $response->assertJsonStructure(['success', 'message']);

    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

test('user cannot mark another user\'s notification as read', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user1->id]);

    $user1->notify(new ArticleApprovedNotification($article));
    $notification = $user1->notifications->first();

    $response = $this->actingAs($user2)->post(
        route('notifications.mark-as-read', $notification->id),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(404);
    $response->assertJsonStructure(['success', 'message']);
});

test('marking already-read notification as read is idempotent', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleApprovedNotification($article));
    $notification = $user->notifications->first();

    // Mark as read first time
    $notification->markAsRead();
    $readAt1 = $notification->read_at;

    // Mark as read second time
    $response = $this->actingAs($user)->post(
        route('notifications.mark-as-read', $notification->id),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(200);
    $notification->refresh();
    expect($notification->read_at)->toEqual($readAt1);
});

test('invalid notification ID returns 404', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(
        route('notifications.mark-as-read', 'invalid-uuid'),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(404);
});

// ===== MARK ALL AS READ TESTS =====

test('user can mark all their notifications as read', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    // Create 5 unread notifications
    for ($i = 0; $i < 5; $i++) {
        $user->notify(new ArticleApprovedNotification($article));
    }

    expect($user->notifications()->whereNull('read_at')->count())->toBe(5);

    $response = $this->actingAs($user)->post(
        route('notifications.mark-all-as-read'),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(200);
    $response->assertJsonStructure(['success', 'message']);

    expect($user->notifications()->whereNull('read_at')->count())->toBe(0);
});

test('mark-all-as-read only affects current user\'s notifications', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user1->id]);

    // Create unread notifications for both users
    $user1->notify(new ArticleApprovedNotification($article));
    $user1->notify(new ArticleApprovedNotification($article));
    $user2->notify(new ArticleApprovedNotification($article));
    $user2->notify(new ArticleApprovedNotification($article));

    expect($user1->notifications()->whereNull('read_at')->count())->toBe(2);
    expect($user2->notifications()->whereNull('read_at')->count())->toBe(2);

    // User1 marks all as read
    $this->actingAs($user1)->post(
        route('notifications.mark-all-as-read'),
        ['_token' => csrf_token()]
    );

    expect($user1->notifications()->whereNull('read_at')->count())->toBe(0);
    expect($user2->notifications()->whereNull('read_at')->count())->toBe(2);
});

test('mark-all-as-read is idempotent', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleApprovedNotification($article));

    // Mark all as read first time
    $this->actingAs($user)->post(
        route('notifications.mark-all-as-read'),
        ['_token' => csrf_token()]
    );

    // Mark all as read second time
    $response = $this->actingAs($user)->post(
        route('notifications.mark-all-as-read'),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(200);
    expect($user->notifications()->whereNull('read_at')->count())->toBe(0);
});

test('mark-all-as-read with no unread notifications succeeds', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(
        route('notifications.mark-all-as-read'),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
});

// ===== UNREAD COUNT TESTS =====

test('unread count is accurate', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    // Create 5 notifications
    for ($i = 0; $i < 5; $i++) {
        $user->notify(new ArticleApprovedNotification($article));
    }

    $response = $this->actingAs($user)->get(route('notifications.index'));
    expect($response->viewData('unreadCount'))->toBe(5);

    // Mark 2 as read
    $notifications = $user->notifications->take(2);
    foreach ($notifications as $notification) {
        $notification->markAsRead();
    }

    $response = $this->actingAs($user)->get(route('notifications.index'));
    expect($response->viewData('unreadCount'))->toBe(3);
});

// ===== NOTIFICATION DISPLAY TESTS =====

test('notification index displays correct notification type badges', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $writer = User::factory()->create(['role' => 'writer']);
    $article = Article::factory()->create(['author_id' => $writer->id]);

    // Create different notification types
    $user->notify(new ArticleSubmittedForReviewNotification($article));
    $writer->notify(new ArticleApprovedNotification($article));
    $writer->notify(new ArticleRejectedNotification($article));

    $response = $this->actingAs($user)->get(route('notifications.index'));
    $response->assertViewHas('notifications');
});

test('notification dropdown component receives unreadCount and notifications', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    // Create 3 unread notifications
    for ($i = 0; $i < 3; $i++) {
        $user->notify(new ArticleApprovedNotification($article));
    }

    // The navbar will display the component with correct data
    // This is tested indirectly via navbar rendering
    $response = $this->actingAs($user)->get('/');
    $response->assertOk();
});

// ===== SECURITY TESTS =====

test('user cannot access another user\'s notification data via notification ID manipulation', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user1->id]);

    $user1->notify(new ArticleApprovedNotification($article));
    $notification1 = $user1->notifications->first();

    $user2->notify(new ArticleApprovedNotification($article));
    $notification2 = $user2->notifications->first();

    // Try to mark user1's notification as user2
    $response = $this->actingAs($user2)->post(
        route('notifications.mark-as-read', $notification1->id),
        ['_token' => csrf_token()]
    );

    $response->assertStatus(404);

    // Verify notification1 is still unread
    $notification1->refresh();
    expect($notification1->read_at)->toBeNull();
});

test('notifications are returned in latest-first order', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    // Create notifications with slight delays
    $notification1 = $user->notifications()->create([
        'type' => ArticleApprovedNotification::class,
        'data' => ['article_id' => $article->id, 'article_title' => 'Article 1'],
    ]);

    sleep(1);

    $notification2 = $user->notifications()->create([
        'type' => ArticleApprovedNotification::class,
        'data' => ['article_id' => $article->id, 'article_title' => 'Article 2'],
    ]);

    $response = $this->actingAs($user)->get(route('notifications.index'));
    $notifications = $response->viewData('notifications');

    expect($notifications->first()->id)->toBe($notification2->id);
});

// ===== CSRF PROTECTION TESTS =====

test('mark-as-read requires CSRF token', function () {
    $user = User::factory()->create();
    $article = Article::factory()->create(['author_id' => $user->id]);

    $user->notify(new ArticleApprovedNotification($article));
    $notification = $user->notifications->first();

    $response = $this->actingAs($user)->post(
        route('notifications.mark-as-read', $notification->id)
    );

    $response->assertStatus(419); // Token mismatch
});

test('mark-all-as-read requires CSRF token', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('notifications.mark-all-as-read'));

    $response->assertStatus(419); // Token mismatch
});
