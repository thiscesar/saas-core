<div>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold lg:text-3xl">Editar Usuário</h1>
    </div>

    <x-hr/>

    {{-- Main Content --}}
    <div class="px-5 pb-8 lg:px-6">
        <div class="mx-auto max-w-2xl">
            {{-- User Profile Header --}}
            <x-card class="mb-6 bg-base-100 shadow-sm">
                <div class="flex flex-col items-center gap-4 py-4 text-center">
                    <x-user-avatar :user="$user" class="w-20! h-20!" />

                    <div>
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            <h2 class="text-xl font-bold">{{ $user->name }}</h2>
                            @if($user->is_admin)
                                <x-badge value="Admin" class="badge-accent" />
                            @endif
                            @foreach($user->roles as $role)
                                <x-badge value="{{ $role->display_name }}" class="badge-info" />
                            @endforeach
                            @if($user->status === 'pending')
                                <x-badge value="Pendente" class="badge-warning" />
                            @endif
                            @if($user->trashed())
                                <x-badge value="Removido" class="badge-error" />
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-base-content/70">{{ $user->email }}</p>
                    </div>
                </div>
            </x-card>

            @if($user->status === 'pending')
                <x-alert icon="o-clock" class="alert-warning mb-4">
                    <div class="flex items-center justify-between gap-1">
                        <div>
                            <strong>Convite pendente</strong>
                            <p class="text-sm">Este usuário ainda não ativou sua conta. O convite expira em 24 horas.</p>
                        </div>
                        <x-button
                            label="Reenviar Convite"
                            icon="o-paper-airplane"
                            wire:click="resendInvitation"
                            wire:confirm="Tem certeza? Isso invalidará o convite anterior e enviará um novo email."
                            class="btn-sm btn-warning"
                            spinner="resendInvitation"
                        />
                    </div>
                </x-alert>
            @endif

            <x-card class="bg-base-100 shadow-sm">
            <x-form wire:submit="save">
                <div class="space-y-4">
                    <x-input
                        label="Nome completo"
                        wire:model="name"
                        icon="o-user"
                        placeholder="Digite o nome completo"
                    />

                    <x-input
                        label="E-mail"
                        wire:model="email"
                        type="email"
                        icon="o-envelope"
                        placeholder="usuario@exemplo.com"
                        readonly
                        disabled
                        hint="Email não pode ser alterado (vem do Slack)"
                    />

                    @if($this->isEditingOwnAccount())
                        <x-input
                            label="Nova senha (deixe em branco para manter a atual)"
                            wire:model="password"
                            type="password"
                            icon="o-lock-closed"
                            placeholder="Mínimo de 8 caracteres"
                            hint="Preencha apenas se deseja alterar a senha"
                        />

                        <x-input
                            label="Confirmar nova senha"
                            wire:model="password_confirmation"
                            type="password"
                            icon="o-lock-closed"
                            placeholder="Digite a senha novamente"
                        />
                    @endif

                    <x-select
                        label="Função"
                        wire:model="role_id"
                        :options="$this->roles"
                        option-value="id"
                        option-label="display_name"
                        placeholder="Selecione uma função"
                        hint="Define as permissões do usuário no sistema"
                    />

                    <x-checkbox
                        label="Super Administrador"
                        wire:model="is_admin"
                        hint="Acesso total ao sistema, ignorando permissões (use com cautela)"
                    />
                </div>

                <x-slot:actions>
                    <x-button label="Cancelar" link="{{ route('users.index') }}" />
                    <x-button label="Salvar" type="submit" class="btn-primary" spinner="save" />

                    @if(!$this->isEditingOwnAccount() && !$this->user->trashed())
                        <x-button
                            label="Remover"
                            wire:click="delete"
                            wire:confirm="Tem certeza que deseja remover este usuário?"
                            class="btn-warning"
                            spinner="delete"
                        />
                    @endif

                    @if($this->user->trashed())
                        <x-button
                            label="Restaurar"
                            wire:click="restore"
                            wire:confirm="Tem certeza que deseja restaurar este usuário?"
                            class="btn-success"
                            spinner="restore"
                        />
                    @endif
                </x-slot:actions>
            </x-form>
        </x-card>
        </div>
    </div>
</div>