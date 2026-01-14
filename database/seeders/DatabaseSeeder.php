<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(RolesPermissionsSeeder::class);

        User::factory()->create([
            'name'  => 'Test User 1',
            'email' => 'test@example.com',
            'password' => 'password1',
        ]);

        User::factory()->create([
            'name'     => 'Test User 2',
            'email'    => 'test2@example.com',
            'password' => 'password2',
            'is_admin' => true,
        ]);
    }
}
