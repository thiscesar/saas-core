<?php

declare(strict_types = 1);

use App\Http\Controllers\Auth\SlackAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View => view('welcome'));

Route::livewire('/login', 'pages::auth.login');

Route::get('/auth/slack/redirect', [SlackAuthController::class, 'redirect'])
    ->name('auth.slack.redirect');

Route::get('/auth/slack/callback', [SlackAuthController::class, 'callback'])
    ->name('auth.slack.callback');
