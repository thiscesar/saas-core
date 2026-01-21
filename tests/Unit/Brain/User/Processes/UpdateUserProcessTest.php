<?php

declare(strict_types = 1);

use App\Brain\User\Processes\UpdateUserProcess;
use App\Models\User;

it('updates a user name and admin status', function (): void {
    $user = User::factory()->create([
        'name'     => 'Old Name',
        'email'    => 'old@example.com',
        'is_admin' => false,
    ]);

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => 'New Name',
        'password' => null,
        'is_admin' => true,
    ]);

    $user->refresh();

    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('old@example.com'); // Email is immutable
    expect($user->is_admin)->toBeTrue();
});

it('updates user password when editing own account', function (): void {
    $user        = User::factory()->create();
    $oldPassword = $user->password;

    // Acting as the user themselves
    $this->actingAs($user);

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => $user->name,
        'password' => 'newpassword123',
        'is_admin' => false,
    ]);

    $user->refresh();

    expect($user->password)->not->toBe($oldPassword);
    expect(Hash::check('newpassword123', $user->password))->toBeTrue();
    expect($user->password_set_at)->not->toBeNull();
});

it('does not update password when null', function (): void {
    $user        = User::factory()->create();
    $oldPassword = $user->password;

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => 'Updated Name',
        'password' => null,
        'is_admin' => false,
    ]);

    $user->refresh();

    expect($user->password)->toBe($oldPassword);
});

it('syncs user role when role_id is provided', function (): void {
    $role1 = App\Models\Role::create(['name' => 'role-1']);
    $role2 = App\Models\Role::create(['name' => 'role-2']);

    $user = User::factory()->create();
    $user->roles()->attach($role1);

    expect($user->hasRole('role-1'))->toBeTrue();

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => $user->name,
        'password' => null,
        'role_id'  => $role2->id,
        'is_admin' => false,
    ]);

    $user->refresh();

    expect($user->hasRole('role-2'))->toBeTrue();
    expect($user->hasRole('role-1'))->toBeFalse();
});

it('removes all roles when role_id is null', function (): void {
    $role = App\Models\Role::create(['name' => 'test-role']);

    $user = User::factory()->create();
    $user->roles()->attach($role);

    expect($user->roles)->toHaveCount(1);

    UpdateUserProcess::dispatchSync([
        'userId'   => $user->id,
        'name'     => $user->name,
        'password' => null,
        'role_id'  => null,
        'is_admin' => false,
    ]);

    $user->refresh();

    expect($user->roles)->toHaveCount(0);
});
