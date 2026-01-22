<?php

declare(strict_types = 1);

use App\Models\Login;
use App\Models\User;

it('can create a login record', function (): void {
    $user = User::factory()->create();

    $login = Login::create([
        'user_id'    => $user->id,
        'ip'         => '127.0.0.1',
        'user_agent' => 'Mozilla/5.0',
    ]);

    expect($login)->toBeInstanceOf(Login::class);
    expect($login->user_id)->toBe($user->id);
    expect($login->ip)->toBe('127.0.0.1');
    expect($login->user_agent)->toBe('Mozilla/5.0');
});

it('belongs to a user', function (): void {
    $user = User::factory()->create();

    $login = Login::create([
        'user_id'    => $user->id,
        'ip'         => '192.168.1.1',
        'user_agent' => 'Chrome',
    ]);

    expect($login->user)->toBeInstanceOf(User::class);
    expect($login->user->id)->toBe($user->id);
});

it('has fillable attributes', function (): void {
    $login = new Login();

    expect($login->getFillable())->toBe([
        'user_id',
        'ip',
        'user_agent',
    ]);
});

it('has timestamps', function (): void {
    $user = User::factory()->create();

    $login = Login::create([
        'user_id'    => $user->id,
        'ip'         => '10.0.0.1',
        'user_agent' => 'Safari',
    ]);

    expect($login->created_at)->not->toBeNull();
    expect($login->updated_at)->not->toBeNull();
});
