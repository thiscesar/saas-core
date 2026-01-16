<?php

declare(strict_types = 1);

use App\Models\User;

it('redirects authenticated users from login to dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/login');

    $response->assertRedirect('/dashboard');
});

it('allows guests to access login page', function (): void {
    $response = $this->get('/login');

    $response->assertSuccessful();
});

it('redirects unauthenticated users from dashboard to login', function (): void {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/login');
});

it('allows authenticated users to access dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();
    $response->assertSee($user->name);
    $response->assertSee($user->email);
});

it('redirects authenticated users from slack redirect to dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('auth.slack.redirect'));

    $response->assertRedirect('/dashboard');
});

it('logs out user and redirects to login', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    expect(auth()->check())->toBeTrue();

    $response = $this->post('/logout');

    $response->assertRedirect('/login');
    $this->assertGuest();
});
