<?php

declare(strict_types = 1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

it('can create a permission', function (): void {
    $permission = Permission::factory()->create(['name' => 'edit-posts']);

    expect($permission)->toBeInstanceOf(Permission::class);
    expect($permission->name)->toBe('edit-posts');
});

it('belongs to many roles', function (): void {
    $permission = Permission::factory()->create(['name' => 'test-permission']);
    $role1      = Role::factory()->create(['name' => 'role1']);
    $role2      = Role::factory()->create(['name' => 'role2']);

    $permission->roles()->attach([$role1->id, $role2->id]);

    expect($permission->roles)->toHaveCount(2);
    expect($permission->roles->first())->toBeInstanceOf(Role::class);
});

it('belongs to many users', function (): void {
    $permission = Permission::factory()->create(['name' => 'user-permission']);
    $user1      = User::factory()->create();
    $user2      = User::factory()->create();

    $permission->users()->attach([$user1->id, $user2->id]);

    expect($permission->users)->toHaveCount(2);
    expect($permission->users->first())->toBeInstanceOf(User::class);
});

it('can be attached to multiple roles', function (): void {
    $permission = Permission::factory()->create(['name' => 'delete-posts']);
    $adminRole  = Role::factory()->create(['name' => 'admin']);
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $adminRole->permissions()->attach($permission);
    $editorRole->permissions()->attach($permission);

    expect($permission->roles()->count())->toBe(2);
    expect($permission->roles->pluck('name')->toArray())->toBe(['admin', 'editor']);
});

it('can be attached directly to users', function (): void {
    $permission = Permission::factory()->create(['name' => 'manage-users']);
    $user       = User::factory()->create();

    $user->permissions()->attach($permission);

    expect($user->permissions()->count())->toBe(1);
    expect($user->permissions->first()->name)->toBe('manage-users');
});

it('uses has factory', function (): void {
    $permission = Permission::factory()->create(['name' => 'factory-test']);

    expect($permission->exists)->toBeTrue();
    expect($permission->id)->not->toBeNull();
});
