<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Tasks;

use App\Models\User;
use Brain\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

/**
 * Task LoginTask
 *
 * @property-read string|null $email
 * @property-read string|null $password
 *
 * @property User|null $user
 */
class LoginTask extends Task
{
    public function handle(): self
    {
        // If user is already authenticated by a previous task (e.g., OAuth), just log them in
        if ($this->user) {
            Auth::login($this->user);
            Session::regenerate();

            return $this;
        }

        // Otherwise, do traditional password authentication
        $credentials = [
            'email'    => $this->email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) {
            Session::regenerate();

            $this->user = Auth::user();

            return $this;
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }
}
