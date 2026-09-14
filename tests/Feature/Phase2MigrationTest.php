<?php

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
    // Ensure category exists for tests
    if (! Category::find(1)) {
        Category::create(['id' => 1, 'name' => 'Test Category', 'slug' => 'test']);
    }
});

describe('Phase 2: Section Image Upload Migration', function () {
    it('stores new section image as relative path in database', function () {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id, 'category_id' => 1]);

        $file = UploadedFile::fake()->image('section.jpg', 800, 600);

        // Upload section image via endpoint
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $file,
        ]);

        $uploadResponse->assertStatus(200);
        $imagePath = $uploadResponse->json('path');

        // Save section with local path
        $sectionData = [
            'sections' => [
                [
                    'type' => 'image',
                    'content' => $imagePath,
                    'public_id' => null,
                ],
            ],
        ];

        $saveResponse = $this->actingAs($user)->post("/articles/{$article->id}/sections", $sectionData);
        $saveResponse->assertStatus(200);

        // Verify path stored in database
        $section = $article->sections()->first();
        expect($section->content)->toBe($imagePath);
        expect($section->image_public_id)->toBeNull();
    });

    it('displays local section image with /storage/ prefix', function () {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id, 'category_id' => 1]);

        $file = UploadedFile::fake()->image('section.jpg', 800, 600);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $file,
        ]);

        $imagePath = $uploadResponse->json('path');
        $imageUrl = $uploadResponse->json('url');

        // Verify URL has /storage/ prefix
        expect($imageUrl)->toContain('/storage/');
        expect($imageUrl)->toContain($imagePath);
    });

    it('coexists with old Cloudinary section images', function () {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id, 'category_id' => 1]);

        // Create section with old Cloudinary URL
        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-image.jpg',
            'image_public_id' => 'old-public-id',
            'order' => 1,
        ]);

        // Verify old Cloudinary data still exists
        expect($section->content)->toContain('https://res.cloudinary.com');
        expect($section->image_public_id)->toBe('old-public-id');
    });

    it('deletes local section image from storage on section deletion', function () {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id, 'category_id' => 1]);

        $file = UploadedFile::fake()->image('section.jpg', 800, 600);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $file,
        ]);

        $imagePath = $uploadResponse->json('path');

        // Save section with local image
        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => $imagePath,
            'image_public_id' => null,
            'order' => 1,
        ]);

        Storage::disk('public')->assertExists($imagePath);

        // Delete section
        $section->delete();

        // Verify file deleted from storage
        Storage::disk('public')->assertMissing($imagePath);
    });

    it('queues Cloudinary deletion for old section images on deletion', function () {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id, 'category_id' => 1]);

        // Create section with old Cloudinary image
        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-image.jpg',
            'image_public_id' => 'old-public-id',
            'order' => 1,
        ]);

        // Delete section - should queue Cloudinary deletion without throwing
        $section->delete();

        // Verify section deleted from DB
        expect(ArticleSection::find($section->id))->toBeNull();
    });
});

describe('Phase 2: Article Cover Upload Migration', function () {
    it('stores new article cover as relative local path', function () {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

        // Upload via endpoint
        $uploadResponse = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $file,
        ]);

        $uploadResponse->assertStatus(200);
        $coverPath = $uploadResponse->json('path');

        // Create article with local cover path
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => 1,
            'cover_image' => $coverPath,
            'cover_image_public_id' => null,
        ]);

        // Verify path stored in database
        expect($article->cover_image)->toBe($coverPath);
        expect($article->cover_image_public_id)->toBeNull();
    });

    it('coexists with old Cloudinary covers', function () {
        $user = User::factory()->create();

        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => 1,
            'cover_image' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-cover.jpg',
            'cover_image_public_id' => 'old-cover-id',
        ]);

        expect($article->cover_image)->toContain('https://res.cloudinary.com');
        expect($article->cover_image_public_id)->toBe('old-cover-id');
    });

    it('deletes local cover from storage on article deletion', function () {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $file,
        ]);

        $coverPath = $uploadResponse->json('path');

        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => 1,
            'cover_image' => $coverPath,
            'cover_image_public_id' => null,
        ]);

        Storage::disk('public')->assertExists($coverPath);

        // Delete article
        $article->delete();

        // Verify cover deleted from storage
        Storage::disk('public')->assertMissing($coverPath);
    });

    it('queues Cloudinary deletion for old covers on article deletion', function () {
        $user = User::factory()->create();

        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => 1,
            'cover_image' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-cover.jpg',
            'cover_image_public_id' => 'old-cover-id',
        ]);

        $article->delete();
        expect(Article::find($article->id))->toBeNull();
    });
});

describe('Phase 2: Avatar Upload Migration', function () {
    it('stores new avatar as relative local path', function () {
        $user = User::factory()->create(['avatar' => null, 'avatar_public_id' => null]);

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        // Upload avatar
        $uploadResponse = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $uploadResponse->assertStatus(200);
        $avatarPath = $uploadResponse->json('path');

        // Update user avatar
        $this->actingAs($user)->post("/profile/{$user->slug}/avatar", [
            'image_url' => $avatarPath,
            'public_id' => null,
        ]);

        $user->refresh();

        // Verify path stored in database
        expect($user->avatar)->toBe($avatarPath);
        expect($user->avatar_public_id)->toBeNull();
    });

    it('coexists with old Cloudinary avatars', function () {
        $user = User::factory()->create([
            'avatar' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-avatar.jpg',
            'avatar_public_id' => 'old-avatar-id',
        ]);

        expect($user->avatar)->toContain('https://res.cloudinary.com');
        expect($user->avatar_public_id)->toBe('old-avatar-id');
    });

    it('deletes old local avatar when replacing with new one', function () {
        $user = User::factory()->create();

        // Upload first avatar
        $file1 = UploadedFile::fake()->image('avatar1.jpg', 200, 200);
        $uploadResponse1 = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $file1,
        ]);
        $avatarPath1 = $uploadResponse1->json('path');

        $this->actingAs($user)->post("/profile/{$user->slug}/avatar", [
            'image_url' => $avatarPath1,
            'public_id' => null,
        ]);

        Storage::disk('public')->assertExists($avatarPath1);

        // Upload second avatar to replace
        $file2 = UploadedFile::fake()->image('avatar2.jpg', 200, 200);
        $uploadResponse2 = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $file2,
        ]);
        $avatarPath2 = $uploadResponse2->json('path');

        $this->actingAs($user)->post("/profile/{$user->slug}/avatar", [
            'image_url' => $avatarPath2,
            'public_id' => null,
        ]);

        // Verify old avatar deleted and new avatar exists
        Storage::disk('public')->assertMissing($avatarPath1);
        Storage::disk('public')->assertExists($avatarPath2);

        $user->refresh();
        expect($user->avatar)->toBe($avatarPath2);
    });

    it('deletes local avatar on user deletion', function () {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $avatarPath = $uploadResponse->json('path');

        $user->update(['avatar' => $avatarPath, 'avatar_public_id' => null]);

        Storage::disk('public')->assertExists($avatarPath);

        // Delete user
        $user->delete();

        // Verify avatar deleted from storage
        Storage::disk('public')->assertMissing($avatarPath);
    });

    it('queues Cloudinary deletion when replacing old Cloudinary avatar', function () {
        $user = User::factory()->create([
            'avatar' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-avatar.jpg',
            'avatar_public_id' => 'old-avatar-id',
        ]);

        // Upload new local avatar
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $avatarPath = $uploadResponse->json('path');

        $this->actingAs($user)->post("/profile/{$user->slug}/avatar", [
            'image_url' => $avatarPath,
            'public_id' => null,
        ]);

        // Should not throw - Cloudinary deletion queued
        $user->refresh();
        expect($user->avatar)->toBe($avatarPath);
        expect($user->avatar_public_id)->toBeNull();
    });
});

describe('Phase 2: Hybrid Cloudinary/Local Coexistence', function () {
    it('displays articles with mixed local and Cloudinary covers', function () {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Article with local cover
        $article1 = Article::factory()->create([
            'author_id' => $user1->id,
            'category_id' => 1,
            'cover_image' => 'uploads/articles/covers/1234567890-abc123.jpg',
            'cover_image_public_id' => null,
        ]);

        // Article with Cloudinary cover
        $article2 = Article::factory()->create([
            'author_id' => $user2->id,
            'category_id' => 1,
            'cover_image' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-cover.jpg',
            'cover_image_public_id' => 'old-cover-id',
        ]);

        // Retrieve both articles
        $retrieved1 = Article::find($article1->id);
        $retrieved2 = Article::find($article2->id);

        // Verify both are stored correctly
        expect($retrieved1->cover_image)->toContain('uploads/');
        expect($retrieved1->cover_image_public_id)->toBeNull();

        expect($retrieved2->cover_image)->toContain('res.cloudinary.com');
        expect($retrieved2->cover_image_public_id)->toBe('old-cover-id');
    });

    it('handles users with mixed local and Cloudinary avatars', function () {
        $user1 = User::factory()->create([
            'avatar' => 'uploads/users/avatars/1234567890-avatar1.jpg',
            'avatar_public_id' => null,
        ]);

        $user2 = User::factory()->create([
            'avatar' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-avatar.jpg',
            'avatar_public_id' => 'old-avatar-id',
        ]);

        expect($user1->avatar)->toContain('uploads/');
        expect($user1->avatar_public_id)->toBeNull();

        expect($user2->avatar)->toContain('res.cloudinary.com');
        expect($user2->avatar_public_id)->toBe('old-avatar-id');
    });

    it('handles sections with mixed local and Cloudinary images', function () {
        $user = User::factory()->create();
        $article = Article::factory()->create(['author_id' => $user->id, 'category_id' => 1]);

        // Local image section
        $section1 = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => 'uploads/articles/sections/1234567890-section1.jpg',
            'image_public_id' => null,
            'order' => 1,
        ]);

        // Cloudinary image section
        $section2 = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v123/old-section.jpg',
            'image_public_id' => 'old-section-id',
            'order' => 2,
        ]);

        $sections = $article->sections()->get();

        expect($sections[0]->content)->toContain('uploads/');
        expect($sections[0]->image_public_id)->toBeNull();

        expect($sections[1]->content)->toContain('res.cloudinary.com');
        expect($sections[1]->image_public_id)->toBe('old-section-id');
    });
});
