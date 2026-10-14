<?php
use App\Models\Tag;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

Route::get('/writer/dashboard', function () {

    $user = auth()->user();

    $articles = Article::where('author_id', $user->id)
        ->with('category')
        ->latest()
        ->get();

    $totalArticles = $articles->count();

    $published = $articles->where('status', 'published')->count();

    $pending = $articles->where('status', 'pending')->count();

    $categories = Category::orderBy('name')->get();

    return view('MainPage.writerdashboard', compact(
        'articles',
        'totalArticles',
        'published',
        'pending',
        'categories'
    ));

})->name('writer.dashboard')->middleware('auth');

Route::get('/writer/write', function () {
    return view('MainPage.writingpage');

})->name('writer.write')->middleware('auth');

// Get available tags for tag selector

Route::get('/writer/tags', function (Request $request) {
    $user = $request->user();

    abort_unless(
        $user->isWriter() || $user->isAdmin(),
        403
    );

    $tags = Tag::withCount('articles')
        ->orderBy('name')
        ->get(['id', 'name', 'slug', 'created_by']);

    return response()->json([
        'success' => true,
        'tags' => $tags,
    ]);
})->name('writer.tags')->middleware('auth');


// Edit existing article
Route::get('/writer/write/{id}', function ($id) {

    $article = Article::where('id', $id)
        ->where('author_id', auth()->id())
        ->firstOrFail();

    return view('MainPage.writingpage', compact('article'));

})->name('writer.edit')->middleware('auth');


/*
|--------------------------------------------------------------------------
| Create tag from Writing Page
|--------------------------------------------------------------------------
*/
Route::post('/writer/tags', function (Request $request) {
    $user = $request->user();

    abort_unless(
        $user->isWriter() || $user->isAdmin(),
        403
    );

    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
    ]);

    $name = trim($validated['name']);

    if ($name === '') {
        return response()->json([
            'success' => false,
            'message' => 'Tag name cannot be empty.',
        ], 422);
    }

    // Reuse an existing tag rather than creating a duplicate.
    $existingTag = Tag::whereRaw(
        'LOWER(name) = ?',
        [mb_strtolower($name)]
    )->first();

    if ($existingTag) {
        return response()->json([
            'success' => true,
            'created' => false,
            'message' => 'This tag already exists.',
            'tag' => $existingTag->only([
                'id', 'name', 'slug', 'created_by'
            ]),
        ]);
    }

    $baseSlug = Str::slug($name);

    if ($baseSlug === '') {
        return response()->json([
            'success' => false,
            'message' => 'Please enter a valid tag name.',
        ], 422);
    }

    $slug = $baseSlug;
    $suffix = 2;

    while (Tag::where('slug', $slug)->exists()) {
        $slug = $baseSlug . '-' . $suffix++;
    }

    try {
        $tag = Tag::create([
            'name' => $name,
            'slug' => $slug,
            'created_by' => $user->id,
        ]);
    } catch (\Illuminate\Database\QueryException $e) {
        // Handle a concurrent request that created the same name.
        $existingTag = Tag::whereRaw(
            'LOWER(name) = ?',
            [mb_strtolower($name)]
        )->first();

        if ($existingTag) {
            return response()->json([
                'success' => true,
                'created' => false,
                'message' => 'This tag already exists.',
                'tag' => $existingTag->only([
                    'id', 'name', 'slug', 'created_by'
                ]),
            ]);
        }

        report($e);

        return response()->json([
            'success' => false,
            'message' => 'Could not create tag. Please try again.',
        ], 409);
    }

    return response()->json([
        'success' => true,
        'created' => true,
        'message' => 'Tag created successfully.',
        'tag' => $tag->only([
            'id', 'name', 'slug', 'created_by'
        ]),
    ], 201);
})->name('writer.tags.store')->middleware('auth');

