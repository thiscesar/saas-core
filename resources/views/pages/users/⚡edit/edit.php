<?php

use App\Brain\User\Processes\UpdateUserProcess;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::app'), Title('Editar Usuário')] class extends Component
{
    use Toast;

    public User $user;

    public string $name = '';

    public string $email = '';

    public ?string $password = null;

    public ?string $password_confirmation = null;

    public bool $is_admin = false;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin ?? false;
    }

    public function save(): void
    {
        $this->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email,' . $this->user->id,
            'password'              => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'nullable|string',
            'is_admin'              => 'boolean',
        ]);

        UpdateUserProcess::dispatchSync([
            'userId'   => $this->user->id,
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'is_admin' => $this->is_admin,
        ]);

        $this->success('Usuário atualizado com sucesso!', redirectTo: route('users.index'));
    }
};