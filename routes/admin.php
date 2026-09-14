<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/admin/dashboard', function () {
    $user = auth()->user();

    $totalUsers = User::count();
    $onlineUsers = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();
    $totalArticles = Article::count();
    $published = Article::where('status', 'published')->count();
    $pending = Article::where('status', 'pending')->count();

    $pendingByCategory = Article::where('status', 'pending')
        ->with('category', 'author')
        ->get()
        ->groupBy(fn ($a) => $a->category->name ?? 'Uncategorized');

    $filterCategory = request('category');

    if ($filterCategory) {
        $pendingArticles = Article::where('status', 'pending')
            ->whereHas('category', fn ($q) => $q->where('slug', $filterCategory))
            ->with('category', 'author')
            ->latest()
            ->get();
    } else {
        $pendingArticles = Article::where('status', 'pending')
            ->with('category', 'author')
            ->latest()
            ->get();
    }

    $categories = Category::all();
    $writers = $user->isAdmin() ? User::where('role', 'writer')->get() : collect();
    $admins = $user->role === 'super_admin' ? User::where('role', 'admin')->get() : collect();

    return view('MainPage.admindashboard', compact(
        'totalUsers', 'onlineUsers', 'totalArticles', 'published', 'pending',
        'pendingByCategory', 'pendingArticles', 'categories', 'writers', 'admins',
        'filterCategory'
    ));
})->name('admin.dashboard')->middleware('auth');

Route::post('/admin/articles/{article}/approve', function (Article $article) {
    $article->status = 'published';
    $article->published_at = now();
    $article->save();

    return back()->with('success', 'Article published!');
})->name('admin.approve')->middleware('auth');

Route::post('/admin/articles/{article}/reject', function (Request $request, Article $article) {
    $request->validate([
        'admin_notes' => 'required|string|max:1000',
    ]);

    $article->status = 'rejected';
    $article->admin_notes = $request->admin_notes;
    $article->save();

    return back()->with('success', 'Article rejected with feedback.');
})->name('admin.reject')->middleware('auth');

Route::post('/admin/users/{user}/promote', function (User $user) {
    if (auth()->user()->role !== 'super_admin') {
        abort(403);
    }
    $user->role = 'admin';
    $user->save();

    return back()->with('success', $user->name.' promoted to admin.');
})->name('admin.promote')->middleware('auth');

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
})->name('admin.demote')->middleware('auth');

Route::post('/admin/articles/{article}/feature', function (Article $article) {
    $article->is_featured = ! $article->is_featured;
    $article->save();
    $status = $article->is_featured ? 'Added to featured' : 'Removed from featured';

    return back()->with('success', $status);
})->name('admin.feature')->middleware('auth');
