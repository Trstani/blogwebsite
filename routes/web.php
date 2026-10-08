<?php

use App\Helpers\ImageHelper;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Admin\LegalPageController;
use App\Http\Controllers\LikeBookmarkController;
use App\Jobs\DeleteCloudinaryImageJob;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
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
    // Featured articles query (NOT affected by search)
    $featured = Article::where('status', 'published')
        ->where('is_featured', true)
        ->withCount(['comments as discussion_count' => fn($q) => $q->whereNull('parent_id')])
        ->with('category', 'author')
        ->latest('updated_at')
        ->first();

    // Additional featured articles (NOT affected by search)
    $articles = Article::where('status', 'published')
        ->where('is_featured', true)
        ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
        ->withCount(['comments as discussion_count' => fn($q) => $q->whereNull('parent_id')])
        ->with('category', 'author')
        ->latest()
        ->take(4)
        ->get();

    // Trending articles (always static, NOT affected by search)
    $trendingArticles = Article::where('status', 'published')
        ->with('category', 'author')
        ->orderByDesc('views')
        ->take(5)
        ->get();

    // Recent articles (display-only, NOT affected by search parameter)
    $recentArticles = Article::where('status', 'published')
        ->withCount(['comments as discussion_count' => fn($q) => $q->whereNull('parent_id')])
        ->with('category', 'author')
        ->latest()
        ->take(6)
        ->get();

    // Most discussed articles (always static, NOT affected by search)
    $mostDiscussedArticles = Article::where('status', 'published')
        ->withCount(['comments as discussion_count' => fn($q) => $q->whereNull('parent_id')])
        ->with('category', 'author')
        ->orderByDesc('discussion_count')
        ->take(5)
        ->get();

    // Popular topics (only tags used by published articles)
    $popularTags = Tag::whereHas('articles', function ($q) {
        $q->where('status', 'published');
    })
        ->withCount(['articles' => function ($q) {
            $q->where('status', 'published');
        }])
        ->orderByDesc('articles_count')
        ->take(6)
        ->get();

    return view(
        'MainPage.homepage',
        compact(
            'featured',
            'articles',
            'trendingArticles',
            'recentArticles',
            'mostDiscussedArticles',
            'popularTags'
        )
    );
})->name('home');


// Authentication page
Route::get('/auth', function () {
    return view('MainPage.authpage');
})->name('auth');


// Explore page
Route::get('/explore', function (Request $request) {
    $articlesQuery = Article::where('status', 'published')
        ->withCount(['comments as discussion_count' => fn($q) => $q->whereNull('parent_id')])
        ->with('category', 'author', 'tags');

    // Filter by tag if provided
    $activeTag = null;
    $tagSlug = $request->query('tag');
    
    if ($tagSlug) {
        $activeTag = Tag::where('slug', $tagSlug)->firstOrFail();
        $articlesQuery->whereHas('tags', function ($query) use ($tagSlug) {
            $query->where('slug', $tagSlug);
        });
    }

    // Filter by category if provided (server-side)
    $categorySlug = $request->query('category');
    if ($categorySlug && $categorySlug !== 'all') {
        $articlesQuery->whereHas('category', function ($query) use ($categorySlug) {
            $query->where('slug', $categorySlug);
        });
    }

    // Filter by search term if provided (server-side)
    $searchQuery = $request->query('search');
    if ($searchQuery) {
        $searchTerm = '%' . $searchQuery . '%';
        $articlesQuery->where(function ($query) use ($searchTerm) {
            $query->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('slug', 'like', $searchTerm)
                  ->orWhereHas('tags', function ($tagQuery) use ($searchTerm) {
                      $tagQuery->where('name', 'like', $searchTerm);
                  });
        });
    }

    $articles = $articlesQuery->get()
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
            'discussion_count' => $a->discussion_count,
        ])
        ->values();

    $categories = Category::all();

    return view(
        'MainPage.explorepage',
        compact('articles', 'categories', 'activeTag', 'searchQuery')
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
            'tags',
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
Route::get('/profile/{slug}', function ($slug, Request $request) {
    $user = User::where('slug', $slug)->firstOrFail();
    $isOwner = auth()->check() && auth()->id() === $user->id;

    // Get tab parameter, validate it
    $tab = $request->query('tab', 'articles');
    $validTabs = ['articles', 'liked', 'bookmarked'];
    $tab = in_array($tab, $validTabs) ? $tab : 'articles';

    // Initialize articles variable
    $articles = null;

    if ($tab === 'articles') {
        // My Articles - show author's published articles
        $articles = Article::where('author_id', $user->id)
            ->where('status', 'published')
            ->with('category', 'author')
            ->latest()
            ->paginate(9);
    } elseif ($tab === 'liked' && $isOwner) {
        // Liked Articles - only for authenticated owner
        $articles = $user->likedArticles()
            ->where('status', 'published')
            ->with('category', 'author')
            ->latest()
            ->paginate(9);
    } elseif ($tab === 'bookmarked' && $isOwner) {
        // Bookmarked Articles - only for authenticated owner
        $articles = $user->bookmarkedArticles()
            ->where('status', 'published')
            ->with('category', 'author')
            ->latest()
            ->paginate(9);
    }

    // If accessing private tabs as non-owner, show My Articles instead
    if ($articles === null) {
        $tab = 'articles';
        $articles = Article::where('author_id', $user->id)
            ->where('status', 'published')
            ->with('category', 'author')
            ->latest()
            ->paginate(9);
    }

    return view(
        'MainPage.profilepage',
        compact('user', 'articles', 'isOwner', 'tab')
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
| Search API
|--------------------------------------------------------------------------
*/

Route::get('/api/search/suggestions', [\App\Http\Controllers\SearchController::class, 'suggestions'])
    ->name('search.suggestions');


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

    /*
    |--------------------------------------------------------------------------
    | Notification Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [
        \App\Http\Controllers\NotificationController::class, 'index'
    ])->name('notifications.index');

    Route::post('/notifications/{notification}/mark-as-read', [
        \App\Http\Controllers\NotificationController::class, 'markAsRead'
    ])->name('notifications.mark-as-read');

    Route::post('/notifications/mark-all-as-read', [
        \App\Http\Controllers\NotificationController::class, 'markAllAsRead'
    ])->name('notifications.mark-all-as-read');

    /*
    |--------------------------------------------------------------------------
    | Like & Bookmark Routes
    |--------------------------------------------------------------------------
    */

    Route::post('/articles/{article}/like', [LikeBookmarkController::class, 'toggleLike'])
        ->name('articles.like');

    Route::post('/articles/{article}/bookmark', [LikeBookmarkController::class, 'toggleBookmark'])
        ->name('articles.bookmark');

    Route::get('/articles/{article}/like-bookmark-status', [LikeBookmarkController::class, 'getStatus'])
        ->name('articles.status');
});
/*
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
*/
Route::get('/privacy-policy', function () {
    $legalPage = \App\Models\LegalPage::where('type', 'privacy_policy')
        ->firstOrFail();

    return view('MainPage.legalpage', compact('legalPage'));
})->name('privacy-policy');

Route::get('/legal-notice', function () {
    $legalPage = \App\Models\LegalPage::where('type', 'legal_notice')
        ->firstOrFail();

    return view('MainPage.legalpage', compact('legalPage'));
})->name('legal-notice');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get(
        '/legal-pages/{type}/edit',
        [LegalPageController::class, 'edit']
    )->name('legal-pages.edit');

    Route::put(
        '/legal-pages/{type}',
        [LegalPageController::class, 'update']
    )->name('legal-pages.update');
});