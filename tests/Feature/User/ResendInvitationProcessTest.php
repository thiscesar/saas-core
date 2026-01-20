<?php

declare(strict_types = 1);

use App\Brain\User\Processes\ResendInvitationProcess;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Support\Facades\Notification;

it('deletes old invitation and creates new one', function () {
    Notification::fake();

    // Create pending user with invitation
    $oldInvitation = Invitation::factory()->create(['email' => 'test@example.com']);
    $user          = User::factory()->pending()->create([
        'email'         => 'test@example.com',
        'invitation_id' => $oldInvitation->id,
    ]);

    $oldToken = $oldInvitation->token;

    // Resend invitation
    ResendInvitationProcess::dispatchSync(['userId' => $user->id]);

    // Old invitation should be deleted
    expect(Invitation::find($oldInvitation->id))->toBeNull();

    // New invitation should exist
    $newInvitation = Invitation::where('email', 'test@example.com')->first();
    expect($newInvitation)->not->toBeNull();
    expect($newInvitation->token)->not->toBe($oldToken);
});

it('updates user invitation_id with new invitation', function () {
    Notification::fake();

    $oldInvitation = Invitation::factory()->create(['email' => 'test@example.com']);
    $user          = User::factory()->pending()->create([
        'email'         => 'test@example.com',
        'invitation_id' => $oldInvitation->id,
    ]);

    $oldInvitationId = $oldInvitation->id;

    ResendInvitationProcess::dispatchSync(['userId' => $user->id]);

    $user->refresh();

    // User should have new invitation_id
    expect($user->invitation_id)->not->toBe($oldInvitationId);

    $newInvitation = Invitation::where('email', 'test@example.com')->first();
    expect($user->invitation_id)->toBe($newInvitation->id);
});

it('sends new invitation notification', function () {
    Notification::fake();

    $oldInvitation = Invitation::factory()->create(['email' => 'test@example.com']);
    $user          = User::factory()->pending()->create([
        'email'         => 'test@example.com',
        'invitation_id' => $oldInvitation->id,
    ]);

    ResendInvitationProcess::dispatchSync(['userId' => $user->id]);

    $newInvitation = Invitation::where('email', 'test@example.com')->first();

    Notification::assertSentOnDemand(UserInvitationNotification::class, function ($notification) use ($newInvitation) {
        return $notification->invitation->id === $newInvitation->id;
    });
});

it('generates different token for new invitation', function () {
    Notification::fake();

    $oldInvitation = Invitation::factory()->create(['email' => 'test@example.com']);
    $user          = User::factory()->pending()->create([
        'email'         => 'test@example.com',
        'invitation_id' => $oldInvitation->id,
    ]);

    $oldToken = $oldInvitation->token;

    ResendInvitationProcess::dispatchSync(['userId' => $user->id]);

    $newInvitation = Invitation::where('email', 'test@example.com')->first();

    expect($newInvitation->token)->not->toBe($oldToken);
    expect($newInvitation->token)->not->toBeEmpty();
});
