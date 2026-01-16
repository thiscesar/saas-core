<div class="flex min-h-screen items-center justify-center bg-gray-100">
    <div class="w-full max-w-md">
        <div class="rounded-lg bg-white px-8 py-10 shadow-md">
            <h1 class="mb-8 text-center text-3xl font-bold text-gray-900">Login</h1>

            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <form wire:submit="login">
                <div class="mb-6">
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                        E-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        wire:model="email"
                        class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="seu@email.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                        Senha
                    </label>
                    <input
                        type="password"
                        id="password"
                        wire:model="password"
                        class="w-full rounded-md border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input
                            type="checkbox"
                            wire:model="remember"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">Lembrar de mim</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-md bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Entrar
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-2 text-gray-500">Ou continue com</span>
                </div>
            </div>

            <!-- Slack OAuth Button -->
            <a
                href="{{ route('auth.slack.redirect') }}"
                class="flex w-full items-center justify-center gap-3 rounded-md border border-gray-300 bg-white px-4 py-2 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
            >
                <svg class="h-5 w-5" viewBox="0 0 54 54" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.712,11.456a5.428,5.428 0 1,1 -10.856,0a5.428,5.428 0 0,1 10.856,0" fill="#36C5F0"/>
                    <path d="M45.024,19.712a5.428,5.428 0 1,1 0,-10.856a5.428,5.428 0 0,1 0,10.856" fill="#2EB67D"/>
                    <path d="M11.456,34.288a5.428,5.428 0 1,1 0,10.856a5.428,5.428 0 0,1 0,-10.856" fill="#ECB22E"/>
                    <path d="M34.288,42.544a5.428,5.428 0 1,1 10.856,0a5.428,5.428 0 0,1 -10.856,0" fill="#E01E5A"/>
                </svg>
                <span>Entrar com Slack</span>
            </a>

            @if (session('error'))
                <div class="mt-4 rounded-md bg-red-50 px-4 py-3 text-red-800">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</div>