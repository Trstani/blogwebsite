<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Registration Routes (JSON API)
Route::post('/register', [
    RegisterController::class,
    'register',
])->name('register');

Route::post('/verify-otp', [
    RegisterController::class,
    'verifyOtp',
])->name('verify.otp');

Route::post('/resend-otp', [
    RegisterController::class,
    'resendOtp',
])->name('resend.otp');

// Forgot Password Routes
Route::post('/forgot-password', [
    ForgotPasswordController::class,
    'sendOtp',
])->name('forgot.password');

Route::post('/verify-password-reset', [
    ForgotPasswordController::class,
    'verifyOtp',
])->name('verify.password.reset');

Route::post('/resend-password-reset', [
    ForgotPasswordController::class,
    'resendOtp',
])->name('resend.password.reset');

Route::post('/reset-password', [
    ForgotPasswordController::class,
    'resetPassword',
])->name('reset.password');

// Login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Email atau password salah.',
    ]);
})->name('login');

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Forgot Password Page
Route::get('/forgot-password', function () {
    return view('MainPage.forgot-password');
})->name('forgot.password.page');

// Reset Password Page
Route::get('/reset-password', function () {
    return view('MainPage.reset-password');
})->name('reset.password.page');
