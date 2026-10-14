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
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
        | Login Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $ip = $request->ip();

            return [
                Limit::perMinute(5)
                    ->by('login:' . $email . '|' . $ip),

                Limit::perMinute(30)
                    ->by('login-ip:' . $ip),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Registration Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(5)
                ->by('register-ip:' . $request->ip());
        });

        /*
        |--------------------------------------------------------------------------
        | OTP Verification Rate Limiters
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('verify-otp', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $ip = $request->ip();

            return [
                Limit::perMinute(5)
                    ->by('verify-otp:' . $email . '|' . $ip),

                Limit::perMinute(20)
                    ->by('verify-otp-ip:' . $ip),
            ];
        });

        RateLimiter::for('verify-password-reset', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $ip = $request->ip();

            return [
                Limit::perMinute(5)
                    ->by('verify-password-reset:' . $email . '|' . $ip),

                Limit::perMinute(20)
                    ->by('verify-password-reset-ip:' . $ip),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | OTP Sending Rate Limiters
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('resend-otp', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $ip = $request->ip();

            return [
                Limit::perMinute(1)
                    ->by('resend-otp:' . $email . '|' . $ip),

                Limit::perHour(5)
                    ->by('resend-otp-ip:' . $ip),
            ];
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $ip = $request->ip();

            return [
                Limit::perMinute(2)
                    ->by('forgot-password:' . $email . '|' . $ip),

                Limit::perHour(5)
                    ->by('forgot-password-ip:' . $ip),
            ];
        });

        RateLimiter::for('resend-password-reset', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $ip = $request->ip();

            return [
                Limit::perMinute(1)
                    ->by('resend-password-reset:' . $email . '|' . $ip),

                Limit::perHour(5)
                    ->by('resend-password-reset-ip:' . $ip),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Password Reset Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('reset-password', function (Request $request) {
            return Limit::perMinute(5)
                ->by('reset-password-ip:' . $request->ip());
        });

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
