<?php

declare(strict_types = 1);

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Layout('layouts::app'), Title('Usuários')] class extends Component
{
    use Toast;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'show_deleted')]
    public bool $showDeleted = false;

    public function with(): array
    {
        return [
            'users' => User::query()
                ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"))
                ->when($this->showDeleted, fn ($query) => $query->withTrashed())
                ->orderByRaw("CASE
                    WHEN deleted_at IS NOT NULL THEN 3
                    WHEN status = 'active' THEN 1
                    WHEN status = 'pending' THEN 2
                END")
                ->latest()
                ->paginate(20),
        ];
    }

    public function delete(int $userId): void
    {
        if (auth()->id() === $userId) {
            $this->error('Você não pode excluir a si mesmo.');

            return;
        }

        $user = User::findOrFail($userId);
        $user->delete();
        $this->success('Usuário removido com sucesso!');
    }

    public function restore(int $userId): void
    {
        $user = User::withTrashed()->findOrFail($userId);
        $user->restore();
        $this->success('Usuário restaurado com sucesso!');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedShowDeleted(): void
    {
        $this->resetPage();
    }
};