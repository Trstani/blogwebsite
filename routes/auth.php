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
])->middleware('throttle:register')->name('register');

Route::post('/verify-otp', [
    RegisterController::class,
    'verifyOtp',
])->middleware('throttle:verify-otp')->name('verify.otp');

Route::post('/resend-otp', [
    RegisterController::class,
    'resendOtp',
])->middleware('throttle:resend-otp')->name('resend.otp');

// Forgot Password Routes
Route::post('/forgot-password', [
    ForgotPasswordController::class,
    'sendOtp',
])->middleware('throttle:forgot-password')->name('forgot.password');

Route::post('/verify-password-reset', [
    ForgotPasswordController::class,
    'verifyOtp',
])->middleware('throttle:verify-password-reset')
    ->name('verify.password.reset');

Route::post('/resend-password-reset', [
    ForgotPasswordController::class,
    'resendOtp',
])->middleware('throttle:resend-password-reset')
    ->name('resend.password.reset');

Route::post('/reset-password', [
    ForgotPasswordController::class,
    'resetPassword',
])->middleware('throttle:reset-password')
    ->name('reset.password');

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
})->middleware('throttle:login')->name('login');

// Logout
Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Diagnostic endpoint for OTP verification troubleshooting (debug only, remove in production)
Route::get('/diagnostic/otp', [
    \App\Http\Controllers\Auth\DiagnosticOtpController::class,
    'diagnose',
]);

// Detailed diagnostic endpoint
Route::get('/diagnostic/otp-detailed', [
    \App\Http\Controllers\Auth\DetailedDiagnosticController::class,
    'diagnose',
]);

// Forgot Password Page
Route::get('/forgot-password', function () {
    return view('MainPage.forgot-password');
})->name('forgot.password.page');

// Reset Password Page
Route::get('/reset-password', function () {
    return view('MainPage.reset-password');
})->name('reset.password.page');
