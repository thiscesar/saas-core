<?php

declare(strict_types = 1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hasRole returns true when user has role', function () {
    $role = Role::create(['name' => 'test-role']);
    $user = User::factory()->create();
    $user->roles()->attach($role);

    expect($user->hasRole('test-role'))->toBeTrue();
    expect($user->hasRole('other-role'))->toBeFalse();
});

test('hasPermission checks direct and role permissions', function () {
    $permission = Permission::create(['name' => 'test-permission']);
    $role       = Role::create(['name' => 'test-role']);
    $role->permissions()->attach($permission);

    $user = User::factory()->create();
    $user->roles()->attach($role);

    expect($user->hasPermission('test-permission'))->toBeTrue();
});

test('isSuperAdmin returns true for admin users', function () {
    $admin   = User::factory()->create(['is_admin' => true]);
    $regular = User::factory()->create(['is_admin' => false]);

    expect($admin->isSuperAdmin())->toBeTrue();
    expect($regular->isSuperAdmin())->toBeFalse();
});

test('syncRoles replaces all existing roles', function () {
    $role1 = Role::create(['name' => 'role-1']);
    $role2 = Role::create(['name' => 'role-2']);
    $role3 = Role::create(['name' => 'role-3']);

    $user = User::factory()->create();
    $user->roles()->attach([$role1->id, $role2->id]);

    expect($user->roles)->toHaveCount(2);

    $user->syncRoles([$role3->id]);

    $user->refresh();
    expect($user->roles)->toHaveCount(1);
    expect($user->hasRole('role-3'))->toBeTrue();
    expect($user->hasRole('role-1'))->toBeFalse();
});
