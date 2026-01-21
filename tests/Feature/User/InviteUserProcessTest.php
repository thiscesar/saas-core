<?php

declare(strict_types = 1);

use App\Brain\User\Processes\InviteUserProcess;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\Notification;

it('creates invitation and sends notification', function (): void {
    Notification::fake();

    $result = InviteUserProcess::dispatchSync([
        'name'     => 'John Doe',
        'email'    => 'john@example.com',
        'is_admin' => false,
    ]);

    // Check invitation was created
    $invitation = Invitation::where('email', 'john@example.com')->first();
    expect($invitation)->not->toBeNull();
    expect($invitation->isValid())->toBeTrue();

    // Check user was created with pending status
    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('John Doe');
    expect($user->status)->toBe('pending');
    expect($user->invitation_id)->toBe($invitation->id);
    expect($user->password)->toBeNull();

    // Check notification was sent
    Notification::assertSentOnDemand(UserInvitationNotification::class, fn ($notification, $channels, $notifiable): bool => $notifiable->routes['mail'] === 'john@example.com'
        && $notification->invitation->id === $invitation->id);
});

it('creates admin user when is_admin is true', function (): void {
    Notification::fake();

    InviteUserProcess::dispatchSync([
        'name'     => 'Admin User',
        'email'    => 'admin@example.com',
        'is_admin' => true,
    ]);

    $user = User::where('email', 'admin@example.com')->first();
    expect($user->is_admin)->toBeTrue();
});

it('creates invitation with 24 hour expiration', function (): void {
    Notification::fake();

    InviteUserProcess::dispatchSync([
        'name'     => 'Jane Doe',
        'email'    => 'jane@example.com',
        'is_admin' => false,
    ]);

    $invitation = Invitation::where('email', 'jane@example.com')->first();

    expect($invitation->expires_at->isFuture())->toBeTrue();
    expect(now()->diffInHours($invitation->expires_at))->toBeGreaterThanOrEqual(23);
    expect(now()->diffInHours($invitation->expires_at))->toBeLessThanOrEqual(24);
});

it('creates unique token for invitation', function (): void {
    Notification::fake();

    InviteUserProcess::dispatchSync([
        'name'     => 'User One',
        'email'    => 'user1@example.com',
        'is_admin' => false,
    ]);

    InviteUserProcess::dispatchSync([
        'name'     => 'User Two',
        'email'    => 'user2@example.com',
        'is_admin' => false,
    ]);

    $invitation1 = Invitation::where('email', 'user1@example.com')->first();
    $invitation2 = Invitation::where('email', 'user2@example.com')->first();

    expect($invitation1->token)->not->toBe($invitation2->token);
});
