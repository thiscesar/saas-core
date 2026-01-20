<?php

declare(strict_types = 1);

use App\Models\Invitation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Mary\Traits\Toast;

new #[Layout('layouts.auth'), Title('Ativar Conta')] class extends Component
{
    use Toast;

    public string $token = '';

    public ?Invitation $invitation = null;

    #[Validate('required|numeric|digits:6')]
    public string $pin = '';

    public function mount(string $token): void
    {
        $this->token = $token;

        $this->invitation = Invitation::where('token', $token)->first();

        if (! $this->invitation) {
            $this->error('Convite não encontrado.', redirectTo: '/login');

            return;
        }

        if ($this->invitation->isExpired()) {
            $this->error('Este convite expirou.', redirectTo: '/login');

            return;
        }

        if ($this->invitation->isAccepted()) {
            $this->error('Este convite já foi aceito.', redirectTo: '/login');

            return;
        }
    }

    public function verify(): void
    {
        $this->validate();

        if ($this->pin !== $this->invitation->pin) {
            $this->addError('pin', 'Código PIN inválido.');

            return;
        }

        // Store validated invitation in session for Slack auth
        session(['pending_invitation_id' => $this->invitation->id]);

        // Use full page redirect for OAuth flow (navigate: false forces traditional redirect)
        $this->redirect('/auth/slack/redirect', navigate: false);
    }
};