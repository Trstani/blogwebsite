<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\Tag;
use App\Models\User;
use App\Observers\ArticleObserver;
use App\Observers\ArticleSectionObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Existing Observers
        |--------------------------------------------------------------------------
        */
        Article::observe(ArticleObserver::class);
        ArticleSection::observe(ArticleSectionObserver::class);
        User::observe(UserObserver::class);

        /*
        |--------------------------------------------------------------------------
        | Popular Tags Navbar Composer
        |--------------------------------------------------------------------------
        */
        View::composer('components.navbar', function ($view) {
            $popularTags = Tag::whereHas('articles', function ($query) {
                $query->where('status', 'published');
            })
                ->withCount(['articles' => function ($query) {
                    $query->where('status', 'published');
                }])
                ->orderByDesc('articles_count')
                ->take(6)
                ->get();

            $view->with('popularTags', $popularTags);
        });
    }
}
