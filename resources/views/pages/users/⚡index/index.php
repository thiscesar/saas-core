<?php

use App\Brain\User\Processes\DeleteUserProcess;
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

    public function with(): array
    {
        return [
            'users' => User::query()
                ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"))
                ->latest()
                ->paginate(20),
        ];
    }

    public function deleteUser(int $userId): void
    {
        if (auth()->id() === $userId) {
            $this->error('Você não pode excluir sua própria conta.');

            return;
        }

        DeleteUserProcess::dispatchSync(['userId' => $userId]);

        $this->success('Usuário excluído com sucesso!');
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }
};