<?php

declare(strict_types = 1);

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View => view('welcome'));

Route::post('/login', LoginController::class)->name('login.store');

Route::get('/login/{id}', fn ($id): Illuminate\Contracts\Auth\Authenticatable | false => Auth::loginUsingId($id));
