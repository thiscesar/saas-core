<?php

declare(strict_types = 1);

use App\Http\Controllers\Auth\SlackAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Illuminate\Routing\Redirector | \Illuminate\Http\RedirectResponse => auth()->check() ? redirect('/dashboard') : redirect('/login'))->name('home');

// Guest routes (redirect to dashboard if authenticated)
Route::middleware('guest')->group(function (): void {
    Route::livewire('/login', 'pages::auth.login')->name('login');
    Route::livewire('/invitation/activate/{token}', 'pages::invitation-activate')->name('invitation.activate');

    Route::get('/auth/slack/redirect', [SlackAuthController::class, 'redirect'])
        ->name('auth.slack.redirect');
});

Route::get('/auth/slack/callback', [SlackAuthController::class, 'callback'])
    ->name('auth.slack.callback');

// Authenticated routes
Route::middleware('auth')->group(function (): void {
    Route::livewire('/auth/set-password', 'pages::auth.set-password')->name('auth.set-password');
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/settings', 'pages::profile')->name('settings');

    Route::post('/logout', function (): Illuminate\Routing\Redirector | Illuminate\Http\RedirectResponse {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/login');
    })->name('logout');
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::livewire('/users', 'pages::users.index')->name('users.index');
    Route::livewire('/users/create', 'pages::users.create')->name('users.create');
    Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('users.edit');
});
