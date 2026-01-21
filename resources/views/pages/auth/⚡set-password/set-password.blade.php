<div data-theme="light" class="flex min-h-screen items-center justify-center bg-base-200">
    <div class="w-full max-w-md">
        <x-card title="Definir Senha" shadow class="bg-base-100">
            <div class="mb-4">
                <p class="text-sm text-base-content/70">
                    Por segurança, você precisa definir uma senha forte para acessar o sistema.
                    Esta senha será usada para fazer login junto com seu e-mail.
                </p>
            </div>

            <x-form wire:submit="setPassword">
                <x-input
                    label="Nova Senha"
                    wire:model="password"
                    type="password"
                    icon="o-lock-closed"
                    placeholder="••••••••"
                    hint="Mínimo 8 caracteres, incluindo letras, números e símbolos"
                    inline
                />

                <x-input
                    label="Confirmar Senha"
                    wire:model="password_confirmation"
                    type="password"
                    icon="o-lock-closed"
                    placeholder="••••••••"
                    inline
                />

                <x-button
                    label="Definir Senha"
                    type="submit"
                    icon="o-check"
                    class="btn-primary w-full"
                    spinner="setPassword"
                />
            </x-form>
        </x-card>
    </div>
</div>