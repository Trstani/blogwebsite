<?php

use App\Helpers\ImageHelper;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FileUploadController;
use App\Jobs\DeleteCloudinaryImageJob;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use App\Services\LocalFileStorageService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    $featured = Article::where('status', 'published')
        ->where('is_featured', true)
        ->with('category', 'author')
        ->latest('updated_at')
        ->first();

    $articles = Article::where('status', 'published')
        ->where('is_featured', true)
        ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
        ->with('category', 'author')
        ->latest()
        ->take(4)
        ->get();

    $trendingArticles = Article::where('status', 'published')
        ->with('category', 'author')
        ->orderByDesc('views')
        ->take(5)
        ->get();

    $recentArticles = Article::where('status', 'published')
        ->with('category', 'author')
        ->latest()
        ->take(6)
        ->get();

    return view(
        'MainPage.homepage',
        compact(
            'featured',
            'articles',
            'trendingArticles',
            'recentArticles'
        )
    );
})->name('home');


// Authentication page
Route::get('/auth', function () {
    return view('MainPage.authpage');
})->name('auth');


// Explore page
Route::get('/explore', function () {
    $articles = Article::where('status', 'published')
        ->with('category', 'author')
        ->get()
        ->map(fn ($a) => (object) [
            'id' => $a->id,
            'title' => $a->title,
            'slug' => $a->slug,
            'category' => $a->category->slug ?? '',
            'categoryName' => $a->category->name ?? '',
            'description' => $a->description,
            'author' => $a->author->name ?? '',
            'date' => Carbon::parse(
                $a->published_at ?? $a->created_at
            )->format('M d, Y'),
            'thumbnail' => ImageHelper::imageUrl($a->cover_image),
            'views' => $a->views,
        ])
        ->values();

    $categories = Category::all();

    return view(
        'MainPage.explorepage',
        compact('articles', 'categories')
    );
})->name('explore');


/*
|--------------------------------------------------------------------------
| OTP Verification
|--------------------------------------------------------------------------
*/

// Display OTP verification page
Route::get('/verify-otp', function (Request $request) {
    return view('MainPage.verifypage', [
        'email' => $request->query('email'),
    ]);
})->name('verify-otp');


// Process OTP verification
Route::post('/verify-otp', [RegisterController::class, 'verifyOtp'])
    ->name('verify-otp.post');

    // Resend OTP
Route::post('/resend-otp', [RegisterController::class, 'resendOtp'])
    ->name('resend-otp');

/*
|--------------------------------------------------------------------------
| Article Reading
|--------------------------------------------------------------------------
*/

Route::get('/blog/{slug}', function ($slug) {
    $article = Article::where('slug', $slug)
        ->with(
            'sections',
            'category',
            'author',
            'comments.user',
            'comments.replies.user'
        )
        ->firstOrFail();

    // Public hanya bisa lihat published
    if ($article->status !== 'published') {
        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            abort(404);
        }
    }

    // Track views hanya untuk published
    if ($article->status === 'published') {
        $viewedKey = 'viewed_articles';
        $viewed = session()->get($viewedKey, []);

        if (! in_array($article->id, $viewed)) {
            $article->increment('views');

            $viewed[] = $article->id;
            session()->put($viewedKey, $viewed);
        }
    }

    return view(
        'MainPage.readingpage',
        compact('article')
    );
})->name('article.read');


/*
|--------------------------------------------------------------------------
| Profile
|--------------------------------------------------------------------------
*/

// Public profile
Route::get('/profile/{slug}', function ($slug) {
    $user = User::where('slug', $slug)->firstOrFail();

    $articles = Article::where('author_id', $user->id)
        ->where('status', 'published')
        ->with('category')
        ->latest()
        ->get();

    $isOwner = auth()->check() && auth()->id() === $user->id;

    return view(
        'MainPage.profilepage',
        compact('user', 'articles', 'isOwner')
    );
})->name('profile');


// Upload avatar
Route::post('/profile/{slug}/avatar', function (
    Request $request,
    $slug
) {
    $user = User::where('slug', $slug)->firstOrFail();

    if (auth()->id() !== $user->id) {
        abort(403);
    }

    $request->validate([
        'image_url' => 'required|string',
        'public_id' => 'nullable|string',
    ]);

    // Update user.
    // UserObserver handles cleanup of the previous avatar.
    $user->update([
        'avatar' => $request->image_url,
        'avatar_public_id' => $request->public_id,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Avatar updated!',
    ]);
})
    ->name('profile.avatar')
    ->middleware('auth');


// Delete avatar
Route::delete('/profile/{slug}/avatar', function ($slug) {
    $user = User::where('slug', $slug)->firstOrFail();

    if (auth()->id() !== $user->id) {
        abort(403);
    }

    if ($user->avatar) {
        $originalAvatar = $user->avatar;
        $originalPublicId = $user->avatar_public_id;

        $fileService = app(LocalFileStorageService::class);

        if ($fileService->isLocalPath($originalAvatar)) {
            // Local file: delete immediately.
            $fileService->delete($originalAvatar);

            Log::info(
                'Deleted local avatar on avatar.delete route',
                [
                    'user_id' => $user->id,
                    'path' => $originalAvatar,
                ]
            );
        } elseif ($originalPublicId) {
            // Legacy Cloudinary file: queue deletion.
            dispatch(
                new DeleteCloudinaryImageJob(
                    $originalPublicId,
                    'user_avatar'
                )
            );

            Log::info(
                'Queued Cloudinary avatar deletion on avatar.delete route',
                [
                    'user_id' => $user->id,
                    'public_id' => $originalPublicId,
                ]
            );
        }

        // Clear avatar fields.
        $user->update([
            'avatar' => null,
            'avatar_public_id' => null,
        ]);
    }

    return back()->with('success', 'Avatar removed!');
})
    ->name('profile.avatar.delete')
    ->middleware('auth');


// Update bio
Route::put('/profile/{slug}/bio', function (
    Request $request,
    $slug
) {
    $user = User::where('slug', $slug)->firstOrFail();

    if (auth()->id() !== $user->id) {
        abort(403);
    }

    $request->validate([
        'bio' => 'nullable|string|max:500',
    ]);

    $user->update([
        'bio' => $request->bio,
    ]);

    return back()->with('success', 'Bio updated!');
})
    ->name('profile.bio')
    ->middleware('auth');


/*
|--------------------------------------------------------------------------
| Article API
|--------------------------------------------------------------------------
*/

Route::get('/articles/{id}', [ArticleController::class, 'getArticle'])
    ->name('articles.get');


/*
|--------------------------------------------------------------------------
| Local File Uploads
|--------------------------------------------------------------------------
|
| These endpoints store uploaded files on the local server.
|
*/

Route::middleware('auth')->group(function () {

    Route::post(
        '/local-upload/image',
        [FileUploadController::class, 'uploadImage']
    )->name('local.upload.image');

    Route::post(
        '/local-upload/cover',
        [FileUploadController::class, 'uploadCover']
    )->name('local.upload.cover');

    Route::post(
        '/local-upload/avatar',
        [FileUploadController::class, 'uploadAvatar']
    )->name('local.upload.avatar');

    Route::post(
        '/local-upload/gif',
        [FileUploadController::class, 'uploadGif']
    )->name('local.upload.gif');
});

Route::get('/about', function () {
    $featured = Article::where('status', 'published')
        ->where('is_featured', true)
        ->with('category', 'author')
        ->latest('updated_at')
        ->first();

    $articles = Article::where('status', 'published')
        ->where('is_featured', true)
        ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
        ->with('category', 'author')
        ->latest()
        ->take(4)
        ->get();

    return view('MainPage.about', compact('featured', 'articles'));
})->name('about');