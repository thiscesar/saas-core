<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Illuminate\Contracts\View\Factory | \Illuminate\Contracts\View\View => view('welcome'));

Route::get('/login/{id}', fn ($id): Illuminate\Contracts\Auth\Authenticatable | false => Auth::loginUsingId($id));