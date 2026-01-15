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
        </div>
    </div>
</div>