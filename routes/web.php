<?php

declare(strict_types = 1);

use App\Http\Controllers\Auth\SlackAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect('/dashboard') : redirect('/login'))->name('home');

// Guest routes (redirect to dashboard if authenticated)
Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'pages::auth.login')->name('login');

    Route::get('/auth/slack/redirect', [SlackAuthController::class, 'redirect'])
        ->name('auth.slack.redirect');
});

Route::get('/auth/slack/callback', [SlackAuthController::class, 'callback'])
    ->name('auth.slack.callback');

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
});
