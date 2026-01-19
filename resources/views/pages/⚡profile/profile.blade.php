<div>
    {{-- Page Header --}}
    <div class="mb-5 px-5 lg:mb-6 lg:px-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold lg:text-3xl">Configurações</h1>
            {{-- Espaço para ações futuras --}}
        </div>
    </div>

    {{-- Header Divider --}}
    <div class="mb-6 border-t border-base-300"></div>

    {{-- Main Content --}}
    <div class="max-w-5xl space-y-6">
    {{-- Profile Information Card --}}
    <x-card title="Informações do Perfil" shadow class="bg-base-100">
        <form wire:submit="updateProfile">
            <div class="space-y-4">
                <x-input
                    label="Nome"
                    wire:model="name"
                    icon="o-user"
                    placeholder="Seu nome completo"
                    hint="Como você gostaria de ser chamado?"
                />

                <x-input
                    label="Email"
                    wire:model="email"
                    icon="o-envelope"
                    type="email"
                    placeholder="seu@email.com"
                    hint="Usado para login e notificações"
                />

                @if(auth()->user()->slack_id)
                    <x-alert icon="o-information-circle" class="alert-info">
                        Você está autenticado via Slack. Algumas informações podem ser sincronizadas automaticamente.
                    </x-alert>
                @endif

                <div class="flex justify-end">
                    <x-button
                        label="Salvar Alterações"
                        icon="o-check"
                        type="submit"
                        class="btn-primary"
                        spinner="updateProfile"
                    />
                </div>
            </div>
        </form>
    </x-card>

    {{-- Change Password Card --}}
    @if(!auth()->user()->slack_id)
        <x-card title="Alterar Senha" shadow class="bg-base-100">
            <form wire:submit="updatePassword">
                <div class="space-y-4">
                    <x-alert icon="o-shield-check" class="alert-warning">
                        Use uma senha forte com pelo menos 8 caracteres.
                    </x-alert>

                    <x-input
                        label="Senha Atual"
                        wire:model="current_password"
                        type="password"
                        icon="o-lock-closed"
                        placeholder="Digite sua senha atual"
                    />

                    <x-input
                        label="Nova Senha"
                        wire:model="password"
                        type="password"
                        icon="o-key"
                        placeholder="Digite sua nova senha"
                    />

                    <x-input
                        label="Confirmar Nova Senha"
                        wire:model="password_confirmation"
                        type="password"
                        icon="o-key"
                        placeholder="Confirme sua nova senha"
                    />

                    <div class="flex justify-end">
                        <x-button
                            label="Alterar Senha"
                            icon="o-check"
                            type="submit"
                            class="btn-secondary"
                            spinner="updatePassword"
                        />
                    </div>
                </div>
            </form>
        </x-card>
    @else
        <x-card title="Autenticação" shadow class="bg-base-100">
            <x-alert icon="o-information-circle" class="alert-info">
                Você está autenticado via Slack OAuth. A senha é gerenciada pela sua conta do Slack.
            </x-alert>
        </x-card>
    @endif

    {{-- Account Info --}}
    <x-card title="Informações da Conta" shadow class="bg-base-100">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-base-content/70">Membro desde</dt>
                <dd class="mt-1 text-sm">{{ auth()->user()->created_at->format('d/m/Y') }}</dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-base-content/70">Email verificado</dt>
                <dd class="mt-1">
                    @if(auth()->user()->email_verified_at)
                        <x-badge value="✓ Verificado" class="badge-success" />
                    @else
                        <x-badge value="Pendente" class="badge-warning" />
                    @endif
                </dd>
            </div>

            @if(auth()->user()->slack_id)
                <div>
                    <dt class="text-sm font-medium text-base-content/70">Slack ID</dt>
                    <dd class="mt-1 font-mono text-sm">{{ auth()->user()->slack_id }}</dd>
                </div>
            @endif

            <div>
                <dt class="text-sm font-medium text-base-content/70">Tipo de Conta</dt>
                <dd class="mt-1">
                    @if(auth()->user()->is_admin ?? false)
                        <x-badge value="Admin" class="badge-accent" />
                    @else
                        <x-badge value="Usuário" class="badge-primary" />
                    @endif
                </dd>
            </div>
        </div>
    </x-card>
    </div>
    {{-- End Main Content --}}
</div>
