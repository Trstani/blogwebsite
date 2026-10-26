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

            return [
                Limit::perMinute(5)
                    ->by('login:' . $email),

                Limit::perHour(20)
                    ->by('login-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Registration Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('register', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(5)
                    ->by('register-email:' . $email),

                Limit::perHour(10)
                    ->by('register-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Registration OTP Verification Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('verify-otp', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(5)
                    ->by('verify-otp:' . $email),

                Limit::perHour(20)
                    ->by('verify-otp-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Password Reset OTP Verification Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('verify-password-reset', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(5)
                    ->by('verify-password-reset:' . $email),

                Limit::perHour(20)
                    ->by('verify-password-reset-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Registration OTP Resend Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('resend-otp', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(1)
                    ->by('resend-otp:' . $email),

                Limit::perHour(5)
                    ->by('resend-otp-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Forgot Password Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('forgot-password', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(2)
                    ->by('forgot-password:' . $email),

                Limit::perHour(5)
                    ->by('forgot-password-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Password Reset OTP Resend Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('resend-password-reset', function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));

            return [
                Limit::perMinute(1)
                    ->by('resend-password-reset:' . $email),

                Limit::perHour(5)
                    ->by('resend-password-reset-hour:' . $email),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | Reset Password Rate Limiter
        |--------------------------------------------------------------------------
        */
        RateLimiter::for('reset-password', function (Request $request) {
            $userId = $request->session()->get('password_reset_user_id');

            $key = $userId
                ? 'reset-password:user:' . $userId
                : 'reset-password:session:' . $request->session()->getId();

            return [
                Limit::perMinute(5)->by($key),
                Limit::perHour(20)->by($key . ':hour'),
            ];
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
