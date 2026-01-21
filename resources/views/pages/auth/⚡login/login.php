<?php

declare(strict_types = 1);

use App\Brain\Auth\Processes\AuthProcess;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts.auth'), Title('Login')] class extends Component
{
    use Toast;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public function login(): void
    {
        // Rate limiting: 5 attempts per IP
        $key = 'login:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('email', "Muitas tentativas. Tente novamente em {$seconds} segundos.");

            return;
        }

        $this->validate();

        // Check if user exists and is eligible for login
        // Soft deleted users are automatically excluded from queries
        $user = App\Models\User::where('email', $this->email)->first();

        // Generic error message - does not reveal if user exists or not
        if (! $user || $user->status !== 'active' || ! $user->password_set_at) {
            RateLimiter::hit($key, 900); // Block for 15 minutes
            $this->addError('email', 'Email ou senha incorretos.');

            return;
        }

        // Attempt to authenticate
        try {
            AuthProcess::dispatchSync([
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            RateLimiter::clear($key);
            $this->success('Login realizado com sucesso!', redirectTo: '/dashboard');
        } catch (ValidationException $e) {
            // Generic error message - does not reveal if password is wrong
            RateLimiter::hit($key, 900); // Block for 15 minutes
            $this->addError('email', 'Email ou senha incorretos.');
        }
    }
};
