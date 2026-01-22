<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::app'), Title('Minha Conta')] class extends Component
{
    use Toast;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|min:8')]
    public ?string $current_password = null;

    #[Validate(['nullable', 'string', 'confirmed'])]
    public ?string $password = null;

    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $this->name = auth()->user()->name;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $user       = auth()->user();
        $user->name = $this->name;
        $user->save();

        $this->success('Perfil atualizado com sucesso!');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        // Verify current password
        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'A senha atual está incorreta.');

            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        // Clear password fields
        $this->current_password      = null;
        $this->password              = null;
        $this->password_confirmation = null;

        $this->success('Senha alterada com sucesso!');
    }
};
