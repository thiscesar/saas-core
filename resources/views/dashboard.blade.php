<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                    <form action="/logout" method="POST">
                        @csrf
                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <!-- Welcome Card -->
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center gap-4">
                            @if(auth()->user()->avatar_url ?? null)
                                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="h-16 w-16 rounded-full">
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900">
                                    Olá, {{ auth()->user()->name }}!
                                </h2>
                                <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                            </div>
                        </div>

                        <!-- User Info -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Método de Login</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if(auth()->user()->slack_id ?? null)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800">
                                                <svg class="h-4 w-4" viewBox="0 0 54 54" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.712,11.456a5.428,5.428 0 1,1 -10.856,0a5.428,5.428 0 0,1 10.856,0" fill="#36C5F0"/>
                                                    <path d="M45.024,19.712a5.428,5.428 0 1,1 0,-10.856a5.428,5.428 0 0,1 0,10.856" fill="#2EB67D"/>
                                                    <path d="M11.456,34.288a5.428,5.428 0 1,1 0,10.856a5.428,5.428 0 0,1 0,-10.856" fill="#ECB22E"/>
                                                    <path d="M34.288,42.544a5.428,5.428 0 1,1 10.856,0a5.428,5.428 0 0,1 -10.856,0" fill="#E01E5A"/>
                                                </svg>
                                                Slack OAuth
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800">
                                                Email/Senha
                                            </span>
                                        @endif
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email Verificado</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @if(auth()->user()->email_verified_at)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800">
                                                ✓ Verificado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-xs font-medium text-yellow-800">
                                                Pendente
                                            </span>
                                        @endif
                                    </dd>
                                </div>

                                @if(auth()->user()->slack_id ?? null)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Slack ID</dt>
                                    <dd class="mt-1 text-sm font-mono text-gray-900">{{ auth()->user()->slack_id }}</dd>
                                </div>
                                @endif

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Membro desde</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ auth()->user()->created_at->format('d/m/Y') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Total de Logins</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                            {{ auth()->user()->logins()->count() }}
                        </dd>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Último Login</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                            {{ auth()->user()->logins()->latest()->first()?->created_at?->diffForHumans() ?? 'Agora' }}
                        </dd>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Tipo de Conta</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">
                            {{ (auth()->user()->is_admin ?? false) ? 'Admin' : 'Usuário' }}
                        </dd>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
