<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\Can;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all permissions from Can enum
        $permissions = [];

        foreach (Can::cases() as $permission) {
            $permissions[$permission->value] = Permission::create([
                'name' => $permission->value,
            ]);
        }

        // Define roles with their permissions
        $rolesConfig = [
            'suporte' => [
                'view-user',
                'view-logs',
            ],
            'comercial' => [
                'view-user',
            ],
            'desenvolvedor' => [
                'view-user',
                'create-user',
                'update-user',
                'view-logs',
            ],
            'diretor' => [
                'view-user',
                'create-user',
                'update-user',
                'delete-user',
                'view-logs',
            ],
        ];

        foreach ($rolesConfig as $roleName => $permissionNames) {
            $role = Role::create(['name' => $roleName]);

            $permissionIds = collect($permissionNames)
                ->map(fn ($name) => $permissions[$name]->id)
                ->toArray();

            $role->permissions()->attach($permissionIds);
        }
    }
}
