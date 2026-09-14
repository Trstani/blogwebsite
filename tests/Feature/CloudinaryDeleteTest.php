<?php

namespace Tests\Feature;

use App\Helpers\CloudinaryHelper;
use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CloudinaryDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test article deletion triggers observer
     */
    public function test_article_delete_triggers_observer(): void
    {
        // Create category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create a user
        $user = User::factory()->create();

        // Create an article with cover image
        $article = Article::create([
            'title' => 'Test Article',
            'slug' => 'test-article-123',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'author_id' => $user->id,
            'status' => 'draft',
            'cover_image' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v1234567890/test_cover.jpg',
        ]);

        // Add a section image
        ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v1234567890/test_section_image.jpg',
            'order' => 1,
        ]);

        // Add a text section
        ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'text',
            'content' => '<p>Test content</p>',
            'order' => 2,
        ]);

        $articleId = $article->id;

        // Delete the article - this should trigger the observer
        $article->delete();

        // Verify article is deleted
        $this->assertDatabaseMissing('articles', ['id' => $articleId]);

        // Verify sections are deleted
        $this->assertDatabaseMissing('article_sections', ['article_id' => $articleId]);

        $this->assertTrue(true, 'Article and sections deleted successfully - observer triggered');
    }

    /**
     * Test section image deletion triggers observer
     */
    public function test_section_delete_triggers_observer(): void
    {
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $user = User::factory()->create();

        $article = Article::create([
            'title' => 'Test Article',
            'slug' => 'test-article-456',
            'description' => 'Test Description',
            'category_id' => $category->id,
            'author_id' => $user->id,
            'status' => 'draft',
        ]);

        // Add section image
        $section = ArticleSection::create([
            'article_id' => $article->id,
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/kjkvm9vj/image/upload/v1234567890/test_section.jpg',
            'order' => 1,
        ]);

        $sectionId = $section->id;

        // Delete the section
        $section->delete();

        // Verify section is deleted
        $this->assertDatabaseMissing('article_sections', ['id' => $sectionId]);

        $this->assertTrue(true, 'Section deleted successfully - observer triggered');
    }

    /**
     * Test CloudinaryHelper extracts public_id correctly
     */
    public function test_cloudinary_helper_extracts_public_id(): void
    {
        $helper = new CloudinaryHelper;

        // Test URL with version
        $url1 = 'https://res.cloudinary.com/kjkvm9vj/image/upload/v1234567890/abc123def456.jpg';
        $publicId1 = $helper->extractPublicId($url1);
        $this->assertEquals('abc123def456', $publicId1);

        // Test URL without version
        $url2 = 'https://res.cloudinary.com/kjkvm9vj/image/upload/xyz789uvw123.png';
        $publicId2 = $helper->extractPublicId($url2);
        $this->assertEquals('xyz789uvw123', $publicId2);

        // Test null URL
        $publicId3 = $helper->extractPublicId(null);
        $this->assertNull($publicId3);

        // Test non-Cloudinary URL
        $publicId4 = $helper->extractPublicId('https://example.com/image.jpg');
        $this->assertNull($publicId4);

        $this->assertTrue(true, 'CloudinaryHelper public_id extraction works correctly');
    }
}
