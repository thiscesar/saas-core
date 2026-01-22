<?php

declare(strict_types = 1);

use App\Brain\User\Processes\CreateUserProcess;
use App\Models\User;

it('creates a new user', function (): void {
    CreateUserProcess::dispatchSync([
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => 'password123',
        'is_admin' => false,
    ]);

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();

    $user = User::where('email', 'test@example.com')->first();
    expect($user->name)->toBe('Test User');
    expect($user->is_admin)->toBeFalse();
});

it('creates an admin user', function (): void {
    CreateUserProcess::dispatchSync([
        'name'     => 'Admin User',
        'email'    => 'admin@example.com',
        'password' => 'password123',
        'is_admin' => true,
    ]);

    $user = User::where('email', 'admin@example.com')->first();
    expect($user->is_admin)->toBeTrue();
});

it('hashes the password when creating a user', function (): void {
    CreateUserProcess::dispatchSync([
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => 'password123',
        'is_admin' => false,
    ]);

    $user = User::where('email', 'test@example.com')->first();
    expect($user->password)->not->toBe('password123');
    expect(Hash::check('password123', $user->password))->toBeTrue();
});

it('creates a pending user with invitation id', function (): void {
    $invitation = App\Models\Invitation::factory()->create();

    CreateUserProcess::dispatchSync([
        'name'         => 'Pending User',
        'email'        => 'pending@example.com',
        'invitationId' => $invitation->id,
    ]);

    $user = User::where('email', 'pending@example.com')->first();
    expect($user->status)->toBe('pending');
    expect($user->invitation_id)->toBe($invitation->id);
});

it('creates an active user without invitation id', function (): void {
    CreateUserProcess::dispatchSync([
        'name'     => 'Active User',
        'email'    => 'active@example.com',
        'password' => 'password123',
    ]);

    $user = User::where('email', 'active@example.com')->first();
    expect($user->status)->toBe('active');
    expect($user->invitation_id)->toBeNull();
});
