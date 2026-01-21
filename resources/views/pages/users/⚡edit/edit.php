<?php

declare(strict_types = 1);

use App\Brain\User\Processes\ResendInvitationProcess;
use App\Brain\User\Processes\UpdateUserProcess;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rules\Password;
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

    public ?int $role_id = null;

    public bool $is_admin = false;

    public function mount(User $user): void
    {
        // Reload user with trashed to ensure deleted_at is loaded
        $this->user = User::withTrashed()->with('roles')->findOrFail($user->id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->is_admin = $this->user->is_admin ?? false;
        $this->role_id = $this->user->roles->first()?->id;
    }

    public function getRolesProperty()
    {
        return Role::all();
    }

    public function save(): void
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'role_id'  => 'nullable|exists:roles,id',
            'is_admin' => 'boolean',
        ];

        // Only allow password changes if editing own account
        if ($this->isEditingOwnAccount()) {
            $rules['password']              = ['nullable', 'confirmed', Password::defaults()];
            $rules['password_confirmation'] = 'nullable|string';
        }

        $this->validate($rules);

        UpdateUserProcess::dispatchSync([
            'userId'   => $this->user->id,
            'name'     => $this->name,
            'password' => $this->password,
            'role_id'  => $this->role_id,
            'is_admin' => $this->is_admin,
        ]);

        $this->success('Usuário atualizado com sucesso!', redirectTo: route('users.index'));
    }

    public function delete(): void
    {
        if ($this->user->id === auth()->id()) {
            $this->error('Você não pode excluir a si mesmo.');

            return;
        }

        $this->user->delete();
        $this->success('Usuário removido com sucesso!', redirectTo: route('users.index'));
    }

    public function restore(): void
    {
        $this->user->restore();
        $this->success('Usuário restaurado com sucesso!', redirectTo: route('users.index'));
    }

    public function resendInvitation(): void
    {
        if ($this->user->status !== 'pending') {
            $this->error('Este usuário já ativou sua conta.');

            return;
        }

        ResendInvitationProcess::dispatchSync(['userId' => $this->user->id]);

        $this->success('Convite reenviado com sucesso!');
    }

    public function isEditingOwnAccount(): bool
    {
        return $this->user->id === auth()->id();
    }
};