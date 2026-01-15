<?php

use App\Brain\Auth\Processes\AuthProcess;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

new #[Layout('layouts.auth'), Title('Login')] class extends Component
{
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
                'email' => $this->email,
                'password' => $this->password,
            ]);

            session()->flash('status', 'Login realizado com sucesso!');

            $this->redirect('/dashboard', navigate: true);
        } catch (ValidationException $e) {
            $this->addError('email', $e->validator->errors()->first('email'));
        }
    }
};