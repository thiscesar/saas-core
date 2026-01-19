<div>
    {{-- Page Header --}}
    <div class="mb-6 px-5 lg:px-6">
        <h1 class="text-2xl font-bold lg:text-3xl">Novo Usuário</h1>
    </div>

    {{-- Main Content --}}
    <div class="px-5 lg:px-6">
        <div class="mx-auto max-w-2xl">
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
                        label="Senha"
                        wire:model="password"
                        type="password"
                        icon="o-lock-closed"
                        placeholder="Mínimo de 8 caracteres"
                        hint="Use uma senha forte"
                    />

                    <x-input
                        label="Confirmar senha"
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