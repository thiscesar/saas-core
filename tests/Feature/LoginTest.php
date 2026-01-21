<?php

declare(strict_types = 1);

use App\Models\User;

test('active user with password can login successfully', function () {
    $user = User::factory()->create([
        'email'           => 'user@example.com',
        'password'        => bcrypt('password'),
        'password_set_at' => now(),
        'status'          => 'active',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'user@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
});

test('pending user cannot login', function () {
    User::factory()->create([
        'email'           => 'pending@example.com',
        'password'        => bcrypt('password'),
        'password_set_at' => now(),
        'status'          => 'pending',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'pending@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('deleted user cannot login', function () {
    User::factory()->trashed()->create([
        'email'           => 'deleted@example.com',
        'password'        => bcrypt('password'),
        'password_set_at' => now(),
        'status'          => 'active',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'deleted@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('user without password_set_at cannot login', function () {
    User::factory()->create([
        'email'           => 'nopassword@example.com',
        'password'        => bcrypt('password'),
        'password_set_at' => null,
        'status'          => 'active',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'nopassword@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('non-existent user cannot login', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'nonexistent@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});
