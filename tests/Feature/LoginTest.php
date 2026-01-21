<?php

declare(strict_types = 1);

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    // Clear rate limiter before each test
    RateLimiter::clear('login:' . request()->ip());
});

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

test('pending user shows generic error', function () {
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
        ->assertHasErrors(['email' => 'Email ou senha incorretos.']);

    $this->assertGuest();
});

test('deleted user shows generic error', function () {
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
        ->assertHasErrors(['email' => 'Email ou senha incorretos.']);

    $this->assertGuest();
});

test('user without password_set_at shows generic error', function () {
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
        ->assertHasErrors(['email' => 'Email ou senha incorretos.']);

    $this->assertGuest();
});

test('non-existent user shows generic error', function () {
    Livewire::test('pages::auth.login')
        ->set('email', 'nonexistent@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email' => 'Email ou senha incorretos.']);

    $this->assertGuest();
});

test('wrong password shows generic error', function () {
    User::factory()->create([
        'email'           => 'user@example.com',
        'password'        => bcrypt('correctpassword'),
        'password_set_at' => now(),
        'status'          => 'active',
    ]);

    Livewire::test('pages::auth.login')
        ->set('email', 'user@example.com')
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email' => 'Email ou senha incorretos.']);

    $this->assertGuest();
});

test('rate limiting blocks after 5 failed attempts', function () {
    User::factory()->create([
        'email'           => 'user@example.com',
        'password'        => bcrypt('password'),
        'password_set_at' => now(),
        'status'          => 'active',
    ]);

    // Make 5 failed attempts
    for ($i = 0; $i < 5; $i++) {
        Livewire::test('pages::auth.login')
            ->set('email', 'user@example.com')
            ->set('password', 'wrongpassword')
            ->call('login');
    }

    // 6th attempt should be blocked by rate limiting
    Livewire::test('pages::auth.login')
        ->set('email', 'user@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

test('rate limiting clears on successful login', function () {
    $user = User::factory()->create([
        'email'           => 'user@example.com',
        'password'        => bcrypt('password'),
        'password_set_at' => now(),
        'status'          => 'active',
    ]);

    // Make 3 failed attempts
    for ($i = 0; $i < 3; $i++) {
        Livewire::test('pages::auth.login')
            ->set('email', 'user@example.com')
            ->set('password', 'wrongpassword')
            ->call('login');
    }

    // Successful login should clear rate limiter
    Livewire::test('pages::auth.login')
        ->set('email', 'user@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);

    // Verify rate limiter was cleared by checking we can make a new attempt
    expect(RateLimiter::attempts('login:' . request()->ip()))->toBe(0);
});
