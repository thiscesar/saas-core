<?php

declare(strict_types = 1);

use App\Brain\User\Processes\UpdateUserProcess;
use App\Models\User;

it('updates a user', function () {
    $user = User::factory()->create([
        'name'     => 'Old Name',
        'email'    => 'old@example.com',
        'is_admin' => false,
    ]);

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => 'New Name',
        'email'    => 'new@example.com',
        'password' => null,
        'is_admin' => true,
    ]);

    $user->refresh();

    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@example.com');
    expect($user->is_admin)->toBeTrue();
});

it('updates user password when provided', function () {
    $user        = User::factory()->create();
    $oldPassword = $user->password;

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => $user->name,
        'email'    => $user->email,
        'password' => 'newpassword123',
        'is_admin' => false,
    ]);

    $user->refresh();

    expect($user->password)->not->toBe($oldPassword);
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
});

it('does not update password when null', function () {
    $user        = User::factory()->create();
    $oldPassword = $user->password;

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => 'Updated Name',
        'email'    => $user->email,
        'password' => null,
        'is_admin' => false,
    ]);

    $user->refresh();

    expect($user->password)->toBe($oldPassword);
});
