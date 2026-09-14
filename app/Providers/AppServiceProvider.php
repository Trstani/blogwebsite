<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\ArticleSection;
use App\Models\User;
use App\Observers\ArticleObserver;
use App\Observers\ArticleSectionObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

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
    }
}
