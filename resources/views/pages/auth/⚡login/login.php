<?php

declare(strict_types = 1);

use App\Brain\Auth\Processes\AuthProcess;
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

    public bool $remember = false;

    public function login(): void
    {
        $this->validate();

        try {
            AuthProcess::dispatchSync([
                'email'    => $this->email,
                'password' => $this->password,
            ]);

            $this->success('Login realizado com sucesso!', redirectTo: '/dashboard');
        } catch (ValidationException $e) {
            $this->addError('email', $e->validator->errors()->first('email'));
        }
    }
};
