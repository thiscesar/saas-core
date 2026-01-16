<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 font-sans antialiased">
    <div class="min-h-screen">
        <x-header title="Dashboard" separator>
            <x-slot:actions>
                <form action="/logout" method="POST">
                    @csrf
                    <x-button
                        label="Sair"
                        icon="o-arrow-right-on-rectangle"
                        type="submit"
                        class="btn-error btn-sm"
                        no-wire-navigate
                    />
                </form>
            </x-slot:actions>
        </x-header>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <x-card shadow class="mb-6 bg-base-100">
                <div class="flex items-center gap-4">
                    @if(auth()->user()->avatar_url ?? null)
                        <x-avatar image="{{ auth()->user()->avatar_url }}" class="w-16" />
                    @else
                        <x-avatar>
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-2xl font-bold text-primary-content">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        </x-avatar>
                    @endif
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

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                <x-stat
                    title="Total de Logins"
                    description="Quantidade de acessos"
                    value="{{ auth()->user()->logins()->count() }}"
                    icon="o-users"
                    color="text-primary"
                />

                <x-stat
                    title="Último Login"
                    description="Último acesso realizado"
                    value="{{ auth()->user()->logins()->latest()->first()?->created_at?->diffForHumans() ?? 'Agora' }}"
                    icon="o-clock"
                    color="text-secondary"
                />

                <x-stat
                    title="Tipo de Conta"
                    description="Nível de acesso"
                    value="{{ (auth()->user()->is_admin ?? false) ? 'Admin' : 'Usuário' }}"
                    icon="o-shield-check"
                    color="text-accent"
                />
            </div>
        </main>
    </div>
</body>
</html>
