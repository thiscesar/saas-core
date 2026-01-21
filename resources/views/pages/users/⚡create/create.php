<?php

declare(strict_types = 1);

use App\Brain\User\Processes\InviteUserProcess;
use App\Models\Role;
use App\Rules\EmailDomain;
use App\Rules\UniqueInvitation;
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

    #[Validate(['required', 'email', 'unique:users,email', new EmailDomain(), new UniqueInvitation()])]
    public string $email = '';

    #[Validate('nullable|exists:roles,id')]
    public ?int $role_id = null;

    #[Validate('boolean')]
    public bool $is_admin = false;

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function save(): void
    {
        $this->validate();

        InviteUserProcess::dispatchSync([
            'name'     => $this->name,
            'email'    => $this->email,
            'role_id'  => $this->role_id,
            'is_admin' => $this->is_admin,
        ]);

        $this->success('Convite enviado com sucesso!', redirectTo: route('users.index'));
    }
};
