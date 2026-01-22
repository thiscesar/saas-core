<div>
    {{-- Page Header --}}
    <div class="mb-5 ">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold lg:text-3xl">Dashboard</h1>
            {{-- Espaço para ações futuras (filtros, botões, etc) --}}
        </div>
    </div>

    {{-- Header Divider --}}
    <x-hr/>

    {{-- Main Content --}}
    <div>
    {{-- Welcome Card --}}
    <x-card shadow class="mb-6 bg-base-100">
        <div class="flex items-center gap-4">
            <x-user-avatar :user="auth()->user()" class="!w-16" />

            <div>
                <h2 class="text-2xl font-bold">
                    Olá, {{ auth()->user()->name }}!
                </h2>
                <p class="text-sm text-base-content/70">{{ auth()->user()->email }}</p>
            </div>
        </div>

        <x-hr class="my-6" />

        <div class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-base-content/70">Método de Login</dt>
                <dd class="mt-1">
                    @if(auth()->user()->slack_id ?? null)
                        <x-badge value="Slack OAuth" class="badge-secondary gap-2">
                            <x-slot:prepend>
                                <img src="{{ asset('images/logos/slack.svg') }}" alt="Slack" class="h-4 w-4">
                            </x-slot:prepend>
                        </x-badge>
                    @else
                        <x-badge value="Email/Senha" class="badge-primary" />
                    @endif
                </dd>
            </div>

            <div>
                <dt class="text-sm font-medium text-base-content/70">Email Verificado</dt>
                <dd class="mt-1">
                    @if(auth()->user()->email_verified_at)
                        <x-badge value="✓ Verificado" class="badge-success" />
                    @else
                        <x-badge value="Pendente" class="badge-warning" />
                    @endif
                </dd>
            </div>

            @if(auth()->user()->slack_id ?? null)
            <div>
                <dt class="text-sm font-medium text-base-content/70">Slack ID</dt>
                <dd class="mt-1 font-mono text-sm">{{ auth()->user()->slack_id }}</dd>
            </div>
            @endif

            <div>
                <dt class="text-sm font-medium text-base-content/70">Membro desde</dt>
                <dd class="mt-1 text-sm">{{ auth()->user()->created_at->format('d/m/Y') }}</dd>
            </div>
        </div>
    </x-card>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <x-stat
            title="Total de Logins"
            description="Quantidade de acessos"
            :value="$this->totalLogins()"
            icon="o-users"
            color="text-primary"
        />

        <x-stat
            title="Último Login"
            description="Último acesso realizado"
            :value="$this->lastLogin()"
            icon="o-clock"
            color="text-secondary"
        />

        <x-stat
            title="Tipo de Conta"
            description="Nível de acesso"
            :value="$this->accountType()"
            icon="o-shield-check"
            color="text-accent"
        />
    </div>
    {{-- End Main Content --}}
    </div>
</div>