<?php

declare(strict_types = 1);

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

it('belongs to many permissions', function (): void {
    $role        = Role::factory()->create(['name' => 'test-role']);
    $permission1 = Permission::factory()->create(['name' => 'permission1']);
    $permission2 = Permission::factory()->create(['name' => 'permission2']);

    $role->permissions()->attach([$permission1->id, $permission2->id]);

    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions->first())->toBeInstanceOf(Permission::class);
});

it('belongs to many users', function (): void {
    $role  = Role::factory()->create(['name' => 'user-role']);
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $role->users()->attach([$user1->id, $user2->id]);

    expect($role->users)->toHaveCount(2);
    expect($role->users->first())->toBeInstanceOf(User::class);
});

it('can be created', function (): void {
    $role = Role::factory()->create(['name' => 'admin']);

    expect($role)->toBeInstanceOf(Role::class);
    expect($role->name)->toBe('admin');
    expect($role->exists)->toBeTrue();
});

test('display_name returns formatted name with first letter capitalized', function (): void {
    $role = Role::factory()->create(['name' => 'desenvolvedor']);

    expect($role->name)->toBe('desenvolvedor');
    expect($role->display_name)->toBe('Desenvolvedor');
});

test('display_name works with multiple roles', function (): void {
    $roles = [
        'suporte'       => 'Suporte',
        'comercial'     => 'Comercial',
        'desenvolvedor' => 'Desenvolvedor',
        'diretor'       => 'Diretor',
    ];

    foreach ($roles as $name => $expected) {
        $role = Role::factory()->create(['name' => $name]);

        expect($role->display_name)->toBe($expected);
    }
});
