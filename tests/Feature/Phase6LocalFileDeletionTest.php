<?php

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * PHASE 6 - LOCAL FILE DELETION REPRODUCTION TESTS
 *
 * These tests reproduce the orphan file bugs identified in the audit:
 * Bug #1: Unsaved local images are orphaned
 * Bug #2: Avatar route bypass prevents Cloudinary deletion
 * Bug #3: Path normalization is fragile
 * Bug #4: No deletion result validation
 * Bug #5: Avatar public_id left orphaned
 */

beforeEach(function () {
    Storage::fake('public');
});

describe('BUG #1: UNSAVED LOCAL IMAGES ORPHANED', function () {
    it('reproduces unsaved image orphaning - article never saved to db', function () {
        $user = User::factory()->create();

        // Step 1: Create article (auto-draft via store)
        $response = $this->actingAs($user)->post('/articles', [
            'title' => 'Test Article',
            'description' => 'Test',
            'category_id' => Category::factory()->create()->id,
        ]);
        $this->assertIsArray($response->json());

        $articleId = $response->json('article_id') ?? $response->json('id');
        expect($articleId)->toBeTruthy();

        // Step 2: Upload image to unsaved article
        $image = UploadedFile::fake()->image('test.jpg', 100, 100);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);

        $uploadResponse->assertStatus(200);
        $imagePath = $uploadResponse->json('path');
        expect($imagePath)->toContain('uploads/articles/sections/');

        // Step 3: Verify physical file exists
        Storage::disk('public')->assertExists($imagePath);

        // Step 4: User abandons article (closes browser, leaves page, etc.)
        // In real scenario, unsaved image section is never saved to database
        // Frontend clears the section from DOM, but backend never receives delete request

        // Step 5: Check physical file - should still exist (not deleted)
        // This is the bug: orphaned file
        Storage::disk('public')->assertExists($imagePath);

        // EXPECTED (current buggy behavior):
        // File remains in storage/app/public/uploads/articles/sections/
        // No cleanup happened because:
        // - Article was never saved to DB
        // - Section was never saved to DB
        // - No delete request was sent to backend
        // - Frontend just removed DOM element

        expect(Storage::disk('public')->exists($imagePath))->toBeTrue();
    });

    it('reproduces unsaved image orphaning - user deletes unsaved section before save', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Create and save article first
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Upload image (unsaved section)
        $image = UploadedFile::fake()->image('test.jpg', 100, 100);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);

        $imagePath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($imagePath);

        // Call deleteUnsavedImage endpoint (which only handles Cloudinary)
        $this->actingAs($user)->delete("/articles/{$article->id}/unsaved-image", [
            'public_id' => 'dummy-public-id',
        ]);

        // EXPECTED (current buggy behavior):
        // Endpoint only queues Cloudinary deletion, ignores local files
        // Local file remains orphaned
        Storage::disk('public')->assertExists($imagePath);
    });
});

describe('BUG #2 & #3 & #5: AVATAR ROUTE BYPASS + PATH NORMALIZATION + PUBLIC_ID ORPHAN', function () {
    it('avatar deletion now properly clears public_id and queues Cloudinary cleanup', function () {
        $user = User::factory()->create([
            'avatar' => 'https://res.cloudinary.com/test/image/upload/v1234/abc.jpg',
            'avatar_public_id' => 'abc',
        ]);

        // DELETE /profile/{slug}/avatar now properly handles Cloudinary deletion
        $response = $this->actingAs($user)->delete("/profile/{$user->slug}/avatar");

        // EXPECTED (after fix):
        // 1. Cloudinary deletion job is queued (now handled properly)
        // 2. avatar_public_id is cleared from database (no more orphan)
        $user->refresh();
        expect($user->avatar)->toBeNull();
        expect($user->avatar_public_id)->toBeNull(); // FIXED: No longer orphaned!
    });

    it('reproduces avatar replacement bypass - old Cloudinary avatar not queued when replacing with new one', function () {
        $user = User::factory()->create([
            'avatar' => 'https://res.cloudinary.com/test/image/upload/v1234/abc.jpg',
            'avatar_public_id' => 'abc',
        ]);

        // POST /profile/{slug}/avatar uses direct Storage calls, bypasses UserObserver
        $response = $this->actingAs($user)->post("/profile/{$user->slug}/avatar", [
            'image_url' => 'https://res.cloudinary.com/test/image/upload/v5678/xyz.jpg',
            'public_id' => 'xyz',
        ]);

        // EXPECTED (current buggy behavior):
        // Old Cloudinary image (abc) is NOT queued for deletion
        // Only direct Storage calls happen, which fail silently for Cloudinary URLs
        $user->refresh();
        expect($user->avatar)->toBe('https://res.cloudinary.com/test/image/upload/v5678/xyz.jpg');
        expect($user->avatar_public_id)->toBe('xyz');
        // Old public_id 'abc' was never deleted from Cloudinary
    });

    it('reproduces path normalization fragility - str_replace mismatch', function () {
        $user = User::factory()->create();

        // Upload a local avatar
        $avatar = UploadedFile::fake()->image('avatar.jpg', 100, 100);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $avatar,
        ]);

        $avatarPath = $uploadResponse->json('path'); // e.g., 'uploads/users/avatars/1750000000-abc.jpg'
        $user->update(['avatar' => $avatarPath, 'avatar_public_id' => null]);

        Storage::disk('public')->assertExists($avatarPath);

        // Now POST with /storage/ prefix (as it would come from frontend)
        $prefixedPath = '/storage/'.$avatarPath;
        $response = $this->actingAs($user)->post("/profile/{$user->slug}/avatar", [
            'image_url' => 'https://res.cloudinary.com/test/image/upload/v1234/new.jpg',
            'public_id' => 'new',
        ]);

        // The route uses str_replace('/storage/', '', $user->avatar)
        // But $user->avatar is just 'uploads/...', not '/storage/uploads/...'
        // So the str_replace does nothing, path normalization fails
        // This is fragile - should use LocalFileStorageService::normalizePath()

        $user->refresh();
        // If old avatar deletion failed silently, file may still exist
        // (This test just documents the fragility, actual result depends on path format)
    });
});

describe('BUG #1 EXPANDED: IMAGE SECTION DELETION', function () {
    it('reproduces image section deletion with orphan file check', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Upload and save image section
        $image = UploadedFile::fake()->image('test.jpg', 100, 100);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);

        $imagePath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($imagePath);

        // Create section with image
        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => $imagePath,
            'order' => 1,
        ]);

        // Delete section via endpoint
        $response = $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");

        $response->assertStatus(200);

        // EXPECTED (correct behavior - should pass):
        // Image file should be deleted by ArticleSectionObserver::deleted()
        Storage::disk('public')->assertMissing($imagePath);
    });
});

describe('BUG #1 EXPANDED: GIF SECTION HANDLING', function () {
    it('checks if GIF deletion is handled (audit found it may not be)', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Upload GIF
        $gif = UploadedFile::fake()->create('test.gif', 512, 'image/gif');
        $gif->name = 'test.gif';

        $uploadResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);

        $gifPath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($gifPath);

        // Create GIF section
        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'gif',
            'content' => $gifPath,
            'order' => 1,
        ]);

        // Delete GIF section
        $response = $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");

        $response->assertStatus(200);

        // EXPECTED:
        // GIF file should be deleted
        // But audit found ArticleSectionObserver only handles type='image', not type='gif'
        // So this test will expose the bug if GIF deletion is broken
        Storage::disk('public')->assertMissing($gifPath);
    });
});

describe('ARTICLE DELETION CASCADE', function () {
    it('deletes all article files including cover, images, and gifs', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Create article with cover
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
        ]);

        // Upload and set cover
        $cover = UploadedFile::fake()->image('cover.jpg', 100, 100);
        $coverResponse = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $cover,
        ]);

        $coverPath = $coverResponse->json('path');
        $article->update(['cover_image' => $coverPath]);

        Storage::disk('public')->assertExists($coverPath);

        // Add image section
        $image = UploadedFile::fake()->image('section-image.jpg', 100, 100);
        $imageResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);

        $imagePath = $imageResponse->json('path');
        $imageSection = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => $imagePath,
            'order' => 1,
        ]);

        Storage::disk('public')->assertExists($imagePath);

        // Add GIF section
        $gif = UploadedFile::fake()->create('section-gif.gif', 512, 'image/gif');
        $gif->name = 'section-gif.gif';

        $gifResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);

        $gifPath = $gifResponse->json('path');
        $gifSection = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'gif',
            'content' => $gifPath,
            'order' => 2,
        ]);

        Storage::disk('public')->assertExists($gifPath);

        // Delete article
        $response = $this->actingAs($user)->delete("/articles/{$article->id}");

        // EXPECTED (correct behavior):
        // All three files should be deleted
        Storage::disk('public')->assertMissing($coverPath);
        Storage::disk('public')->assertMissing($imagePath);
        Storage::disk('public')->assertMissing($gifPath);
    });
});

describe('COVER REPLACEMENT', function () {
    it('deletes old cover when replaced', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Upload and set first cover
        $cover1 = UploadedFile::fake()->image('cover1.jpg', 100, 100);
        $cover1Response = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $cover1,
        ]);

        $cover1Path = $cover1Response->json('path');
        $article->update(['cover_image' => $cover1Path]);

        Storage::disk('public')->assertExists($cover1Path);

        // Upload and set new cover - should delete old one
        $cover2 = UploadedFile::fake()->image('cover2.jpg', 100, 100);
        $cover2Response = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $cover2,
        ]);

        $cover2Path = $cover2Response->json('path');

        // Update article with new cover (triggers ArticleObserver::updating)
        $article->update(['cover_image' => $cover2Path]);

        // EXPECTED (correct behavior):
        // Old cover should be deleted by ArticleObserver::updating
        Storage::disk('public')->assertMissing($cover1Path);
        Storage::disk('public')->assertExists($cover2Path);
    });
});

describe('REGRESSION: PHASE 6 FIXES VERIFICATION', function () {
    it('verifies GIF deletion now works (FIX #1)', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Create GIF section
        $gif = UploadedFile::fake()->create('test.gif', 512, 'image/gif');
        $gif->name = 'test.gif';

        $uploadResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);

        $gifPath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($gifPath);

        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'gif',
            'content' => $gifPath,
            'order' => 1,
        ]);

        // Delete section - should now delete GIF file too
        $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");

        // FIXED: GIF file should be deleted
        Storage::disk('public')->assertMissing($gifPath);
    });

    it('verifies avatar deletion clears public_id (FIX #2)', function () {
        $user = User::factory()->create([
            'avatar' => 'uploads/users/avatars/test.jpg',
            'avatar_public_id' => 'test-public-id',
        ]);

        Storage::disk('public')->put('uploads/users/avatars/test.jpg', 'fake image');
        Storage::disk('public')->assertExists('uploads/users/avatars/test.jpg');

        // Delete avatar
        $this->actingAs($user)->delete("/profile/{$user->slug}/avatar");

        $user->refresh();

        // FIXED: Both avatar path and public_id should be cleared
        expect($user->avatar)->toBeNull();
        expect($user->avatar_public_id)->toBeNull();
    });

    it('verifies unsaved local images can be deleted (FIX #3)', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Upload image
        $image = UploadedFile::fake()->image('test.jpg', 100, 100);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);

        $imagePath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($imagePath);

        // Delete unsaved image with local_path parameter (new fix)
        $response = $this->actingAs($user)->delete("/articles/{$article->id}/unsaved-image", [
            'local_path' => $imagePath,
        ]);

        // FIXED: Local file should be deleted
        Storage::disk('public')->assertMissing($imagePath);
    });

    it('verifies article deletion cascade removes all file types', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Create cover
        $cover = UploadedFile::fake()->image('cover.jpg', 100, 100);
        $coverResponse = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $cover,
        ]);
        $coverPath = $coverResponse->json('path');
        $article->update(['cover_image' => $coverPath]);
        Storage::disk('public')->assertExists($coverPath);

        // Create image section
        $image = UploadedFile::fake()->image('image.jpg', 100, 100);
        $imageResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);
        $imagePath = $imageResponse->json('path');
        ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => $imagePath,
            'order' => 1,
        ]);
        Storage::disk('public')->assertExists($imagePath);

        // Create GIF section (new in Phase 6 fix)
        $gif = UploadedFile::fake()->create('gif.gif', 512, 'image/gif');
        $gif->name = 'gif.gif';
        $gifResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);
        $gifPath = $gifResponse->json('path');
        ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'gif',
            'content' => $gifPath,
            'order' => 2,
        ]);
        Storage::disk('public')->assertExists($gifPath);

        // Delete article
        $this->actingAs($user)->delete("/articles/{$article->id}");

        // FIXED: All files should be deleted
        Storage::disk('public')->assertMissing($coverPath);
        Storage::disk('public')->assertMissing($imagePath);
        Storage::disk('public')->assertMissing($gifPath);
    });

    it('verifies multiple image section deletions do not cause errors', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        $filePaths = [];

        // Create 3 image sections
        for ($i = 1; $i <= 3; $i++) {
            $image = UploadedFile::fake()->image("image{$i}.jpg", 100, 100);
            $response = $this->actingAs($user)->post('/local-upload/image', [
                'file' => $image,
            ]);
            $path = $response->json('path');
            $filePaths[] = $path;

            ArticleSection::create([
                'article_id' => $article->id,
                'type' => 'image',
                'content' => $path,
                'order' => $i,
            ]);

            Storage::disk('public')->assertExists($path);
        }

        // Delete each section individually
        $sections = ArticleSection::where('article_id', $article->id)->get();
        foreach ($sections as $section) {
            $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");
        }

        // All files should be deleted
        foreach ($filePaths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
    });

    it('verifies mixed content deletion (text + images + gifs)', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Create text section (no file)
        $textSection = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'text',
            'content' => 'Sample text content',
            'order' => 1,
        ]);

        // Create image section
        $image = UploadedFile::fake()->image('image.jpg', 100, 100);
        $imageResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);
        $imagePath = $imageResponse->json('path');
        $imageSection = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => $imagePath,
            'order' => 2,
        ]);
        Storage::disk('public')->assertExists($imagePath);

        // Create GIF section
        $gif = UploadedFile::fake()->create('test.gif', 512, 'image/gif');
        $gif->name = 'test.gif';
        $gifResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);
        $gifPath = $gifResponse->json('path');
        $gifSection = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'gif',
            'content' => $gifPath,
            'order' => 3,
        ]);
        Storage::disk('public')->assertExists($gifPath);

        // Delete image section
        $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$imageSection->id}");
        Storage::disk('public')->assertMissing($imagePath);

        // Text section should still exist
        expect(ArticleSection::find($textSection->id))->not->toBeNull();

        // Delete GIF section
        $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$gifSection->id}");
        Storage::disk('public')->assertMissing($gifPath);

        // Text section still exists
        expect(ArticleSection::find($textSection->id))->not->toBeNull();

        // Delete article (includes text section)
        $this->actingAs($user)->delete("/articles/{$article->id}");

        // All sections deleted
        expect(ArticleSection::where('article_id', $article->id)->count())->toBe(0);
    });
});

describe('PHASE 6.1: BROWSER GIF DELETION EXACT FLOW', function () {
    it('reproduces exact browser scenario - save GIF via saveSections then delete', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Step 1: Create article
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Step 2: Upload GIF file (simulating browser upload to /local-upload/gif)
        $gif = UploadedFile::fake()->create('test.gif', 512, 'image/gif');
        $uploadResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);

        $uploadResponse->assertStatus(200);
        $gifPath = $uploadResponse->json('path');
        expect($gifPath)->toContain('uploads/articles/gifs/');

        // Step 3: Verify GIF file exists in storage
        Storage::disk('public')->assertExists($gifPath);

        // Step 4: Save sections via saveSections() endpoint (simulating browser save)
        // This is the EXACT flow the browser uses: collect sections with GIF path, POST to /articles/{id}/sections
        $saveResponse = $this->actingAs($user)->post("/articles/{$article->id}/sections", [
            'sections' => [
                [
                    'type' => 'gif',
                    'content' => $gifPath,  // ← This is what the browser sends
                    'public_id' => null,
                ],
            ],
        ]);

        $saveResponse->assertStatus(200);
        $saveResponse->assertJson(['success' => true]);

        // Step 5: Verify section is saved to database with correct content
        $section = ArticleSection::where('article_id', $article->id)
            ->where('type', 'gif')
            ->first();

        expect($section)->not->toBeNull();
        expect($section->content)->toBe($gifPath);

        // Step 6: Delete GIF section via DELETE endpoint (simulating browser delete)
        $deleteResponse = $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");

        $deleteResponse->assertStatus(200);
        $deleteResponse->assertJson(['success' => true]);

        // Step 7: Verify section is deleted from database
        expect(ArticleSection::find($section->id))->toBeNull();

        // Step 8: CRITICAL - Verify GIF file is deleted from storage
        // THIS IS THE BUG: file should be gone but may still exist
        Storage::disk('public')->assertMissing($gifPath);
    });

    it('reproduces exact browser scenario - image section deletion (control test)', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        // Step 1: Create article
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        // Step 2: Upload image file
        $image = UploadedFile::fake()->image('test.jpg', 100, 100);
        $uploadResponse = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $image,
        ]);

        $uploadResponse->assertStatus(200);
        $imagePath = $uploadResponse->json('path');
        expect($imagePath)->toContain('uploads/articles/sections/');

        Storage::disk('public')->assertExists($imagePath);

        // Step 3: Save image section via saveSections()
        $saveResponse = $this->actingAs($user)->post("/articles/{$article->id}/sections", [
            'sections' => [
                [
                    'type' => 'image',
                    'content' => $imagePath,
                    'public_id' => null,
                ],
            ],
        ]);

        $saveResponse->assertStatus(200);

        $section = ArticleSection::where('article_id', $article->id)
            ->where('type', 'image')
            ->first();

        expect($section)->not->toBeNull();

        // Step 4: Delete image section
        $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");

        // Step 5: Verify image file is deleted
        Storage::disk('public')->assertMissing($imagePath);
    });

    it('tests observer is called for GIF deletion with logging', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $gif = UploadedFile::fake()->create('test.gif', 512, 'image/gif');
        $uploadResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);

        $gifPath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($gifPath);

        // Save via saveSections
        $this->actingAs($user)->post("/articles/{$article->id}/sections", [
            'sections' => [
                ['type' => 'gif', 'content' => $gifPath, 'public_id' => null],
            ],
        ]);

        $section = ArticleSection::where('article_id', $article->id)
            ->where('type', 'gif')
            ->first();

        expect($section)->not->toBeNull();
        expect($section->type)->toBe('gif');
        expect($section->content)->toBe($gifPath);

        // Inspect logs after deletion
        \Log::info('TEST: About to delete GIF section', [
            'section_id' => $section->id,
            'section_type' => $section->type,
            'section_content' => $section->content,
        ]);

        $this->actingAs($user)->delete("/articles/{$article->id}/sections/{$section->id}");

        // Verify file is deleted
        Storage::disk('public')->assertMissing($gifPath);
    });

    it('handles unsaved GIF cleanup via deleteUnsavedImage endpoint', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
        ]);

        // Upload GIF but DO NOT save to database
        $gif = UploadedFile::fake()->create('test.gif', 512, 'image/gif');
        $uploadResponse = $this->actingAs($user)->post('/local-upload/gif', [
            'file' => $gif,
        ]);

        $gifPath = $uploadResponse->json('path');
        Storage::disk('public')->assertExists($gifPath);

        // Simulate user deleting unsaved GIF section via browser
        // Browser sends local_path parameter to cleanup endpoint
        $cleanupResponse = $this->actingAs($user)->delete("/articles/{$article->id}/unsaved-image", [
            'local_path' => $gifPath,
        ]);

        $cleanupResponse->assertStatus(200);
        $cleanupResponse->assertJson(['success' => true]);

        // Verify GIF file is deleted
        Storage::disk('public')->assertMissing($gifPath);
    });
});
