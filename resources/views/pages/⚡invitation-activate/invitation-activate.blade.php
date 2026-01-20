<div data-theme="light" class="flex min-h-screen items-center justify-center bg-base-200">
    <div class="w-full max-w-md">
        <x-card title="Ativar Conta" shadow class="bg-base-100">
            @if($invitation)
                <div class="mb-6">
                    <p class="text-sm text-base-content/70">
                        Bem-vindo! Para ativar sua conta, insira o código PIN de 6 dígitos enviado para <strong>{{ $invitation->email }}</strong>.
                    </p>
                </div>

                <x-form wire:submit="verify">
                    <div class="mb-6 text-center">
                        <label class="label mb-3">
                            <span class="label-text">Código PIN</span>
                        </label>
                        <div class="flex justify-center">
                            <x-pin
                                wire:model="pin"
                                size="6"
                                numeric
                            />
                        </div>
                        <label class="label mt-3">
                            <span class="label-text-alt text-base-content/60">Verifique o código de 6 dígitos enviado no email</span>
                        </label>
                    </div>

                    <x-button
                        label="Verificar e Conectar ao Slack"
                        type="submit"
                        icon="o-check-circle"
                        class="btn-primary w-full"
                        spinner="verify"
                    />
                </x-form>

                <div class="mt-6">
                    <div class="flex items-start gap-3 rounded-lg bg-base-200 p-4 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 shrink-0 text-base-content/60">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <p class="text-base-content/70">
                            Após verificar o código PIN, você será redirecionado para conectar sua conta do Slack.
                        </p>
                    </div>
                </div>
            @endif
        </x-card>
    </div>
</div>