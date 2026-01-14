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
 * @property-read string $email
 * @property-read string $password
 *
 * @property User|null $user
 */
class LoginTask extends Task
{
    public function handle(): self
    {
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
