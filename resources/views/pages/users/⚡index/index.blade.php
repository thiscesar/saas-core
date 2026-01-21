<div>
    {{-- Page Header --}}
    <div class="mb-5 px-5 lg:px-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold lg:text-3xl">Usuários</h1>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <x-checkbox
                    label="Mostrar removidos"
                    wire:model.live="showDeleted"
                />
                <x-input
                    wire:model.live.debounce.300ms="search"
                    placeholder="Buscar..."
                    icon="o-magnifying-glass"
                    clearable
                    class="w-full sm:w-64"
                />
                <x-button
                    label="Novo"
                    icon="o-plus"
                    link="{{ route('users.create') }}"
                    class="btn-primary"
                    responsive
                />
            </div>
        </div>
    </div>

    {{-- Header Divider --}}
    <div class="mb-6 border-t border-base-300"></div>

    {{-- Main Content --}}
    <div class="px-5 lg:px-6">
        <x-card class="bg-base-100 shadow-sm">
            <x-table
                :headers="[
                    ['key' => 'user', 'label' => 'Usuário'],
                    ['key' => 'email', 'label' => 'E-mail'],
                    ['key' => 'created_at', 'label' => 'Cadastrado em'],
                    ['key' => 'actions', 'label' => 'Ações', 'class' => 'w-32'],
                ]"
                :rows="$users"
                striped
            >
                @scope('cell_user', $user)
                    <div class="flex items-center gap-3">
                        <x-user-avatar :user="$user" class="!w-10" />
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">{{ $user->name }}</span>
                                @if($user->is_admin)
                                    <x-badge value="Admin" class="badge-sm badge-accent" />
                                @endif
                                @if($user->status === 'pending')
                                    <x-badge value="Pendente" class="badge-sm badge-warning" />
                                @endif
                                @if($user->trashed())
                                    <x-badge value="Removido" class="badge-sm badge-error" />
                                @endif
                            </div>
                        </div>
                    </div>
                @endscope

                @scope('cell_email', $user)
                    {{ $user->email }}
                @endscope

                @scope('cell_created_at', $user)
                    <span class="text-sm text-base-content/70">
                        {{ $user->created_at->diffForHumans() }}
                    </span>
                @endscope

                @scope('cell_actions', $user)
                    <div class="flex gap-2">
                        <x-button
                            icon="o-pencil"
                            link="{{ route('users.edit', $user) }}"
                            class="btn-ghost btn-sm"
                            tooltip="Editar"
                        />
                        @if(auth()->id() !== $user->id && !$user->trashed())
                            <x-button
                                icon="o-trash"
                                wire:click="delete({{ $user->id }})"
                                wire:confirm="Tem certeza que deseja remover este usuário?"
                                class="btn-ghost btn-sm text-warning"
                                tooltip="Remover"
                                spinner="delete"
                            />
                        @endif
                        @if($user->trashed())
                            <x-button
                                icon="o-arrow-path"
                                wire:click="restore({{ $user->id }})"
                                wire:confirm="Tem certeza que deseja restaurar este usuário?"
                                class="btn-ghost btn-sm text-success"
                                tooltip="Restaurar"
                                spinner="restore"
                            />
                        @endif
                    </div>
                @endscope
            </x-table>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </x-card>
    </div>
</div>