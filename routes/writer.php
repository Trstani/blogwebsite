<?php

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Route;

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

// Edit existing article
Route::get('/writer/write/{id}', function ($id) {

    $article = Article::where('id', $id)
        ->where('author_id', auth()->id())
        ->firstOrFail();

    return view('MainPage.writingpage', compact('article'));

})->name('writer.edit')->middleware('auth');