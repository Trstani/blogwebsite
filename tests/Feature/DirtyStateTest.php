<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

/**
 * PHASE 6 - DIRTY STATE TESTS
 *
 * Tests for the dirty state tracking system on the Writing Page:
 * - Initialization: Fresh load should have isDirty = false
 * - Edit detection: Any user edit should set isDirty = true
 * - Save behavior: Successful save should set isDirty = false
 * - Failed save: Failed save should preserve isDirty = true
 * - Navigation: Unsaved changes should warn before leaving
 *
 * Note: These are integration/feature tests that verify the backend behavior.
 * The JavaScript state tracking is verified through browser testing (Tasks #15-19).
 */

describe('DIRTY STATE - ARTICLE CREATION', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    });

    it('creates new article in draft status', function () {
        $response = $this->actingAs($this->user)->post('/articles', [
            'title' => 'Test Article',
            'description' => 'Test description',
            'category_id' => $this->category->id,
        ]);

        expect($response->json('success'))->toBeTrue();
        expect($response->json('article_id'))->toBeTruthy();

        $article = Article::find($response->json('article_id'));
        expect($article->status)->toBe('draft');
        expect($article->title)->toBe('Test Article');
    });

    it('new article has no sections initially', function () {
        $response = $this->actingAs($this->user)->post('/articles', [
            'title' => 'Test Article',
            'description' => 'Test',
            'category_id' => $this->category->id,
        ]);

        $article = Article::find($response->json('article_id'));
        expect($article->sections()->count())->toBe(0);
    });
});

describe('DIRTY STATE - ARTICLE LOADING', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
            'title' => 'Test Article',
            'description' => 'Test description',
        ]);
    });

    it('loads article data correctly', function () {
        $response = $this->actingAs($this->user)
            ->get("/articles/{$this->article->id}");

        expect($response->json('success'))->toBeTrue();
        expect($response->json('article_id'))->toBe($this->article->id);
        expect($response->json('title'))->toBe('Test Article');
        expect($response->json('description'))->toBe('Test description');
    });

    it('loads article with sections', function () {
        // Add text section
        $this->article->sections()->create([
            'type' => 'text',
            'content' => 'Sample text content',
            'order' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/articles/{$this->article->id}");

        expect($response->json('success'))->toBeTrue();
        expect(count($response->json('sections')))->toBe(1);
        expect($response->json('sections.0.type'))->toBe('text');
        expect($response->json('sections.0.content'))->toBe('Sample text content');
    });

    it('returns sections sorted by order', function () {
        for ($i = 1; $i <= 3; $i++) {
            $this->article->sections()->create([
                'type' => 'text',
                'content' => "Section $i",
                'order' => $i,
            ]);
        }

        $response = $this->actingAs($this->user)
            ->get("/articles/{$this->article->id}");

        $sections = $response->json('sections');
        expect(count($sections))->toBe(3);
        expect($sections[0]['content'])->toBe('Section 1');
        expect($sections[1]['content'])->toBe('Section 2');
        expect($sections[2]['content'])->toBe('Section 3');
    });

    it('prevents unauthorized access to article data', function () {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->get("/articles/{$this->article->id}");

        expect($response->status())->toBe(403);
    });

    it('requires authentication to load article', function () {
        $response = $this->get("/articles/{$this->article->id}");

        expect($response->status())->toBe(403);
    });
});

describe('DIRTY STATE - SECTION MANAGEMENT', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);
    });

    it('saves sections with proper structure', function () {
        $response = $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'First section text',
                    ],
                    [
                        'type' => 'image',
                        'content' => 'uploads/articles/sections/image.jpg',
                        'public_id' => 'img-public-id',
                    ],
                ],
            ]
        );

        expect($response->json('success'))->toBeTrue();

        expect($this->article->sections()->count())->toBe(2);
        expect($this->article->sections()->first()->type)->toBe('text');
        expect($this->article->sections()->first()->order)->toBe(1);
        $sections = $this->article->sections()->get();
        expect($sections[1]->type)->toBe('image');
        expect($sections[1]->order)->toBe(2);
    });

    it('replaces previous sections when saving', function () {
        // Create initial sections
        $this->article->sections()->create([
            'type' => 'text',
            'content' => 'Old section',
            'order' => 1,
        ]);

        expect($this->article->sections()->count())->toBe(1);

        // Save new sections
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'New section 1',
                    ],
                    [
                        'type' => 'text',
                        'content' => 'New section 2',
                    ],
                ],
            ]
        );

        expect($this->article->sections()->count())->toBe(2);
        expect($this->article->sections()->orderBy('order')->first()->content)->toBe('New section 1');
    });

    it('preserves article id and status when saving sections', function () {
        $originalId = $this->article->id;
        $originalStatus = $this->article->status;

        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Test content'],
                ],
            ]
        );

        $this->article->refresh();
        expect($this->article->id)->toBe($originalId);
        expect($this->article->status)->toBe($originalStatus);
    });

    it('prevents unauthorized section updates', function () {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'Test',
                    ],
                ],
            ]
        );

        expect($response->status())->toBe(403);
    });
});

describe('DIRTY STATE - SAVE AND SUBMIT WORKFLOW', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);
    });

    it('article remains draft after saving sections', function () {
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    [
                        'type' => 'text',
                        'content' => 'Test content',
                    ],
                ],
            ]
        );

        $this->article->refresh();
        expect($this->article->status)->toBe('draft');
    });

    it('submits article from draft to pending', function () {
        // Add sections first
        $this->article->sections()->create([
            'type' => 'text',
            'content' => 'Test content',
            'order' => 1,
        ]);

        $response = $this->actingAs($this->user)
            ->post("/articles/{$this->article->id}/submit");

        expect($response->json('success'))->toBeTrue();

        $this->article->refresh();
        expect($this->article->status)->toBe('pending');
    });

    it('prevents unauthorized article submission', function () {
        $otherUser = User::factory()->create();

        $response = $this->actingAs($otherUser)
            ->post("/articles/{$this->article->id}/submit");

        expect($response->status())->toBe(403);
    });
});

describe('DIRTY STATE - VALIDATION AND CONSTRAINTS', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    });

    it('creates articles successfully when data is valid', function () {
        $response = $this->actingAs($this->user)->post('/articles', [
            'title' => 'Test Article',
            'description' => 'Test description',
            'category_id' => $this->category->id,
        ]);

        expect($response->json('success'))->toBeTrue();
        expect($response->json('article_id'))->toBeTruthy();
    });

    it('allows optional description', function () {
        $response = $this->actingAs($this->user)->post('/articles', [
            'title' => 'Test Article',
            'category_id' => $this->category->id,
        ]);

        expect($response->json('success'))->toBeTrue();
    });

    it('allows empty string for optional description', function () {
        $response = $this->actingAs($this->user)->post('/articles', [
            'title' => 'Test Article',
            'description' => '',
            'category_id' => $this->category->id,
        ]);

        expect($response->json('success'))->toBeTrue();
        $article = Article::find($response->json('article_id'));
        // Empty string is stored as null in database
        expect($article->description)->toBeNull();
    });
});

describe('DIRTY STATE - MULTIPLE EDIT AND SAVE CYCLES', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);
    });

    it('allows multiple save cycles', function () {
        for ($cycle = 1; $cycle <= 3; $cycle++) {
            $response = $this->actingAs($this->user)->post(
                "/articles/{$this->article->id}/sections",
                [
                    'sections' => [
                        [
                            'type' => 'text',
                            'content' => "Content cycle $cycle",
                        ],
                    ],
                ]
            );

            expect($response->json('success'))->toBeTrue();
        }

        $this->article->refresh();
        expect($this->article->sections()->first()->content)->toBe('Content cycle 3');
    });

    it('preserves section order across multiple saves', function () {
        // First save
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Section 1'],
                    ['type' => 'text', 'content' => 'Section 2'],
                ],
            ]
        );

        // Second save with different order
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Section 2'],
                    ['type' => 'text', 'content' => 'Section 1'],
                ],
            ]
        );

        $sections = $this->article->sections()->orderBy('order')->get();
        expect($sections->first()->content)->toBe('Section 2');
        expect($sections->last()->content)->toBe('Section 1');
    });

    it('handles empty then full content lifecycle', function () {
        // Create with empty article
        expect($this->article->sections()->count())->toBe(0);

        // Add first section
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Content'],
                ],
            ]
        );

        expect($this->article->sections()->count())->toBe(1);

        // Expand to multiple sections
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Section 1'],
                    ['type' => 'text', 'content' => 'Section 2'],
                    ['type' => 'text', 'content' => 'Section 3'],
                ],
            ]
        );

        expect($this->article->sections()->count())->toBe(3);

        // Reduce back to one
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Final section'],
                ],
            ]
        );

        expect($this->article->sections()->count())->toBe(1);
        expect($this->article->sections()->first()->content)->toBe('Final section');
    });
});

describe('DIRTY STATE - TIMING AND UPDATES', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);
    });

    it('article maintains proper state after section saves', function () {
        // Initial state
        expect($this->article->status)->toBe('draft');
        expect($this->article->sections()->count())->toBe(0);

        // Save sections
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Test content'],
                ],
            ]
        );

        $this->article->refresh();

        // After save, article should still be draft
        expect($this->article->status)->toBe('draft');
        expect($this->article->sections()->count())->toBe(1);
    });

    it('maintains article state across multiple operations', function () {
        // Create
        expect($this->article->created_at)->not->toBeNull();

        // Save sections
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Content 1'],
                ],
            ]
        );

        $createdAtAfterFirstSave = $this->article->created_at;

        // Save again
        $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Content 2'],
                ],
            ]
        );

        $this->article->refresh();

        // created_at should not change
        expect($this->article->created_at->timestamp)
            ->toBe($createdAtAfterFirstSave->timestamp);
    });
});

describe('DIRTY STATE - ERROR RECOVERY', function () {
    beforeEach(function () {
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->article = Article::factory()->create([
            'author_id' => $this->user->id,
            'category_id' => $this->category->id,
            'status' => 'draft',
        ]);
    });

    it('maintains article state after multiple saves', function () {
        // Save valid sections first time
        $response1 = $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'First save'],
                ],
            ]
        );

        expect($response1->json('success'))->toBeTrue();

        // Save different content second time
        $response2 = $this->actingAs($this->user)->post(
            "/articles/{$this->article->id}/sections",
            [
                'sections' => [
                    ['type' => 'text', 'content' => 'Second save'],
                ],
            ]
        );

        expect($response2->json('success'))->toBeTrue();

        // Latest content should be saved
        expect($this->article->sections()->first()->content)->toBe('Second save');
    });

    it('article remains submittable after multiple saves', function () {
        // Save sections multiple times
        for ($i = 1; $i <= 3; $i++) {
            $this->actingAs($this->user)->post(
                "/articles/{$this->article->id}/sections",
                [
                    'sections' => [
                        ['type' => 'text', 'content' => "Version $i"],
                    ],
                ]
            );
        }

        // Should still be able to submit
        $response = $this->actingAs($this->user)
            ->post("/articles/{$this->article->id}/submit");

        expect($response->json('success'))->toBeTrue();
        $this->article->refresh();
        expect($this->article->status)->toBe('pending');
    });
});
