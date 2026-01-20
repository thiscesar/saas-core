<div>
    {{-- Page Header --}}
    <div class="mb-6 px-5 lg:px-6">
        <h1 class="text-2xl font-bold lg:text-3xl">Editar Usuário</h1>
    </div>

    {{-- Main Content --}}
    <div class="px-5 lg:px-6">
        <div class="mx-auto max-w-2xl">
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
                    />

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

                    <x-checkbox
                        label="Administrador"
                        wire:model="is_admin"
                        hint="Usuários administradores têm acesso total ao sistema"
                    />
                </div>

                <x-slot:actions>
                    <x-button label="Cancelar" link="{{ route('users.index') }}" />
                    <x-button label="Salvar" type="submit" class="btn-primary" spinner="save" />
                </x-slot:actions>
            </x-form>
        </x-card>
        </div>
    </div>
</div>