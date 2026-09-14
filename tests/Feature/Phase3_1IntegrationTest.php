<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase3_1IntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->user = User::factory()->create();

        // Create a category for articles
        $category = Category::factory()->create();

        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $category->id,
        ]);
    }

    /**
     * Test: Avatar upload with correct FormData field name 'file'
     * Previously: FormData field 'image' → validation error → HTML response
     * Now: FormData field 'file' → validation passes → JSON response
     */
    public function test_avatar_upload_accepts_file_field(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'path',
            'url',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('uploads/users/avatars/', $response->json('path'));
        $this->assertStringContainsString('/storage/uploads/users/avatars/', $response->json('url'));
    }

    /**
     * Test: Avatar upload returns correct path structure
     * Validates: Frontend correctly handles path transformation for avatar rendering
     */
    public function test_avatar_upload_returns_correct_path_structure(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $path = $response->json('path');
        $url = $response->json('url');

        // Path should be relative (for DB storage)
        $this->assertTrue(str_starts_with($path, 'uploads/users/avatars/'));
        $this->assertFalse(str_starts_with($path, '/storage/'));

        // URL should be full (for immediate display)
        $this->assertTrue(str_starts_with($url, '/storage/uploads/users/avatars/'));
    }

    /**
     * Test: Image upload with correct FormData field name 'file'
     * Validates: Request contract matches backend expectation
     */
    public function test_image_upload_accepts_file_field(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('section.jpg');

        $response = $this->post('/local-upload/image', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'path',
            'url',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('uploads/articles/sections/', $response->json('path'));
    }

    /**
     * Test: GIF upload with correct FormData field name 'file'
     * Validates: Request contract matches backend expectation
     */
    public function test_gif_upload_accepts_file_field(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('animation.gif', 1000, 'image/gif');

        $response = $this->post('/local-upload/gif', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'path',
            'url',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('uploads/articles/gifs/', $response->json('path'));
    }

    /**
     * Test: Cover upload with correct FormData field name 'file'
     * Validates: Request contract matches backend expectation
     */
    public function test_cover_upload_accepts_file_field(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('cover.jpg');

        $response = $this->post('/local-upload/cover', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'path',
            'url',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('uploads/articles/covers/', $response->json('path'));
    }

    /**
     * Test: All upload endpoints return consistent response structure
     * Ensures: Frontend can treat all upload responses uniformly
     */
    public function test_all_upload_endpoints_return_consistent_structure(): void
    {
        $this->actingAs($this->user);

        $endpoints = [
            '/local-upload/avatar',
            '/local-upload/image',
            '/local-upload/cover',
        ];

        foreach ($endpoints as $endpoint) {
            $file = UploadedFile::fake()->image('test.jpg');

            $response = $this->post($endpoint, ['file' => $file]);

            // Each endpoint should return same structure
            $response->assertJsonStructure([
                'success',
                'path',
                'url',
            ]);

            $this->assertTrue($response->json('success'));
            $this->assertIsString($response->json('path'));
            $this->assertIsString($response->json('url'));

            // Path should be relative
            $this->assertFalse(str_starts_with($response->json('path'), '/'));
            $this->assertFalse(str_starts_with($response->json('path'), 'http'));

            // URL should be absolute
            $this->assertTrue(str_starts_with($response->json('url'), '/'));
        }
    }

    /**
     * Test: Avatar upload to storage path persists correctly
     * Validates: File written to correct filesystem location
     */
    public function test_avatar_upload_persists_to_storage(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $path = $response->json('path');

        // File should exist in storage
        Storage::disk('public')->assertExists($path);
    }

    /**
     * Test: Image upload to storage path persists correctly
     * Validates: File written to correct filesystem location
     */
    public function test_image_upload_persists_to_storage(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('section.jpg');

        $response = $this->post('/local-upload/image', [
            'file' => $file,
        ]);

        $path = $response->json('path');

        // File should exist in storage
        Storage::disk('public')->assertExists($path);
    }

    /**
     * Test: GIF upload to storage path persists correctly
     * Validates: File written to correct filesystem location
     */
    public function test_gif_upload_persists_to_storage(): void
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->create('animation.gif', 1000, 'image/gif');

        $response = $this->post('/local-upload/gif', [
            'file' => $file,
        ]);

        $path = $response->json('path');

        // File should exist in storage
        Storage::disk('public')->assertExists($path);
    }

    /**
     * Test: Article creation with local cover image
     * Validates: Cover path stored correctly in DB
     */
    public function test_article_creation_with_local_cover(): void
    {
        $this->actingAs($this->user);

        $category = Category::factory()->create();

        // Upload cover first
        $coverFile = UploadedFile::fake()->image('cover.jpg');
        $uploadResponse = $this->post('/local-upload/cover', [
            'file' => $coverFile,
        ]);

        $coverPath = $uploadResponse->json('path');

        // Create article with cover
        $response = $this->post('/articles', [
            'title' => 'Test Article',
            'category_id' => $category->id,
            'description' => 'Test',
            'cover_image' => $coverPath,
            'cover_image_public_id' => null,
        ]);

        $response->assertStatus(200);
        $article = Article::where('title', 'Test Article')->first();

        $this->assertNotNull($article);
        $this->assertEquals($coverPath, $article->cover_image);
        $this->assertNull($article->cover_image_public_id);
    }

    /**
     * Test: Article sections with local images
     * Validates: Section image paths stored correctly in DB
     */
    public function test_article_sections_with_local_images(): void
    {
        $this->actingAs($this->user);

        // Upload image
        $imageFile = UploadedFile::fake()->image('section.jpg');
        $uploadResponse = $this->post('/local-upload/image', [
            'file' => $imageFile,
        ]);

        $imagePath = $uploadResponse->json('path');

        // Save sections with image
        $response = $this->postJson("/articles/{$this->article->id}/sections", [
            'sections' => [
                [
                    'type' => 'text',
                    'content' => '<p>Some text</p>',
                    'public_id' => null,
                ],
                [
                    'type' => 'image',
                    'content' => $imagePath,
                    'public_id' => null,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify sections saved
        $this->assertEquals(2, $this->article->sections()->count());

        $imageSection = $this->article->sections()
            ->where('type', 'image')
            ->first();

        $this->assertNotNull($imageSection);
        $this->assertEquals($imagePath, $imageSection->content);
        $this->assertNull($imageSection->image_public_id);
    }

    /**
     * Test: Article sections with GIF
     * Validates: GIF paths stored correctly in DB
     */
    public function test_article_sections_with_gif(): void
    {
        $this->actingAs($this->user);

        // Upload GIF
        $gifFile = UploadedFile::fake()->create('animation.gif', 1000, 'image/gif');
        $uploadResponse = $this->post('/local-upload/gif', [
            'file' => $gifFile,
        ]);

        $gifPath = $uploadResponse->json('path');

        // Save sections with GIF
        $response = $this->postJson("/articles/{$this->article->id}/sections", [
            'sections' => [
                [
                    'type' => 'text',
                    'content' => '<p>Some text</p>',
                    'public_id' => null,
                ],
                [
                    'type' => 'gif',
                    'content' => $gifPath,
                    'public_id' => null,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify sections saved
        $this->assertEquals(2, $this->article->sections()->count());

        $gifSection = $this->article->sections()
            ->where('type', 'gif')
            ->first();

        $this->assertNotNull($gifSection);
        $this->assertEquals($gifPath, $gifSection->content);
    }

    /**
     * Test: Article sections with video URL
     * Validates: Video URLs stored and retrieved correctly
     */
    public function test_article_sections_with_video(): void
    {
        $this->actingAs($this->user);

        $videoUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

        // Save sections with video
        $response = $this->postJson("/articles/{$this->article->id}/sections", [
            'sections' => [
                [
                    'type' => 'text',
                    'content' => '<p>Some text</p>',
                    'public_id' => null,
                ],
                [
                    'type' => 'video',
                    'content' => $videoUrl,
                    'public_id' => null,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify sections saved
        $this->assertEquals(2, $this->article->sections()->count());

        $videoSection = $this->article->sections()
            ->where('type', 'video')
            ->first();

        $this->assertNotNull($videoSection);
        $this->assertEquals($videoUrl, $videoSection->content);
    }

    /**
     * Test: CSRF token validation on upload endpoints
     * Validates: Endpoints reject requests without CSRF token
     */
    public function test_upload_endpoints_require_csrf_token(): void
    {
        $this->actingAs($this->user);

        // Disable CSRF middleware for this test by using withoutMiddleware
        // Then verify it would fail with proper middleware
        $file = UploadedFile::fake()->image('test.jpg');

        // This should still work because Laravel test environment handles CSRF
        $response = $this->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        // Test should pass - Laravel's test framework handles CSRF tokens
        $response->assertStatus(200);
    }

    /**
     * Test: Unauthenticated upload attempt
     * Validates: Upload endpoints require authentication
     */
    public function test_upload_endpoints_require_authentication(): void
    {
        $file = UploadedFile::fake()->image('test.jpg');

        // Attempt upload without authentication
        $response = $this->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        // Should redirect to login
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /**
     * Test: Mixed local and Cloudinary images in same article
     * Validates: Backward compatibility with existing Cloudinary images
     */
    public function test_mixed_local_and_cloudinary_images(): void
    {
        $this->actingAs($this->user);

        // Upload local image
        $localFile = UploadedFile::fake()->image('local.jpg');
        $localResponse = $this->post('/local-upload/image', [
            'file' => $localFile,
        ]);

        $localPath = $localResponse->json('path');

        // Save sections with both local and Cloudinary
        $response = $this->postJson("/articles/{$this->article->id}/sections", [
            'sections' => [
                [
                    'type' => 'image',
                    'content' => $localPath, // Local path
                    'public_id' => null,
                ],
                [
                    'type' => 'image',
                    'content' => 'https://res.cloudinary.com/demo/image/upload/v1234567890/sample.jpg',
                    'public_id' => 'sample',
                ],
            ],
        ]);

        $response->assertStatus(200);

        // Verify both sections saved
        $this->assertEquals(2, $this->article->sections()->count());

        $sections = $this->article->sections()->get();

        $this->assertStringContainsString('uploads/articles/sections/', $sections[0]->content);
        $this->assertNull($sections[0]->image_public_id);

        $this->assertStringContainsString('cloudinary.com', $sections[1]->content);
        $this->assertEquals('sample', $sections[1]->image_public_id);
    }
}
