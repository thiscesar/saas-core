<?php

declare(strict_types = 1);

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Notification;

it('uses mail channel for delivery', function (): void {
    $invitation   = Invitation::factory()->create();
    $notification = new UserInvitationNotification($invitation, '1234');

    $channels = $notification->via(new User());

    expect($channels)->toBe(['mail']);
});

it('returns array representation with invitation data', function (): void {
    $invitation = Invitation::factory()->create([
        'token' => 'test-token-123',
    ]);

    $notification = new UserInvitationNotification($invitation, '1234');

    $array = $notification->toArray(new User());

    expect($array)->toHaveKey('invitation_id');
    expect($array)->toHaveKey('token');
    expect($array['invitation_id'])->toBe($invitation->id);
    expect($array['token'])->toBe('test-token-123');
});

it('creates mail message with correct subject', function (): void {
    $invitation   = Invitation::factory()->create();
    $notification = new UserInvitationNotification($invitation, '5678');

    $mailMessage = $notification->toMail(new User());

    expect($mailMessage)->toBeInstanceOf(MailMessage::class);
    expect($mailMessage->subject)->toBe('Convite para acessar o sistema');
});

it('creates mail message with pin code', function (): void {
    $invitation   = Invitation::factory()->create();
    $pin          = '9876';
    $notification = new UserInvitationNotification($invitation, $pin);

    $mailMessage = $notification->toMail(new User());

    expect($mailMessage->introLines)->toContain('Você foi convidado para acessar o sistema.');
    expect($mailMessage->introLines)->toContain('Para ativar sua conta, use o código PIN abaixo:');
    expect($mailMessage->introLines)->toContain("**Código PIN:** {$pin}");
});

it('creates mail message with activation link', function (): void {
    $invitation   = Invitation::factory()->create(['token' => 'unique-token']);
    $notification = new UserInvitationNotification($invitation, '1111');

    $mailMessage = $notification->toMail(new User());

    $expectedUrl = route('invitation.activate', ['token' => 'unique-token']);

    expect($mailMessage->actionText)->toBe('Ativar Conta');
    expect($mailMessage->actionUrl)->toBe($expectedUrl);
});

it('creates mail message with expiration notice', function (): void {
    $invitation   = Invitation::factory()->create();
    $notification = new UserInvitationNotification($invitation, '2222');

    $mailMessage = $notification->toMail(new User());

    expect($mailMessage->outroLines)->toContain('Este convite é válido por 24 horas.');
    expect($mailMessage->outroLines)->toContain('Se você não solicitou este convite, ignore este email.');
});

it('implements should queue interface', function (): void {
    $invitation   = Invitation::factory()->create();
    $notification = new UserInvitationNotification($invitation, '3333');

    expect($notification)->toBeInstanceOf(Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('can be sent to a user', function (): void {
    Notification::fake();

    $user       = User::factory()->create();
    $invitation = Invitation::factory()->create();
    $pin        = '4444';

    $user->notify(new UserInvitationNotification($invitation, $pin));

    Notification::assertSentTo($user, UserInvitationNotification::class);
});
