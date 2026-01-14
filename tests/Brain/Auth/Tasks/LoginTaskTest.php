<?php

declare(strict_types = 1);

use App\Brain\Auth\Tasks\LoginTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class)->group('brain', 'auth');

it('successfully authenticates with correct credentials', function (): void {
    $password = 'password123';
    $user     = User::factory()->create([
        'email'    => 'test@example.com',
        'password' => $password,
    ]);

    $task = LoginTask::dispatchSync([
        'email'    => 'test@example.com',
        'password' => $password,
    ]);

    expect($task)
        ->user->toBeInstanceOf(User::class)
        ->user->id->toBe($user->id)
        ->user->email->toBe('test@example.com')
        ->and(Auth::check())->toBeTrue()
        ->and(Auth::id())->toBe($user->id);
});

it('regenerates session on successful login', function (): void {
    $password = 'password123';
    User::factory()->create([
        'email'    => 'test@example.com',
        'password' => $password,
    ]);

    $oldSessionId = session()->getId();

    LoginTask::dispatchSync([
        'email'    => 'test@example.com',
        'password' => $password,
    ]);

    $newSessionId = session()->getId();

    expect($newSessionId)->not->toBe($oldSessionId);
});

it('throws validation exception with incorrect password', function (): void {
    User::factory()->create([
        'email'    => 'test@example.com',
        'password' => 'correct-password',
    ]);

    LoginTask::dispatchSync([
        'email'    => 'test@example.com',
        'password' => 'wrong-password',
    ]);
})->throws(ValidationException::class);

it('throws validation exception with non-existent email', function (): void {
    LoginTask::dispatchSync([
        'email'    => 'nonexistent@example.com',
        'password' => 'password123',
    ]);
})->throws(ValidationException::class);

it('does not authenticate user with incorrect credentials', function (): void {
    User::factory()->create([
        'email'    => 'test@example.com',
        'password' => 'correct-password',
    ]);

    try {
        LoginTask::dispatchSync([
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);
    } catch (ValidationException) {
        // Expected exception
    }

    expect(Auth::check())->toBeFalse();
});

it('authenticates with case-sensitive email', function (): void {
    $password = 'password123';
    User::factory()->create([
        'email'    => 'Test@Example.com',
        'password' => $password,
    ]);

    $task = LoginTask::dispatchSync([
        'email'    => 'Test@Example.com',
        'password' => $password,
    ]);

    expect($task->user)->toBeInstanceOf(User::class)
        ->and(Auth::check())->toBeTrue();
});
