<?php

declare(strict_types = 1);

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Invitation $invitation,
        public string $pin,
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $activationUrl = route('invitation.activate', ['token' => $this->invitation->token]);

        return (new MailMessage())
            ->subject('Convite para acessar o sistema')
            ->greeting('Olá!')
            ->line('Você foi convidado para acessar o sistema.')
            ->line('Para ativar sua conta, use o código PIN abaixo:')
            ->line("**Código PIN:** {$this->pin}")
            ->line('Após inserir o código PIN, você será direcionado para conectar sua conta do Slack.')
            ->action('Ativar Conta', $activationUrl)
            ->line('Este convite é válido por 24 horas.')
            ->line('Se você não solicitou este convite, ignore este email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invitation_id' => $this->invitation->id,
            'token'         => $this->invitation->token,
        ];
    }
}
