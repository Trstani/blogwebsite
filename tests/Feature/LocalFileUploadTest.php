<?php

use App\Models\User;
use App\Services\LocalFileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

describe('LocalFileStorageService', function () {
    describe('File Storage', function () {
        it('stores a valid jpeg file', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->image('test.jpg', 100, 100);
            $result = $service->store($file, 'articles', 'sections');

            expect($result)->toHaveKeys(['path', 'url']);
            expect($result['path'])->toMatch('/^uploads\/articles\/sections\/\d+-[a-zA-Z0-9]{12}\.jpg$/');
            expect($result['url'])->toMatch('/^\/storage\/uploads\/articles\/sections\/\d+-[a-zA-Z0-9]{12}\.jpg$/');

            Storage::disk('public')->assertExists($result['path']);
        });

        it('stores a valid png file', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->image('test.png', 100, 100, 'png');
            $result = $service->store($file, 'articles', 'covers');

            expect($result['path'])->toMatch('/^uploads\/articles\/covers\/\d+-[a-zA-Z0-9]{12}\.png$/');
            Storage::disk('public')->assertExists($result['path']);
        });

        it('stores a valid webp file', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->image('test.webp', 100, 100, 'webp');
            $result = $service->store($file, 'users', 'avatars');

            expect($result['path'])->toMatch('/^uploads\/users\/avatars\/\d+-[a-zA-Z0-9]{12}\.webp$/');
            Storage::disk('public')->assertExists($result['path']);
        });

        it('generates unique filenames', function () {
            $service = app(LocalFileStorageService::class);
            $file1 = UploadedFile::fake()->image('test.jpg', 100, 100);
            $file2 = UploadedFile::fake()->image('test.jpg', 100, 100);

            $result1 = $service->store($file1, 'articles', 'sections');
            $result2 = $service->store($file2, 'articles', 'sections');

            expect($result1['path'])->not->toBe($result2['path']);
        });

        it('rejects invalid file type', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->create('test.txt', 100, 'text/plain');

            expect(fn () => $service->store($file, 'articles', 'sections'))
                ->toThrow(InvalidArgumentException::class, 'not allowed');
        });

        it('rejects oversized file', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->create('test.jpg', 11 * 1024 * 1024, 'image/jpeg');

            expect(fn () => $service->store($file, 'articles', 'sections'))
                ->toThrow(InvalidArgumentException::class, 'exceeds maximum');
        });

        it('rejects invalid category', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->image('test.jpg', 100, 100);

            expect(fn () => $service->store($file, 'invalid', 'sections'))
                ->toThrow(InvalidArgumentException::class, 'Invalid category');
        });

        it('rejects invalid subcategory', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->image('test.jpg', 100, 100);

            expect(fn () => $service->store($file, 'articles', 'invalid'))
                ->toThrow(InvalidArgumentException::class, 'Invalid subcategory');
        });
    });

    describe('File Deletion', function () {
        it('deletes an existing file', function () {
            $service = app(LocalFileStorageService::class);
            $file = UploadedFile::fake()->image('test.jpg', 100, 100);
            $result = $service->store($file, 'articles', 'sections');

            Storage::disk('public')->assertExists($result['path']);

            $deleted = $service->delete($result['path']);

            expect($deleted)->toBeTrue();
            Storage::disk('public')->assertMissing($result['path']);
        });

        it('safely handles nonexistent file', function () {
            $service = app(LocalFileStorageService::class);
            $deleted = $service->delete('uploads/articles/sections/nonexistent.jpg');

            expect($deleted)->toBeFalse();
        });

        it('rejects path traversal attempts', function () {
            $service = app(LocalFileStorageService::class);
            $deleted = $service->delete('../../.env');

            expect($deleted)->toBeFalse();
        });

        it('rejects invalid storage paths', function () {
            $service = app(LocalFileStorageService::class);
            $deleted = $service->delete('random/path/file.jpg');

            expect($deleted)->toBeFalse();
        });
    });

    describe('Path Detection', function () {
        it('recognizes local storage paths', function () {
            $service = app(LocalFileStorageService::class);

            expect($service->isLocalPath('uploads/articles/covers/example.webp'))->toBeTrue();
            expect($service->isLocalPath('uploads/articles/sections/example.png'))->toBeTrue();
            expect($service->isLocalPath('uploads/users/avatars/example.jpg'))->toBeTrue();
        });

        it('recognizes public URLs as local paths', function () {
            $service = app(LocalFileStorageService::class);

            expect($service->isLocalPath('/storage/uploads/articles/covers/example.webp'))->toBeTrue();
            expect($service->isLocalPath('/storage/uploads/users/avatars/example.png'))->toBeTrue();
        });

        it('rejects cloudinary urls', function () {
            $service = app(LocalFileStorageService::class);

            expect($service->isLocalPath('https://res.cloudinary.com/kjkvm9vj/image/upload/v123/abc.jpg'))
                ->toBeFalse();
            expect($service->isLocalPath('http://res.cloudinary.com/kjkvm9vj/image/upload/v123/abc.jpg'))
                ->toBeFalse();
        });

        it('rejects invalid paths', function () {
            $service = app(LocalFileStorageService::class);

            expect($service->isLocalPath('random/path/file.jpg'))->toBeFalse();
            expect($service->isLocalPath('../../.env'))->toBeFalse();
            expect($service->isLocalPath(null))->toBeFalse();
            expect($service->isLocalPath(''))->toBeFalse();
        });
    });

    describe('Path Normalization', function () {
        it('normalizes public url to relative path', function () {
            $service = app(LocalFileStorageService::class);
            $normalized = $service->normalizePath('/storage/uploads/articles/sections/example.webp');

            expect($normalized)->toBe('uploads/articles/sections/example.webp');
        });

        it('normalizes relative paths', function () {
            $service = app(LocalFileStorageService::class);
            $normalized = $service->normalizePath('uploads/articles/covers/example.jpg');

            expect($normalized)->toBe('uploads/articles/covers/example.jpg');
        });

        it('rejects cloudinary urls', function () {
            $service = app(LocalFileStorageService::class);
            $normalized = $service->normalizePath('https://res.cloudinary.com/kjkvm9vj/image/upload/abc.jpg');

            expect($normalized)->toBeNull();
        });

        it('rejects invalid paths', function () {
            $service = app(LocalFileStorageService::class);

            expect($service->normalizePath('../../.env'))->toBeNull();
            expect($service->normalizePath('random/path/file.jpg'))->toBeNull();
            expect($service->normalizePath(''))->toBeNull();
        });
    });
});

describe('POST /local-upload/image', function () {
    it('uploads image successfully', function () {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg', 100, 100);

        $response = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'path', 'url']);
        $response->assertJson(['success' => true]);

        $path = $response->json('path');
        expect($path)->toMatch('/^uploads\/articles\/sections\/\d+-[a-zA-Z0-9]{12}\.jpg$/');

        Storage::disk('public')->assertExists($path);
    });

    it('rejects invalid file type', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/local-upload/image', [
            'file' => UploadedFile::fake()->create('test.php', 100, 'text/x-php'),
        ]);

        // Laravel validation redirects on failure for form requests
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);
    });

    it('rejects missing file', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/local-upload/image', []);

        // Laravel validation redirects on failure
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);
    });

    it('supports png files', function () {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('test.png', 100, 100, 'png');

        $response = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $path = $response->json('path');
        expect($path)->toContain('.png');
    });

    it('supports webp files', function () {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('test.webp', 100, 100, 'webp');

        $response = $this->actingAs($user)->post('/local-upload/image', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $path = $response->json('path');
        expect($path)->toContain('.webp');
    });
});

describe('POST /local-upload/cover', function () {
    it('uploads cover successfully', function () {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

        $response = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $path = $response->json('path');
        expect($path)->toMatch('/^uploads\/articles\/covers\/\d+-[a-zA-Z0-9]{12}\.jpg$/');

        Storage::disk('public')->assertExists($path);
    });

    it('rejects invalid file type', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/local-upload/cover', [
            'file' => UploadedFile::fake()->create('test.php', 100, 'text/x-php'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);
    });
});

describe('POST /local-upload/avatar', function () {
    it('uploads avatar successfully', function () {
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $response = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $path = $response->json('path');
        expect($path)->toMatch('/^uploads\/users\/avatars\/\d+-[a-zA-Z0-9]{12}\.jpg$/');

        Storage::disk('public')->assertExists($path);
    });

    it('rejects invalid file type', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/local-upload/avatar', [
            'file' => UploadedFile::fake()->create('test.php', 100, 'text/x-php'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['file']);
    });
});
