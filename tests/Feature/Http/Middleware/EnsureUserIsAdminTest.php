<?php

declare(strict_types = 1);

use App\Models\User;

it('allows admin users to access admin routes', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get('/users');

    $response->assertSuccessful();
});

it('denies non-admin users access to admin routes', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get('/users');

    $response->assertForbidden();
});

it('denies guest users access to admin routes', function (): void {
    $response = $this->get('/users');

    $response->assertRedirect('/login');
});

it('returns forbidden with custom message for non-admin users', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get('/users');

    $response->assertForbidden();
});

it('allows admin to access user creation route', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get('/users/create');

    $response->assertSuccessful();
});

it('denies non-admin to access user creation route', function (): void {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get('/users/create');

    $response->assertForbidden();
});

it('allows admin to access user edit route', function (): void {
    $admin     = User::factory()->create(['is_admin' => true]);
    $otherUser = User::factory()->create();

    $response = $this->actingAs($admin)->get("/users/{$otherUser->id}/edit");

    $response->assertSuccessful();
});

it('denies non-admin to access user edit route', function (): void {
    $user      = User::factory()->create(['is_admin' => false]);
    $otherUser = User::factory()->create();

    $response = $this->actingAs($user)->get("/users/{$otherUser->id}/edit");

    $response->assertForbidden();
});
