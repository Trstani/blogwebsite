<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\VideoUrlHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('VideoUrlHelper', function () {
    describe('URL Validation', function () {
        it('validates YouTube URL', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->isValidVideoUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ'))->toBeTrue();
            expect($helper->isValidVideoUrl('https://youtu.be/dQw4w9WgXcQ'))->toBeTrue();
            expect($helper->isValidVideoUrl('https://www.youtube.com/embed/dQw4w9WgXcQ'))->toBeTrue();
        });

        it('validates Vimeo URL', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->isValidVideoUrl('https://vimeo.com/123456789'))->toBeTrue();
            expect($helper->isValidVideoUrl('https://player.vimeo.com/video/123456789'))->toBeTrue();
        });

        it('validates direct video URLs', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->isValidVideoUrl('https://example.com/video.mp4'))->toBeTrue();
            expect($helper->isValidVideoUrl('https://example.com/video.webm'))->toBeTrue();
            expect($helper->isValidVideoUrl('https://cdn.example.com/path/to/video.ogg'))->toBeTrue();
        });

        it('rejects invalid URLs', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->isValidVideoUrl('not a url'))->toBeFalse();
            expect($helper->isValidVideoUrl('ftp://example.com/video.mp4'))->toBeFalse();
            expect($helper->isValidVideoUrl(''))->toBeFalse();
            expect($helper->isValidVideoUrl(null))->toBeFalse();
        });

        it('rejects dangerous schemes', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->isValidVideoUrl('javascript:alert("xss")'))->toBeFalse();
            expect($helper->isValidVideoUrl('data:text/html,<script>alert("xss")</script>'))->toBeFalse();
            expect($helper->isValidVideoUrl('file:///etc/passwd'))->toBeFalse();
        });

        it('rejects arbitrary iframe HTML', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->isValidVideoUrl('<iframe src="https://example.com"></iframe>'))->toBeFalse();
            expect($helper->isValidVideoUrl('<script>alert("xss")</script>'))->toBeFalse();
        });
    });

    describe('Video ID Extraction', function () {
        it('extracts YouTube video ID', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->extractYouTubeId('https://www.youtube.com/watch?v=dQw4w9WgXcQ'))
                ->toBe('dQw4w9WgXcQ');
            expect($helper->extractYouTubeId('https://youtu.be/dQw4w9WgXcQ'))
                ->toBe('dQw4w9WgXcQ');
            expect($helper->extractYouTubeId('https://www.youtube.com/embed/dQw4w9WgXcQ'))
                ->toBe('dQw4w9WgXcQ');
        });

        it('extracts Vimeo video ID', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->extractVimeoId('https://vimeo.com/123456789'))
                ->toBe('123456789');
            expect($helper->extractVimeoId('https://player.vimeo.com/video/987654321'))
                ->toBe('987654321');
        });

        it('returns null for non-matching URLs', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->extractYouTubeId('https://vimeo.com/123456789'))->toBeNull();
            expect($helper->extractVimeoId('https://youtube.com/watch?v=abc123'))->toBeNull();
            expect($helper->extractYouTubeId('https://example.com/video.mp4'))->toBeNull();
        });
    });

    describe('Embed URL Generation', function () {
        it('generates YouTube embed URL', function () {
            $helper = app(VideoUrlHelper::class);

            $embedUrl = $helper->getEmbedUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
            expect($embedUrl)->toContain('youtube.com/embed/dQw4w9WgXcQ');
        });

        it('generates Vimeo embed URL', function () {
            $helper = app(VideoUrlHelper::class);

            $embedUrl = $helper->getEmbedUrl('https://vimeo.com/123456789');
            expect($embedUrl)->toContain('player.vimeo.com/video/123456789');
        });

        it('returns direct URL as-is', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->getEmbedUrl('https://example.com/video.mp4'))
                ->toBe('https://example.com/video.mp4');
        });

        it('returns null for invalid URLs', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->getEmbedUrl('not a url'))->toBeNull();
        });
    });

    describe('HTML Generation', function () {
        it('generates safe YouTube embed HTML', function () {
            $helper = app(VideoUrlHelper::class);

            $html = $helper->generateEmbedHtml('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
            expect($html)->toContain('<iframe');
            expect($html)->toContain('youtube.com/embed/dQw4w9WgXcQ');
            expect($html)->toContain('frameborder="0"');
            expect($html)->toContain('allowfullscreen');
        });

        it('generates safe Vimeo embed HTML', function () {
            $helper = app(VideoUrlHelper::class);

            $html = $helper->generateEmbedHtml('https://vimeo.com/123456789');
            expect($html)->toContain('<iframe');
            expect($html)->toContain('player.vimeo.com/video/123456789');
            expect($html)->toContain('allowfullscreen');
        });

        it('generates HTML5 video tag for direct URLs', function () {
            $helper = app(VideoUrlHelper::class);

            $html = $helper->generateEmbedHtml('https://example.com/video.mp4');
            expect($html)->toContain('<video');
            expect($html)->toContain('controls');
            expect($html)->toContain('<source');
            expect($html)->toContain('type="video/mp4"');
        });

        it('escapes URLs in HTML', function () {
            $helper = app(VideoUrlHelper::class);

            $html = $helper->generateEmbedHtml('https://example.com/video.mp4?param=<script>');
            // URL should be escaped to prevent XSS
            expect($html)->not->toContain('<script>');
        });

        it('returns null for invalid URLs', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->generateEmbedHtml('invalid url'))->toBeNull();
        });
    });

    describe('Provider Detection', function () {
        it('detects YouTube provider', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->getProviderType('https://www.youtube.com/watch?v=dQw4w9WgXcQabc'))
                ->toBe('youtube');
        });

        it('detects Vimeo provider', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->getProviderType('https://vimeo.com/123456'))
                ->toBe('vimeo');
        });

        it('detects direct video provider', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->getProviderType('https://example.com/video.mp4'))
                ->toBe('direct');
        });

        it('returns null for unknown provider', function () {
            $helper = app(VideoUrlHelper::class);

            expect($helper->getProviderType('https://example.com/unknown'))
                ->toBeNull();
        });
    });
});

describe('Video Section Storage and Rendering', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);
    });

    it('saves video section with URL', function () {
        $this->actingAs($this->user)
            ->postJson('/articles/'.$this->article->id.'/sections', [
                'sections' => [
                    [
                        'type' => 'video',
                        'content' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQabc',
                    ],
                ],
            ])
            ->assertJsonPath('success', true);

        expect($this->article->sections()->where('type', 'video')->count())->toBe(1);
        expect($this->article->sections()->where('type', 'video')->first()->content)
            ->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQabc');
    });

    it('saves multiple video types in one article', function () {
        $this->actingAs($this->user)
            ->postJson('/articles/'.$this->article->id.'/sections', [
                'sections' => [
                    ['type' => 'text', 'content' => 'Text content'],
                    ['type' => 'video', 'content' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQabc'],
                    ['type' => 'video', 'content' => 'https://vimeo.com/123456789'],
                ],
            ])
            ->assertJsonPath('success', true);

        expect($this->article->sections()->count())->toBe(3);
        expect($this->article->sections()->where('type', 'video')->count())->toBe(2);
    });

    it('retrieves video sections with original URL', function () {
        $videoUrl = 'https://www.youtube.com/watch?v=dQw4w9WgXcQabc';

        $this->article->sections()->create([
            'type' => 'video',
            'content' => $videoUrl,
            'order' => 1,
        ]);

        $this->actingAs($this->user)
            ->getJson('/articles/'.$this->article->id)
            ->assertJsonPath('sections.0.type', 'video')
            ->assertJsonPath('sections.0.content', $videoUrl);
    });
});
