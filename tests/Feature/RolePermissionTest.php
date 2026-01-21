<?php

declare(strict_types = 1);

use App\Brain\User\Processes\InviteUserProcess;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

test('user with role has permissions from that role', function () {
    $permission = Permission::create(['name' => 'view-user']);
    $role       = Role::create(['name' => 'suporte']);
    $role->permissions()->attach($permission);

    $user = User::factory()->create();
    $user->roles()->attach($role);

    expect($user->hasPermission('view-user'))->toBeTrue();
    expect($user->hasRole('suporte'))->toBeTrue();
});

test('super admin bypasses all permission checks', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    expect(Gate::forUser($admin)->allows('view-user'))->toBeTrue();
    expect(Gate::forUser($admin)->allows('delete-user'))->toBeTrue();
    expect(Gate::forUser($admin)->allows('view-logs'))->toBeTrue();
});

test('user without permission cannot access', function () {
    $user = User::factory()->create(['is_admin' => false]);

    expect(Gate::forUser($user)->allows('view-user'))->toBeFalse();
});

test('role assignment during user creation', function () {
    $role = Role::create(['name' => 'comercial']);

    InviteUserProcess::dispatchSync([
        'name'     => 'Test User',
        'email'    => 'newuser@example.com',
        'role_id'  => $role->id,
        'is_admin' => false,
    ]);

    $user = User::where('email', 'newuser@example.com')->first();
    expect($user->hasRole('comercial'))->toBeTrue();
});
