<?php

declare(strict_types = 1);

use App\Models\User;

test('unauthenticated users are redirected to login', function (): void {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('authenticated users are redirected to dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/');

    $response->assertRedirect('/dashboard');
});
