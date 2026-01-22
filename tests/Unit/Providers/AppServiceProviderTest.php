<?php

declare(strict_types = 1);

use App\Enums\Can;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

it('registers gates for all permissions in Can enum', function (): void {
    foreach (Can::cases() as $permission) {
        expect(Gate::has($permission->value))->toBeTrue();
    }
});

it('allows super admin to access all gates', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    foreach (Can::cases() as $permission) {
        expect(Gate::forUser($admin)->allows($permission->value))->toBeTrue();
    }
});

it('allows user with direct permission', function (): void {
    $user       = User::factory()->create(['is_admin' => false]);
    $permission = Permission::factory()->create(['name' => Can::ViewUser->value]);

    $user->permissions()->attach($permission);

    expect(Gate::forUser($user)->allows(Can::ViewUser->value))->toBeTrue();
});

it('denies user without permission', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    expect(Gate::forUser($user)->allows(Can::ViewUser->value))->toBeFalse();
});

it('allows user with permission through role', function (): void {
    $user       = User::factory()->create(['is_admin' => false]);
    $role       = Role::factory()->create(['name' => 'editor']);
    $permission = Permission::factory()->create(['name' => Can::UpdateUser->value]);

    $role->permissions()->attach($permission);
    $user->roles()->attach($role);

    expect(Gate::forUser($user)->allows(Can::UpdateUser->value))->toBeTrue();
});

it('denies user with role but without permission', function (): void {
    $user = User::factory()->create(['is_admin' => false]);
    $role = Role::factory()->create(['name' => 'viewer']);

    $user->roles()->attach($role);

    expect(Gate::forUser($user)->allows(Can::DeleteUser->value))->toBeFalse();
});

it('checks multiple permissions correctly', function (): void {
    $user             = User::factory()->create(['is_admin' => false]);
    $viewPermission   = Permission::factory()->create(['name' => Can::ViewUser->value]);
    $createPermission = Permission::factory()->create(['name' => Can::CreateUser->value]);

    $user->permissions()->attach($viewPermission);

    expect(Gate::forUser($user)->allows(Can::ViewUser->value))->toBeTrue();
    expect(Gate::forUser($user)->allows(Can::CreateUser->value))->toBeFalse();

    $user->permissions()->attach($createPermission);

    expect(Gate::forUser($user)->allows(Can::CreateUser->value))->toBeTrue();
});

it('super admin bypasses permission checks even without explicit permissions', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    // Admin has no permissions or roles attached
    expect($admin->permissions()->count())->toBe(0);
    expect($admin->roles()->count())->toBe(0);

    // But still has access to everything
    expect(Gate::forUser($admin)->allows(Can::ViewUser->value))->toBeTrue();
    expect(Gate::forUser($admin)->allows(Can::CreateUser->value))->toBeTrue();
    expect(Gate::forUser($admin)->allows(Can::UpdateUser->value))->toBeTrue();
    expect(Gate::forUser($admin)->allows(Can::DeleteUser->value))->toBeTrue();
    expect(Gate::forUser($admin)->allows(Can::ViewLogs->value))->toBeTrue();
});

it('user can have multiple roles with different permissions', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $viewerRole = Role::factory()->create(['name' => 'viewer']);
    $editorRole = Role::factory()->create(['name' => 'editor']);

    $viewPermission   = Permission::factory()->create(['name' => Can::ViewUser->value]);
    $updatePermission = Permission::factory()->create(['name' => Can::UpdateUser->value]);

    $viewerRole->permissions()->attach($viewPermission);
    $editorRole->permissions()->attach($updatePermission);

    $user->roles()->attach([$viewerRole->id, $editorRole->id]);

    expect(Gate::forUser($user)->allows(Can::ViewUser->value))->toBeTrue();
    expect(Gate::forUser($user)->allows(Can::UpdateUser->value))->toBeTrue();
    expect(Gate::forUser($user)->allows(Can::DeleteUser->value))->toBeFalse();
});
