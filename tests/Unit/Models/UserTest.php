<?php

declare(strict_types = 1);

use App\Models\User;

it('returns correct initials from user name', function (): void {
    $user = User::factory()->make(['name' => 'João Silva']);

    expect($user->getInitials())->toBe('J');
});

it('returns uppercase initials', function (): void {
    $user = User::factory()->make(['name' => 'maria']);

    expect($user->getInitials())->toBe('M');
});

it('handles single character names', function (): void {
    $user = User::factory()->make(['name' => 'A']);

    expect($user->getInitials())->toBe('A');
});

it('returns avatar url when set', function (): void {
    $user = User::factory()->make(['avatar_url' => 'https://example.com/avatar.jpg']);

    expect($user->getAvatarUrl())->toBe('https://example.com/avatar.jpg');
});

it('returns null when avatar url is not set', function (): void {
    $user = User::factory()->make(['avatar_url' => null]);

    expect($user->getAvatarUrl())->toBeNull();
});

it('belongs to an invitation', function (): void {
    $invitation = App\Models\Invitation::factory()->create();
    $user       = User::factory()->create(['invitation_id' => $invitation->id]);

    expect($user->invitation)->toBeInstanceOf(App\Models\Invitation::class);
    expect($user->invitation->id)->toBe($invitation->id);
});

it('can have null invitation', function (): void {
    $user = User::factory()->create(['invitation_id' => null]);

    expect($user->invitation)->toBeNull();
});

it('checks if user has direct permission', function (): void {
    $user       = User::factory()->create();
    $permission = App\Models\Permission::factory()->create(['name' => 'edit-posts']);

    $user->permissions()->attach($permission);

    expect($user->hasPermission('edit-posts'))->toBeTrue();
    expect($user->hasPermission('delete-posts'))->toBeFalse();
});
