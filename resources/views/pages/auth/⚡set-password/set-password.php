<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts.auth'), Title('Definir Senha')] class extends Component
{
    use Toast;

    #[Validate]
    public string $password = '';

    #[Validate]
    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'password'              => ['required', 'confirmed', Password::defaults()],
            'password_confirmation' => ['required'],
        ];
    }

    public function setPassword(): void
    {
        $this->validate();

        $user = auth()->user();

        if ( ! $user) {
            $this->error('Usuário não autenticado.', redirectTo: '/auth/login');

            return;
        }

        $user->update([
            'password'        => Hash::make($this->password),
            'password_set_at' => now(),
        ]);

        $this->success('Senha definida com sucesso!', redirectTo: '/dashboard');
    }
};