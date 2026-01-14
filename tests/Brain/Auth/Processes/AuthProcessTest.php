<?php

declare(strict_types = 1);

use App\Brain\Auth\Processes\AuthProcess;
use App\Brain\Auth\Tasks\LoginTask;
use App\Brain\Auth\Tasks\LogLoginTask;
use App\Models\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class)->group('brain', 'auth');

it('has correct task list', function (): void {
    $process = new AuthProcess();

    expect($process->getTasks())
        ->toBe([
            LoginTask::class,
            LogLoginTask::class,
        ]);
});

it('executes login and log tasks in sequence', function (): void {
    $password = 'password123';
    $user     = User::factory()->create([
        'email'    => 'test@example.com',
        'password' => $password,
    ]);

    expect(Login::count())->toBe(0);
    expect(Auth::check())->toBeFalse();

    $process = AuthProcess::dispatchSync([
        'email'    => 'test@example.com',
        'password' => $password,
    ]);

    expect(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id)
        ->and(Login::count())->toBe(1);

    $login = Login::first();

    expect($login->user_id)->toBe($user->id);
});
