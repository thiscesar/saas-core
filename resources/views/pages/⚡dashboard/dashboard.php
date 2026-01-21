<?php

declare(strict_types = 1);

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts::app'), Title('Dashboard')] class extends Component
{
    public function totalLogins(): int
    {
        return auth()->user()->logins()->count();
    }

    public function lastLogin(): string
    {
        return auth()->user()->logins()->latest()->first()?->created_at?->diffForHumans() ?? 'Agora';
    }

    public function accountType(): string
    {
        return (auth()->user()->is_admin ?? false) ? 'Admin' : 'Usuário';
    }
};
