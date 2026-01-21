<?php

declare(strict_types = 1);

use App\Models\User;
use Illuminate\Support\Facades\Blade;

it('renders user avatar with image', function (): void {
    $user = User::factory()->create([
        'name'       => 'John Doe',
        'avatar_url' => 'https://example.com/avatar.jpg',
    ]);

    $view = Blade::render(
        '<x-user-avatar :user="$user" class="!w-10" />',
        ['user' => $user]
    );

    expect($view)
        ->toContain('https://example.com/avatar.jpg')
        ->toContain('!w-10');
});

it('renders user avatar with placeholder when no image', function (): void {
    $user = User::factory()->create([
        'name'       => 'Jane Smith',
        'avatar_url' => null,
    ]);

    $view = Blade::render(
        '<x-user-avatar :user="$user" class="!w-16" />',
        ['user' => $user]
    );

    expect($view)
        ->toContain('J')
        ->toContain('!w-16');
});
