<?php

use App\Http\Controllers\Admin\TagController;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', function () {
    $user = auth()->user();
    $activeTab = request('tab', 'overview');

    $totalUsers = User::count();
    $published = Article::where('status', 'published')->count();
    $pending = Article::where('status', 'pending')->count();

    $pendingByCategory = Article::where('status', 'pending')
        ->with('category', 'author')
        ->get()
        ->groupBy(fn ($a) => $a->category->name ?? 'Uncategorized');

    $categories = Category::all();

    // ========== USERS PAGINATION & SEARCH ==========
    $userType = request('user_type', 'users');
    $userSearch = request('user_search', '');
    $adminSearch = request('admin_search', '');
    
    $adminsQuery = User::where('role', 'admin');
    if ($adminSearch) {
        $adminsQuery->where(function ($q) use ($adminSearch) {
            $q->where('name', 'like', "%{$adminSearch}%")
              ->orWhere('email', 'like', "%{$adminSearch}%")
              ->orWhere('slug', 'like', "%{$adminSearch}%");
        });
    }
    $admins = $user->role === 'super_admin' ? $adminsQuery->paginate(15, ['*'], 'admins_page') : collect();

    $writersQuery = User::where('role', 'writer');
    if ($userSearch) {
        $writersQuery->where(function ($q) use ($userSearch) {
            $q->where('name', 'like', "%{$userSearch}%")
              ->orWhere('email', 'like', "%{$userSearch}%")
              ->orWhere('slug', 'like', "%{$userSearch}%");
        });
    }
    $writers = $user->isAdmin() ? $writersQuery->paginate(15, ['*'], 'writers_page') : collect();

    // ========== ARTICLES PAGINATION & SEARCH ==========
    $filterCategory = request('category', '');
    $articleSearch = request('article_search', '');

    // Pending articles
    $pendingQuery = Article::where('status', 'pending')
        ->with('category', 'author');
    
    if ($filterCategory) {
        $pendingQuery->whereHas('category', fn ($q) => $q->where('slug', $filterCategory));
    }
    
    if ($articleSearch) {
        $pendingQuery->where('title', 'like', "%{$articleSearch}%");
    }
    
    $pendingArticles = $pendingQuery->latest()->paginate(10, ['*'], 'pending_page');

    // Published articles
    $publishedQuery = Article::where('status', 'published')
        ->with('category', 'author');
    
    if ($articleSearch) {
        $publishedQuery->where('title', 'like', "%{$articleSearch}%");
    }
    
    $publishedArticles = $publishedQuery->latest()->paginate(10, ['*'], 'published_page');

    // ========== TAXONOMY (TAGS) ==========
    $tags = Tag::withCount('articles')
        ->orderBy('name')
        ->get();

    return view('MainPage.admindashboard', compact(
        'totalUsers', 'published', 'pending',
        'pendingByCategory', 'pendingArticles', 'publishedArticles', 'categories', 
        'writers', 'admins', 'filterCategory', 'userSearch', 'articleSearch', 'adminSearch',
        'activeTab', 'tags', 'userType'
    ));
})->name('admin.dashboard')->middleware(['auth', 'admin']);

Route::post('/admin/articles/{article}/approve', function (Article $article) {
    $article->status = 'published';
    $article->published_at = now();
    $article->save();

    // Notify author if article was just published
    if ($article->wasChanged('status')) {
        $article->author->notify(new \App\Notifications\ArticleApprovedNotification($article));
    }

    return back()->with('success', 'Article published!');
})->name('admin.approve')->middleware(['auth', 'admin']);

Route::post('/admin/articles/{article}/reject', function (Request $request, Article $article) {
    $request->validate([
        'admin_notes' => 'required|string|max:1000',
    ]);

    $article->status = 'rejected';
    $article->admin_notes = $request->admin_notes;
    $article->save();

    // Notify author if article was just rejected
    if ($article->wasChanged('status')) {
        $article->author->notify(new \App\Notifications\ArticleRejectedNotification($article));
    }

    return back()->with('success', 'Article rejected with feedback.');
})->name('admin.reject')->middleware(['auth', 'admin']);

Route::post('/admin/users/{user}/promote', function (User $user) {
    if (auth()->user()->role !== 'super_admin') {
        abort(403);
    }
    $user->role = 'admin';
    $user->save();

    return back()->with('success', $user->name.' promoted to admin.');
})->name('admin.promote')->middleware(['auth', 'admin']);

Route::post('/admin/users/{user}/demote', function (User $user) {
    if (auth()->user()->role !== 'super_admin') {
        abort(403);
    }
    
    // Super Admin cannot demote themselves
    if (auth()->id() === $user->id) {
        return back()->with('error', 'You cannot demote yourself.');
    }
    
    // Super Admin users cannot be demoted
    if ($user->role === 'super_admin') {
        return back()->with('error', 'Super Admin users cannot be demoted.');
    }
    
    $user->role = 'writer';
    $user->save();

    return back()->with('success', $user->name.' demoted to writer.');
})->name('admin.demote')->middleware(['auth', 'admin']);


Route::post('/admin/articles/{article}/feature', function (Article $article) {
    // Unfeature is always allowed, even when the limit is reached.
    if ($article->is_featured) {
        $article->is_featured = false;
        $article->save();

        return back()->with('success', 'Removed from featured.');
    }

    // Count currently featured articles.
    $featuredCount = Article::where('is_featured', true)->count();

    // Prevent adding a sixth featured article.
    if ($featuredCount >= 5) {
        return back()->with(
            'error',
            'You can only feature up to 5 articles. Unfeature an existing article first.'
        );
    }

    $article->is_featured = true;
    $article->save();

    return back()->with('success', 'Added to featured.');
})->name('admin.feature')->middleware(['auth', 'admin']);


// ====== TAG MANAGER ROUTES ======
// Only store, update, and destroy are used by the AJAX modal in the dashboard
// (index, create, edit were removed with the migration to modal-based management)
Route::resource('admin/tags', TagController::class, [
    'names' => [
        'store' => 'admin.tags.store',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ],
    'parameters' => [
        'tags' => 'tag',
    ],
    'only' => ['store', 'update', 'destroy'],
])->middleware(['auth', 'admin']);
