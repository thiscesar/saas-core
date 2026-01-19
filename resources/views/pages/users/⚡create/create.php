<?php

use App\Brain\User\Processes\CreateUserProcess;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts::app'), Title('Novo Usuário')] class extends Component
{
    use Toast;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';

    #[Validate('boolean')]
    public bool $is_admin = false;

    public function save(): void
    {
        $this->validate();

        CreateUserProcess::dispatchSync([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
            'is_admin' => $this->is_admin,
        ]);

        $this->success('Usuário criado com sucesso!', redirectTo: route('users.index'));
    }
};