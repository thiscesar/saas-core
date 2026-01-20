<?php

use App\Brain\User\Processes\InviteUserProcess;
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

    #[Validate('boolean')]
    public bool $is_admin = false;

    public function save(): void
    {
        $this->validate();

        InviteUserProcess::dispatchSync([
            'name'     => $this->name,
            'email'    => $this->email,
            'is_admin' => $this->is_admin,
        ]);

        $this->success('Convite enviado com sucesso!', redirectTo: route('users.index'));
    }
};