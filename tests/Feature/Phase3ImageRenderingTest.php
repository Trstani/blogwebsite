<?php

use App\Helpers\ImageHelper;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ImageHelper', function () {
    describe('imageUrl() function', function () {
        it('transforms local paths to storage URLs', function () {
            $url = ImageHelper::imageUrl('uploads/articles/covers/test.jpg');
            expect($url)->toBe('/storage/uploads/articles/covers/test.jpg');

            $url = ImageHelper::imageUrl('uploads/articles/sections/test.webp');
            expect($url)->toBe('/storage/uploads/articles/sections/test.webp');

            $url = ImageHelper::imageUrl('uploads/users/avatars/test.png');
            expect($url)->toBe('/storage/uploads/users/avatars/test.png');
        });

        it('leaves /storage/ prefixed paths unchanged', function () {
            $url = ImageHelper::imageUrl('/storage/uploads/articles/covers/test.jpg');
            expect($url)->toBe('/storage/uploads/articles/covers/test.jpg');

            $url = ImageHelper::imageUrl('/storage/uploads/users/avatars/test.png');
            expect($url)->toBe('/storage/uploads/users/avatars/test.png');
        });

        it('leaves Cloudinary URLs unchanged', function () {
            $cloudinaryUrl = 'https://res.cloudinary.com/example/image/upload/test.jpg';
            expect(ImageHelper::imageUrl($cloudinaryUrl))->toBe($cloudinaryUrl);

            $cloudinaryUrl = 'http://res.cloudinary.com/example/image/upload/test.webp';
            expect(ImageHelper::imageUrl($cloudinaryUrl))->toBe($cloudinaryUrl);
        });

        it('handles external URLs', function () {
            $externalUrl = 'https://example.com/image.jpg';
            expect(ImageHelper::imageUrl($externalUrl))->toBe($externalUrl);
        });

        it('returns null for null input', function () {
            expect(ImageHelper::imageUrl(null))->toBeNull();
            expect(ImageHelper::imageUrl(''))->toBeNull();
            expect(ImageHelper::imageUrl('   '))->toBeNull();
        });
    });

    describe('isLocalPath() function', function () {
        it('recognizes local storage paths', function () {
            expect(ImageHelper::isLocalPath('uploads/articles/covers/test.jpg'))->toBeTrue();
            expect(ImageHelper::isLocalPath('uploads/articles/sections/test.png'))->toBeTrue();
            expect(ImageHelper::isLocalPath('uploads/users/avatars/test.webp'))->toBeTrue();
            expect(ImageHelper::isLocalPath('/storage/uploads/articles/covers/test.jpg'))->toBeTrue();
        });

        it('rejects non-local paths', function () {
            expect(ImageHelper::isLocalPath('https://res.cloudinary.com/image.jpg'))->toBeFalse();
            expect(ImageHelper::isLocalPath('https://example.com/image.jpg'))->toBeFalse();
            expect(ImageHelper::isLocalPath('invalid/path/image.jpg'))->toBeFalse();
            expect(ImageHelper::isLocalPath(null))->toBeFalse();
            expect(ImageHelper::isLocalPath(''))->toBeFalse();
        });
    });

    describe('isCloudinaryUrl() function', function () {
        it('recognizes Cloudinary URLs', function () {
            expect(ImageHelper::isCloudinaryUrl('https://res.cloudinary.com/cloud/image/upload/v123/file.jpg'))->toBeTrue();
            expect(ImageHelper::isCloudinaryUrl('http://res.cloudinary.com/cloud/image/upload/file.png'))->toBeTrue();
        });

        it('rejects non-Cloudinary URLs', function () {
            expect(ImageHelper::isCloudinaryUrl('uploads/articles/covers/test.jpg'))->toBeFalse();
            expect(ImageHelper::isCloudinaryUrl('https://example.com/image.jpg'))->toBeFalse();
            expect(ImageHelper::isCloudinaryUrl(null))->toBeFalse();
            expect(ImageHelper::isCloudinaryUrl(''))->toBeFalse();
        });
    });
});

describe('Global imageUrl() helper function', function () {
    it('provides global imageUrl() function', function () {
        expect(function_exists('imageUrl'))->toBeTrue();
        expect(function_exists('isLocalPath'))->toBeTrue();
        expect(function_exists('isCloudinaryUrl'))->toBeTrue();
    });

    it('returns correct values from global function', function () {
        $url = imageUrl('uploads/articles/covers/test.jpg');
        expect($url)->toBe('/storage/uploads/articles/covers/test.jpg');

        $url = imageUrl('https://res.cloudinary.com/image.jpg');
        expect($url)->toBe('https://res.cloudinary.com/image.jpg');

        expect(imageUrl(null))->toBeNull();
    });
});

describe('Image Rendering on Reading Page', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'published',
            'cover_image' => 'uploads/articles/covers/test-cover.jpg',
        ]);
    });

    it('renders cover image with correct URL', function () {
        $response = $this->get('/blog/'.$this->article->slug);

        $response->assertOk();
        $response->assertSee('/storage/uploads/articles/covers/test-cover.jpg');
        // Should NOT see the raw local path
        $response->assertDontSee('src="uploads/articles/covers/');
    });

    it('renders section images with correct URL', function () {
        $this->article->sections()->create([
            'type' => 'image',
            'content' => 'uploads/articles/sections/test-image.jpg',
            'order' => 1,
        ]);

        $response = $this->get('/blog/'.$this->article->slug);

        $response->assertOk();
        $response->assertSee('/storage/uploads/articles/sections/test-image.jpg');
        // Should NOT see the raw local path
        $response->assertDontSee('src="uploads/articles/sections/');
    });

    it('renders GIF sections with correct URL', function () {
        $this->article->sections()->create([
            'type' => 'gif',
            'content' => 'uploads/articles/gifs/animation.gif',
            'order' => 1,
        ]);

        $response = $this->get('/blog/'.$this->article->slug);

        $response->assertOk();
        $response->assertSee('/storage/uploads/articles/gifs/animation.gif');
        // Should NOT see the raw local path
        $response->assertDontSee('src="uploads/articles/gifs/');
    });

    it('preserves Cloudinary URLs unchanged', function () {
        $this->article->update([
            'cover_image' => 'https://res.cloudinary.com/cloud/image/upload/test.jpg',
        ]);

        $response = $this->get('/blog/'.$this->article->slug);

        $response->assertOk();
        $response->assertSee('https://res.cloudinary.com/cloud/image/upload/test.jpg');
    });

    it('renders author avatar with correct URL', function () {
        $this->user->update([
            'avatar' => 'uploads/users/avatars/user-avatar.jpg',
        ]);

        $response = $this->get('/blog/'.$this->article->slug);

        $response->assertOk();
        $response->assertSee('/storage/uploads/users/avatars/user-avatar.jpg');
        // Should NOT see the raw local path
        $response->assertDontSee('src="uploads/users/avatars/');
    });

    it('renders Cloudinary user avatar unchanged', function () {
        $this->user->update([
            'avatar' => 'https://res.cloudinary.com/cloud/image/upload/user.jpg',
        ]);

        $response = $this->get('/blog/'.$this->article->slug);

        $response->assertOk();
        $response->assertSee('https://res.cloudinary.com/cloud/image/upload/user.jpg');
    });
});

describe('Image Rendering on Profile Page', function () {
    beforeEach(function () {
        $this->user = User::factory()->create([
            'avatar' => null,  // No avatar initially
        ]);
    });

    it('profile page loads without avatar errors', function () {
        $response = $this->get('/profile/'.$this->user->slug);

        $response->assertOk();
    });
});

describe('Backward Compatibility', function () {
    it('maintains Cloudinary functionality', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'cover_image' => 'https://res.cloudinary.com/test/image/upload/v1/cover.jpg',
            'cover_image_public_id' => 'test/cover',
        ]);

        $response = $this->get('/blog/'.$article->slug);

        $response->assertOk();
        $response->assertSee('https://res.cloudinary.com/test/image/upload/v1/cover.jpg');
    });

    it('handles mixed local and Cloudinary content', function () {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $article = Article::factory()->create([
            'author_id' => $user->id,
            'category_id' => $category->id,
            'status' => 'published',
            'cover_image' => 'uploads/articles/covers/local.jpg',
        ]);

        // Add mixed sections
        $article->sections()->create([
            'type' => 'image',
            'content' => 'uploads/articles/sections/local-image.jpg',
            'order' => 1,
        ]);

        $article->sections()->create([
            'type' => 'image',
            'content' => 'https://res.cloudinary.com/test/image/upload/v1/cloud-image.jpg',
            'image_public_id' => 'test/cloud-image',
            'order' => 2,
        ]);

        $response = $this->get('/blog/'.$article->slug);

        $response->assertOk();
        // Local cover should have /storage/ prefix
        $response->assertSee('/storage/uploads/articles/covers/local.jpg');
        // Local section should have /storage/ prefix
        $response->assertSee('/storage/uploads/articles/sections/local-image.jpg');
        // Cloudinary image should be unchanged
        $response->assertSee('https://res.cloudinary.com/test/image/upload/v1/cloud-image.jpg');
    });
});
