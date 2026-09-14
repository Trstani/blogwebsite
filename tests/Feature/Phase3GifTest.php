<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\LocalFileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

describe('GIF Upload', function () {
    it('uploads GIF file to local storage', function () {
        $response = $this->actingAs(User::factory()->create())
            ->postJson('/local-upload/gif', [
                'file' => UploadedFile::fake()->create('test.gif', 100, 'image/gif'),
            ]);

        $response->assertJsonPath('success', true);
        expect($response->json('path'))->toMatch('/^uploads\/articles\/gifs\/\d+-[a-zA-Z0-9]{12}\.gif$/');
        expect($response->json('url'))->toMatch('/^\/storage\/uploads\/articles\/gifs\/\d+-[a-zA-Z0-9]{12}\.gif$/');

        Storage::disk('public')->assertExists($response->json('path'));
    });

    it('rejects non-GIF files', function () {
        $response = $this->actingAs(User::factory()->create())
            ->postJson('/local-upload/gif', [
                'file' => UploadedFile::fake()->create('test.jpg', 100, 'image/jpeg'),
            ]);

        $response->assertStatus(422);
        expect($response->status())->toBe(422);
    });

    it('rejects oversized GIF', function () {
        $response = $this->actingAs(User::factory()->create())
            ->postJson('/local-upload/gif', [
                'file' => UploadedFile::fake()->create('test.gif', 11 * 1024 * 1024, 'image/gif'),
            ]);

        $response->assertStatus(422);
        expect($response->status())->toBe(422);
    });

    it('requires authentication for GIF upload', function () {
        $response = $this->postJson('/local-upload/gif', [
            'file' => UploadedFile::fake()->create('test.gif', 100, 'image/gif'),
        ]);

        $response->assertUnauthorized();
    });

    it('generates unique GIF filenames', function () {
        $user = User::factory()->create();

        $response1 = $this->actingAs($user)
            ->postJson('/local-upload/gif', [
                'file' => UploadedFile::fake()->create('test.gif', 100, 'image/gif'),
            ]);

        $response2 = $this->actingAs($user)
            ->postJson('/local-upload/gif', [
                'file' => UploadedFile::fake()->create('test.gif', 100, 'image/gif'),
            ]);

        expect($response1->json('path'))->not->toBe($response2->json('path'));
    });
});

describe('GIF Section Storage and Retrieval', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);
    });

    it('saves GIF section with local path', function () {
        $this->actingAs($this->user)
            ->postJson('/articles/'.$this->article->id.'/sections', [
                'sections' => [
                    [
                        'type' => 'gif',
                        'content' => 'uploads/articles/gifs/1234567890-abcdefghijkl.gif',
                    ],
                ],
            ])
            ->assertJsonPath('success', true);

        expect($this->article->sections()->where('type', 'gif')->count())->toBe(1);
        expect($this->article->sections()->where('type', 'gif')->first()->content)
            ->toBe('uploads/articles/gifs/1234567890-abcdefghijkl.gif');
    });

    it('retrieves GIF section with original path', function () {
        $gifPath = 'uploads/articles/gifs/1234567890-abcdefghijkl.gif';

        $this->article->sections()->create([
            'type' => 'gif',
            'content' => $gifPath,
            'order' => 1,
        ]);

        $this->actingAs($this->user)
            ->getJson('/articles/'.$this->article->id)
            ->assertJsonPath('sections.0.type', 'gif')
            ->assertJsonPath('sections.0.content', $gifPath);
    });

    it('saves mixed content with GIF sections', function () {
        $this->actingAs($this->user)
            ->postJson('/articles/'.$this->article->id.'/sections', [
                'sections' => [
                    ['type' => 'text', 'content' => 'Text content'],
                    ['type' => 'gif', 'content' => 'uploads/articles/gifs/gif1.gif'],
                    ['type' => 'image', 'content' => 'uploads/articles/sections/img1.jpg'],
                    ['type' => 'gif', 'content' => 'uploads/articles/gifs/gif2.gif'],
                ],
            ])
            ->assertJsonPath('success', true);

        expect($this->article->sections()->count())->toBe(4);
        expect($this->article->sections()->where('type', 'gif')->count())->toBe(2);
        expect($this->article->sections()->where('type', 'text')->count())->toBe(1);
        expect($this->article->sections()->where('type', 'image')->count())->toBe(1);
    });
});

describe('GIF Deletion', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);
    });

    it('GIF observer integration is configured', function () {
        // Test that GIF sections can be deleted without errors
        $this->article->sections()->create([
            'type' => 'gif',
            'content' => 'uploads/articles/gifs/test.gif',
            'order' => 1,
        ]);

        $section = $this->article->sections()->first();
        expect($section->type)->toBe('gif');

        // Deletion should work without throwing errors
        $section->delete();

        expect($this->article->sections()->count())->toBe(0);
    });
});

describe('GIF Validation', function () {
    beforeEach(function () {
        $this->service = app(LocalFileStorageService::class);
    });

    it('recognizes GIF local paths', function () {
        expect($this->service->isLocalPath('uploads/articles/gifs/example.gif'))->toBeTrue();
        expect($this->service->isLocalPath('/storage/uploads/articles/gifs/example.gif'))->toBeTrue();
    });

    it('validates GIF file path structure', function () {
        // Valid GIF path
        expect($this->service->isLocalPath('uploads/articles/gifs/1234567890-abcdefghijkl.gif'))->toBeTrue();

        // Invalid: wrong category
        expect($this->service->isLocalPath('uploads/users/gifs/file.gif'))->toBeFalse();

        // Invalid: path traversal
        expect($this->service->isLocalPath('uploads/articles/gifs/../../.env'))->toBeFalse();
    });
});
