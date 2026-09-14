<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

/**
 * PHASE 6 - VIDEO PREVIEW TESTS
 *
 * Tests for the new video preview endpoint and Writing Page video integration:
 * - Backend endpoint: POST /articles/{article}/preview-video
 * - Returns: { valid, provider, embedHtml }
 * - Validates: YouTube, Vimeo, direct video URLs
 * - Security: Blocks dangerous schemes (javascript:, data:, file:)
 */

describe('VIDEO PREVIEW ENDPOINT', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);
    });

    describe('YouTube Video Preview', function () {
        it('previews YouTube watch URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('youtube');
            expect($data['embedHtml'])->toContain('iframe');
            expect($data['embedHtml'])->toContain('dQw4w9WgXcQ');
        });

        it('previews YouTube short URL (youtu.be)', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://youtu.be/dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('youtube');
            expect($data['embedHtml'])->toContain('iframe');
            expect($data['embedHtml'])->toContain('dQw4w9WgXcQ');
        });

        it('previews YouTube embed URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('youtube');
            expect($data['embedHtml'])->toContain('iframe');
            expect($data['embedHtml'])->toContain('dQw4w9WgXcQ');
        });

        it('extracts YouTube ID correctly from watch URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&list=PLtest',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['embedHtml'])->toContain('dQw4w9WgXcQ');
        });

        it('extracts YouTube ID from short URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://youtu.be/dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['embedHtml'])->toContain('dQw4w9WgXcQ');
        });
    });

    describe('Vimeo Video Preview', function () {
        it('previews Vimeo URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://vimeo.com/123456789',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('vimeo');
            expect($data['embedHtml'])->toContain('iframe');
            expect($data['embedHtml'])->toContain('vimeo');
        });

        it('previews Vimeo player URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://player.vimeo.com/video/123456789',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('vimeo');
            expect($data['embedHtml'])->toContain('iframe');
        });

        it('extracts Vimeo ID correctly', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://vimeo.com/123456789',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['embedHtml'])->toContain('123456789');
        });
    });

    describe('Direct Video Files', function () {
        it('previews direct MP4 URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://example.com/video.mp4',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('direct');
            expect($data['embedHtml'])->toContain('<video');
            expect($data['embedHtml'])->toContain('https://example.com/video.mp4');
        });

        it('previews direct WebM URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://example.com/video.webm',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('direct');
            expect($data['embedHtml'])->toContain('<video');
        });

        it('previews direct OGG URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://example.com/video.ogg',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('direct');
            expect($data['embedHtml'])->toContain('<video');
        });

        it('supports direct video with query parameters', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://example.com/video.mp4?token=abc123&expires=1234567890',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('direct');
        });
    });

    describe('Invalid URLs', function () {
        it('rejects URL without protocol', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'youtube.com/watch?v=dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });

        it('rejects malformed URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'not a valid url at all',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });

        it('rejects unsupported video provider', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://dailymotion.com/video/xyz123',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });

        it('rejects invalid video file extension', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://example.com/video.txt',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });
    });

    describe('Security', function () {
        it('blocks javascript: URLs', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'javascript:alert("XSS")',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });

        it('blocks data: URLs', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'data:text/html,<script>alert("XSS")</script>',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });

        it('blocks file: URLs', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'file:///etc/passwd',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
        });

        it('escapes HTML in embed HTML output', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            $data = $response->json();
            $html = $data['embedHtml'];

            // HTML should not contain unescaped user input
            expect($html)->not->toContain('<script');
            expect($html)->not->toContain('javascript:');
            expect($html)->toContain('iframe');
        });
    });

    describe('Authorization', function () {
        it('requires authentication', function () {
            $this->post("/articles/{$this->article->id}/preview-video", [
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])->assertStatus(403);
        });

        it('requires article ownership', function () {
            $otherUser = User::factory()->create();

            $response = $this->actingAs($otherUser)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ])
                ->assertStatus(403);
        });

        it('allows owner to preview video', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ])
                ->assertStatus(200);

            expect($response->json('valid'))->toBeTrue();
        });
    });

    describe('Response Format', function () {
        it('returns consistent JSON structure for valid URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ])
                ->assertStatus(200)
                ->assertJsonStructure([
                    'valid',
                    'provider',
                    'embedHtml',
                ]);

            $data = $response->json();
            expect(is_bool($data['valid']))->toBeTrue();
            expect(is_string($data['provider']))->toBeTrue();
            expect(is_string($data['embedHtml']))->toBeTrue();
        });

        it('returns consistent JSON structure for invalid URL', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'not-a-url',
                ])
                ->assertStatus(200)
                ->assertJsonStructure([
                    'valid',
                    'message',
                ]);

            $data = $response->json();
            expect($data['valid'])->toBeFalse();
            expect(isset($data['message']))->toBeTrue();
        });
    });

    describe('Edge Cases', function () {
        it('handles YouTube URL with timestamp', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=123',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['embedHtml'])->toContain('dQw4w9WgXcQ');
        });

        it('handles case-insensitive file extensions', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://example.com/video.MP4',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
            expect($data['provider'])->toBe('direct');
        });

        it('handles URLs with fragments', function () {
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ#t=30s',
                ])
                ->assertStatus(200);

            $data = $response->json();
            expect($data['valid'])->toBeTrue();
        });

        it('handles very long video IDs', function () {
            $longId = str_repeat('a', 100);
            $response = $this->actingAs($this->user)
                ->post("/articles/{$this->article->id}/preview-video", [
                    'url' => "https://www.youtube.com/watch?v={$longId}",
                ])
                ->assertStatus(200);

            $data = $response->json();
            // Should either accept or reject gracefully
            expect(isset($data['valid']))->toBeTrue();
        });

        it('handles article that does not exist', function () {
            $response = $this->actingAs($this->user)
                ->post('/articles/99999/preview-video', [
                    'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                ]);

            expect($response->status())->toBeIn([404, 200]);
        });
    });
});

describe('VIDEO PREVIEW INTEGRATION', function () {
    it('allows multiple preview requests for same article', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        $urls = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://vimeo.com/123456789',
            'https://example.com/video.mp4',
        ];

        foreach ($urls as $url) {
            $response = $this->actingAs($user)
                ->post("/articles/{$article->id}/preview-video", ['url' => $url])
                ->assertStatus(200);

            expect($response->json('valid'))->toBeTrue();
        }
    });

    it('preview requests do not affect article state', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'draft',
            'updated_at' => now()->subHours(1),
        ]);

        $originalUpdatedAt = $article->updated_at;

        $this->actingAs($user)
            ->post("/articles/{$article->id}/preview-video", [
                'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertStatus(200);

        $article->refresh();
        // Article should not be modified by preview request
        expect($article->updated_at->timestamp)->toBe($originalUpdatedAt->timestamp);
    });
});
