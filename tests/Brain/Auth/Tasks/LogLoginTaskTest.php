<?php

declare(strict_types = 1);

use App\Brain\Auth\Tasks\LogLoginTask;
use App\Models\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class)->group('brain', 'auth');

it('creates a login record for the user', function (): void {
    $user = User::factory()->create();

    expect(Login::count())->toBe(0);

    LogLoginTask::dispatchSync([
        'user' => $user,
    ]);

    expect(Login::count())->toBe(1);

    $login = Login::first();

    expect($login)
        ->user_id->toBe($user->id)
        ->ip->not->toBeNull()
        ->user_agent->not->toBeNull();
});

it('records the correct IP address', function (): void {
    $user = User::factory()->create();

    request()->server->set('REMOTE_ADDR', '192.168.1.100');

    LogLoginTask::dispatchSync([
        'user' => $user,
    ]);

    $login = Login::first();

    expect($login->ip)->toBe('192.168.1.100');
});

it('records the correct user agent', function (): void {
    $user = User::factory()->create();

    request()->headers->set('User-Agent', 'Mozilla/5.0 Test Browser');

    LogLoginTask::dispatchSync([
        'user' => $user,
    ]);

    $login = Login::first();

    expect($login->user_agent)->toBe('Mozilla/5.0 Test Browser');
});

it('can create multiple login records for same user', function (): void {
    $user = User::factory()->create();

    LogLoginTask::dispatchSync(['user' => $user]);
    LogLoginTask::dispatchSync(['user' => $user]);
    LogLoginTask::dispatchSync(['user' => $user]);

    expect(Login::count())->toBe(3)
        ->and($user->logins()->count())->toBe(3);
});

it('associates login with correct user', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    LogLoginTask::dispatchSync(['user' => $user1]);
    LogLoginTask::dispatchSync(['user' => $user2]);

    expect($user1->logins()->count())->toBe(1)
        ->and($user2->logins()->count())->toBe(1)
        ->and(Login::count())->toBe(2);
});
