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
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Article::observe(ArticleObserver::class);
        ArticleSection::observe(ArticleSectionObserver::class);
        User::observe(UserObserver::class);

        // Share popular tags with navbar component globally
        View::composer('components.navbar', function ($view) {
            $popularTags = Tag::whereHas('articles', function ($q) {
                $q->where('status', 'published');
            })
                ->withCount(['articles' => function ($q) {
                    $q->where('status', 'published');
                }])
                ->orderByDesc('articles_count')
                ->take(6)
                ->get();

            $view->with('popularTags', $popularTags);
        });
    }
}
