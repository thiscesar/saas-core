<div class="flex min-h-screen items-center justify-center bg-base-200">
    <div class="w-full max-w-md">
        <x-card title="Login" shadow class="bg-base-100">
            @if (session('status'))
                <x-alert icon="o-check-circle" class="alert-success mb-4">
                    {{ session('status') }}
                </x-alert>
            @endif

            <x-form wire:submit="login">
                <x-input
                    label="E-mail"
                    wire:model="email"
                    type="email"
                    icon="o-envelope"
                    placeholder="seu@email.com"
                    inline
                />

                <x-input
                    label="Senha"
                    wire:model="password"
                    type="password"
                    icon="o-lock-closed"
                    placeholder="••••••••"
                    inline
                />

                <x-checkbox
                    label="Lembrar de mim"
                    wire:model="remember"
                />

                <x-slot:actions>
                    <x-button
                        label="Entrar"
                        type="submit"
                        icon="o-arrow-right-on-rectangle"
                        class="btn-primary w-full"
                        spinner="login"
                    />
                </x-slot:actions>
            </x-form>

            <x-hr text="Ou continue com" class="my-6" />

            <x-button
                label="Entrar com Slack"
                link="{{ route('auth.slack.redirect') }}"
                icon="o-chat-bubble-left-right"
                class="btn-outline w-full"
                no-wire-navigate
            >
                <x-slot:prepend>
                    <img src="{{ asset('images/logos/slack.svg') }}" alt="Slack" class="h-5 w-5">
                </x-slot:prepend>
            </x-button>

            @if (session('error'))
                <x-alert icon="o-exclamation-triangle" class="alert-error mt-4">
                    {{ session('error') }}
                </x-alert>
            @endif
        </x-card>
    </div>
</div>