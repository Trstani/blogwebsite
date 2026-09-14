<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isAdmin()) {
        return redirect('/admin/dashboard');
    }

    return redirect('/writer/dashboard');
})->name('dashboard')->middleware('auth');
