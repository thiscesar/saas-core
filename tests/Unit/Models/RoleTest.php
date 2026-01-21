<?php

declare(strict_types = 1);

use App\Models\Role;

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
