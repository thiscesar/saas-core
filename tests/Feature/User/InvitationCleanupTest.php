<?php

declare(strict_types = 1);

use App\Models\Invitation;
use App\Models\User;

it('deletes invitation when pending user is deleted', function () {
    // Create invitation and pending user
    $invitation = Invitation::factory()->create(['email' => 'test@example.com']);
    $user       = User::factory()->pending()->create([
        'email'         => 'test@example.com',
        'invitation_id' => $invitation->id,
    ]);

    // Verify both exist
    expect(Invitation::find($invitation->id))->not->toBeNull();
    expect(User::find($user->id))->not->toBeNull();

    // Delete the pending user
    $user->delete();

    // Invitation should be deleted
    expect(Invitation::find($invitation->id))->toBeNull();
});

it('does not delete invitation when active user is deleted', function () {
    // Create invitation and active user (invitation accepted)
    $invitation = Invitation::factory()->create([
        'email'       => 'active@example.com',
        'accepted_at' => now(),
    ]);

    $user = User::factory()->create([
        'email'         => 'active@example.com',
        'invitation_id' => $invitation->id,
        'status'        => 'active',
    ]);

    // Delete the active user
    $user->delete();

    // Invitation should still exist (historical record)
    expect(Invitation::find($invitation->id))->not->toBeNull();
});

it('handles deletion of user without invitation', function () {
    // Create user without invitation (e.g., created via Slack)
    $user = User::factory()->create([
        'email'         => 'slack-user@example.com',
        'invitation_id' => null,
        'status'        => 'active',
    ]);

    // This should not throw any errors
    $user->delete();

    // User should be deleted
    expect(User::find($user->id))->toBeNull();
});

it('allows new invitation after deleting pending user', function () {
    // Create invitation and pending user
    $invitation = Invitation::factory()->create(['email' => 'reuse@example.com']);
    $user       = User::factory()->pending()->create([
        'email'         => 'reuse@example.com',
        'invitation_id' => $invitation->id,
    ]);

    // Delete the pending user (which should delete invitation)
    $user->delete();

    // Should be able to create a new invitation with the same email
    $newInvitation = Invitation::factory()->create(['email' => 'reuse@example.com']);
    expect($newInvitation)->not->toBeNull();
    expect($newInvitation->email)->toBe('reuse@example.com');

    // Verify old invitation is gone
    expect(Invitation::find($invitation->id))->toBeNull();
});
