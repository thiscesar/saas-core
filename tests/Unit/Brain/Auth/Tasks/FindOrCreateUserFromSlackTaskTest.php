<?php

declare(strict_types = 1);

use App\Brain\Auth\Tasks\FindOrCreateUserFromSlackTask;
use App\Models\User;

it('throws exception when creating user without invitation', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('finds existing user by slack_id and updates tokens', function (): void {
    $user = User::factory()->create([
        'slack_id'            => 'U12345678',
        'slack_access_token'  => 'old-token',
        'slack_refresh_token' => 'old-refresh',
    ]);

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'new@example.com', // Different email, but slack_id matches
        'name'         => 'Updated Name',
        'avatar'       => 'https://example.com/new-avatar.jpg',
        'accessToken'  => 'new-token',
        'refreshToken' => 'new-refresh',
    ]);

    $task->handle();

    expect($task->user->id)->toBe($user->id);
    expect($task->user->slack_access_token)->toBe('new-token');
    expect($task->user->slack_refresh_token)->toBe('new-refresh');
    expect($task->user->email)->toBe($user->email); // Email should not change
    expect($task->user->name)->toBe($user->name);   // Name should not change
});

it('throws exception when user is pending and has slack_id', function (): void {
    $user = User::factory()->create([
        'email'    => 'test@example.com',
        'slack_id' => 'U12345678',
        'status'   => 'pending',
    ]);

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('throws exception when user is pending without slack_id', function (): void {
    $user = User::factory()->create([
        'email'    => 'test@example.com',
        'slack_id' => null,
        'status'   => 'pending',
    ]);

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('throws exception when active user without slack_id tries to login via slack', function (): void {
    $user = User::factory()->create([
        'email'    => 'test@example.com',
        'slack_id' => null,
        'status'   => 'active',
    ]);

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('updates tokens on subsequent logins', function (): void {
    $user = User::factory()->withSlackAuth()->create();

    $oldToken = $user->slack_access_token;

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => $user->slack_id,
        'email'        => $user->email,
        'name'         => $user->name,
        'avatar'       => $user->avatar_url,
        'accessToken'  => 'brand-new-token',
        'refreshToken' => 'brand-new-refresh',
    ]);

    $task->handle();

    $user->refresh();

    expect($user->slack_access_token)->not->toBe($oldToken);
    expect($user->slack_access_token)->toBe('brand-new-token');
    expect($user->slack_refresh_token)->toBe('brand-new-refresh');
});

it('requires invitation for new oauth users', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => null,
        'accessToken'  => 'xoxp-token',
        'refreshToken' => null,
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('requires invitation even with missing avatar', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => null,
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('requires invitation even with missing refresh token', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => null,
    ]);

    expect(fn () => $task->handle())->toThrow(Exception::class, 'Não foi possível realizar o login. Contate o suporte.');
});

it('does not update name when linking by slack_id', function (): void {
    $user = User::factory()->create([
        'name'     => 'Original Name',
        'slack_id' => 'U12345678',
    ]);

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => $user->email,
        'name'         => 'Different Name from Slack',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    $task->handle();

    $user->refresh();

    expect($user->name)->toBe('Original Name'); // Should not be updated
});
