<?php

declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Brain\Auth\Processes\AuthProcess;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request): RedirectResponse
    {
        AuthProcess::dispatchSync($request->validated());

        return redirect()->intended('/dashboard');
    }
}
