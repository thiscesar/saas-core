<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::app'), Title('Configurações')] class extends Component
{
    use Toast;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|min:8')]
    public ?string $current_password = null;

    #[Validate(['nullable', 'string', 'confirmed'])]
    public ?string $password = null;

    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();
        $user->name = $this->name;
        $user->email = $this->email;
        $user->save();

        $this->success('Perfil atualizado com sucesso!');
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();

        // Verify current password
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'A senha atual está incorreta.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        // Clear password fields
        $this->current_password = null;
        $this->password = null;
        $this->password_confirmation = null;

        $this->success('Senha alterada com sucesso!');
    }
};
