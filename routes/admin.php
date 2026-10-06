<?php

use App\Http\Controllers\Admin\TagController;
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', function () {
    $user = auth()->user();

    $totalUsers = User::count();
    $published = Article::where('status', 'published')->count();
    $pending = Article::where('status', 'pending')->count();

    $pendingByCategory = Article::where('status', 'pending')
        ->with('category', 'author')
        ->get()
        ->groupBy(fn ($a) => $a->category->name ?? 'Uncategorized');

    $categories = Category::all();

    // ========== USERS PAGINATION & SEARCH ==========
    $userSearch = request('user_search', '');
    
    $adminsQuery = User::where('role', 'admin');
    if ($userSearch) {
        $adminsQuery->where(function ($q) use ($userSearch) {
            $q->where('name', 'like', "%{$userSearch}%")
              ->orWhere('email', 'like', "%{$userSearch}%")
              ->orWhere('slug', 'like', "%{$userSearch}%");
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

    return view('MainPage.admindashboard', compact(
        'totalUsers', 'published', 'pending',
        'pendingByCategory', 'pendingArticles', 'publishedArticles', 'categories', 
        'writers', 'admins', 'filterCategory', 'userSearch', 'articleSearch'
    ));
})->name('admin.dashboard')->middleware(['auth', 'admin']);

Route::post('/admin/articles/{article}/approve', function (Article $article) {
    $article->status = 'published';
    $article->published_at = now();
    $article->save();

    return back()->with('success', 'Article published!');
})->name('admin.approve')->middleware(['auth', 'admin']);

Route::post('/admin/articles/{article}/reject', function (Request $request, Article $article) {
    $request->validate([
        'admin_notes' => 'required|string|max:1000',
    ]);

    $article->status = 'rejected';
    $article->admin_notes = $request->admin_notes;
    $article->save();

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
    $article->is_featured = ! $article->is_featured;
    $article->save();
    $status = $article->is_featured ? 'Added to featured' : 'Removed from featured';

    return back()->with('success', $status);
})->name('admin.feature')->middleware(['auth', 'admin']);

// ====== TAG MANAGER ROUTES ======
Route::resource('admin/tags', TagController::class, [
    'names' => [
        'index' => 'admin.tags.index',
        'create' => 'admin.tags.create',
        'store' => 'admin.tags.store',
        'edit' => 'admin.tags.edit',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ],
    'parameters' => [
        'tags' => 'tag',
    ],
])->middleware(['auth', 'admin']);
