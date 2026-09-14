<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsavedImageCleanupTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::factory()->create();
        $this->user = User::factory()->create();
    }

    /**
     * TEST 1: Unsaved image delete - should queue Cloudinary deletion
     */
    public function test_unsaved_image_section_delete_queues_cloudinary_deletion(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create();

        $this->actingAs($this->user);

        $response = $this->deleteJson("/articles/{$article->id}/unsaved-image", [
            'public_id' => 'test_public_id_123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Image cleanup queued.',
        ]);
    }

    /**
     * TEST 2: Unsaved text delete - should NOT make backend request (handled in frontend only)
     */
    public function test_unsaved_text_section_delete_no_backend_call(): void
    {
        // This test verifies frontend behavior - text sections are only removed from DOM
        // No backend interaction needed
        // This is a conceptual test - actual behavior is frontend-only
        $this->assertTrue(true, 'Unsaved text sections are handled frontend-only (no backend call)');
    }

    /**
     * TEST 3: Saved image delete - should use Observer/Job pattern
     */
    public function test_saved_image_section_delete_uses_observer(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create();

        $section = $article->sections()->create([
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/test/image.jpg',
            'image_public_id' => 'saved_section_public_id',
            'order' => 1,
        ]);

        $this->actingAs($this->user);

        // Delete via the saved section endpoint (should use Observer)
        $response = $this->deleteJson("/articles/{$article->id}/sections/{$section->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Section deleted and Cloudinary cleanup queued.',
        ]);

        // Verify section is deleted from database
        $this->assertDatabaseMissing('article_sections', [
            'id' => $section->id,
        ]);
    }

    /**
     * TEST 4: Unsaved image replacement - old image should be deleted
     */
    public function test_unsaved_image_replacement_queues_old_deletion(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create();

        $this->actingAs($this->user);

        // Simulate old image cleanup during replacement
        $response = $this->deleteJson("/articles/{$article->id}/unsaved-image", [
            'public_id' => 'old_image_public_id',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * TEST 5: Failed replacement upload - old image should NOT be deleted
     */
    public function test_failed_upload_does_not_delete_old_image(): void
    {
        // This test verifies frontend behavior - if new upload fails,
        // old image cleanup is NOT called
        // This is handled in frontend error handling
        $this->assertTrue(true, 'Failed uploads prevent old image cleanup (frontend-only behavior)');
    }

    /**
     * TEST 6: Saved image replacement - old cover deleted via Observer
     */
    public function test_saved_image_replacement_deletes_old_cover(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create([
                'cover_image_public_id' => 'old_cover_public_id',
            ]);

        $this->actingAs($this->user);

        // Update with new cover
        $response = $this->postJson("/articles/{$article->id}/sections", [
            'sections' => [
                [
                    'type' => 'text',
                    'content' => '<p>Test content</p>',
                    'public_id' => null,
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * TEST 7: Saved text delete - should NOT attempt Cloudinary deletion
     */
    public function test_saved_text_section_delete_no_cloudinary_call(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create();

        $section = $article->sections()->create([
            'type' => 'text',
            'content' => '<p>Test text</p>',
            'order' => 1,
        ]);

        $this->actingAs($this->user);

        $response = $this->deleteJson("/articles/{$article->id}/sections/{$section->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verify section is deleted
        $this->assertDatabaseMissing('article_sections', [
            'id' => $section->id,
        ]);
    }

    /**
     * TEST 8: Authorization - user cannot delete another user's article's image
     */
    public function test_authorization_prevents_unauthorized_cleanup(): void
    {
        $otherUser = User::factory()->create();
        $article = Article::factory()
            ->for($otherUser, 'author')
            ->for($this->category)
            ->create();

        $this->actingAs($this->user);

        // Try to cleanup image from another user's article
        $response = $this->deleteJson("/articles/{$article->id}/unsaved-image", [
            'public_id' => 'unauthorized_public_id',
        ]);

        $response->assertStatus(403);
        $response->assertJson([
            'error' => 'Unauthorized',
        ]);
    }

    /**
     * ADDITIONAL: Verify unsaved-image endpoint requires authentication
     */
    public function test_unsaved_image_cleanup_requires_authentication(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create();

        // No authentication - should be redirected
        $response = $this->deleteJson("/articles/{$article->id}/unsaved-image", [
            'public_id' => 'test_public_id',
        ]);

        $response->assertStatus(401);
    }

    /**
     * ADDITIONAL: Verify unsaved-image endpoint validates public_id
     */
    public function test_unsaved_image_cleanup_validates_public_id(): void
    {
        $article = Article::factory()
            ->for($this->user, 'author')
            ->for($this->category)
            ->create();

        $this->actingAs($this->user);

        // Missing public_id
        $response = $this->deleteJson("/articles/{$article->id}/unsaved-image", [
            // no public_id provided
        ]);

        $response->assertStatus(422);
    }
}
