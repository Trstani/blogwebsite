<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Models\Article;
use App\Models\ArticleSection;
use Illuminate\Support\Facades\Route;

Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
Route::get('/articles/{article}', [ArticleController::class, 'getArticle'])->name('articles.show')->middleware('auth');
Route::post('/articles/{article}/sections', [ArticleController::class, 'saveSections'])->name('articles.sections');
Route::post('/articles/{article}/submit', [ArticleController::class, 'submit'])->name('articles.submit');
Route::post('/articles/{article}/preview-video', [ArticleController::class, 'previewVideo'])->name('articles.preview-video');
Route::post('/articles/{article}/comments', [CommentController::class, 'store'])->name('articles.comments.store')->middleware('auth');
Route::post('/upload/cover', [ArticleController::class, 'uploadCover'])->name('upload.cover');
Route::post('/upload/image', [ArticleController::class, 'uploadImage'])->name('upload.image');

// Delete individual section - triggers observer to delete from Cloudinary
Route::delete('/articles/{article}/sections/{section}', function (Article $article, ArticleSection $section) {
    // Verify ownership
    if ($article->author_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    // Verify section belongs to article
    if ($section->article_id !== $article->id) {
        return response()->json(['error' => 'Section not found'], 404);
    }

    // Delete will trigger ArticleSectionObserver
    $section->delete();

    return response()->json([
        'success' => true,
        'message' => 'Section deleted and Cloudinary cleanup queued.',
    ]);
})->name('articles.sections.delete')->middleware('auth');

// Cleanup unsaved image - for sections that haven't been saved to database yet
Route::delete('/articles/{article}/unsaved-image', [ArticleController::class, 'deleteUnsavedImage'])
    ->name('articles.unsaved-image.delete')
    ->middleware('auth');

Route::delete('/articles/{article}', function (Article $article) {
    if ($article->author_id !== auth()->id()) {
        abort(403);
    }

    // Load sections relationship into memory before deletion
    // This ensures ArticleObserver can access them after sections are deleted
    $article->load('sections');

    // Delete sections using Eloquent model deletion to trigger observers
    // This ensures ArticleSectionObserver::deleted() fires for each section,
    // which queues Cloudinary deletion jobs for section images
    foreach ($article->sections as $section) {
        $section->delete();
    }

    $article->delete();

    return back()->with('success', 'Article deleted.');
})->name('articles.delete')->middleware('auth');
