<?php

declare(strict_types = 1);

use App\Brain\Auth\Tasks\FindOrCreateUserFromSlackTask;
use App\Models\User;

it('creates new user from slack data', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    $task->handle();

    expect($task->user)->toBeInstanceOf(User::class);
    expect($task->user->slack_id)->toBe('U12345678');
    expect($task->user->email)->toBe('test@example.com');
    expect($task->user->name)->toBe('Test User');
    expect($task->user->avatar_url)->toBe('https://example.com/avatar.jpg');
    expect($task->user->email_verified_at)->not->toBeNull();

    $this->assertDatabaseHas('users', [
        'slack_id' => 'U12345678',
        'email'    => 'test@example.com',
        'name'     => 'Test User',
    ]);
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

it('links slack to existing user by email', function (): void {
    $user = User::factory()->create([
        'email'    => 'test@example.com',
        'slack_id' => null, // No Slack linkage yet
    ]);

    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    $task->handle();

    expect($task->user->id)->toBe($user->id);
    expect($task->user->slack_id)->toBe('U12345678');
    expect($task->user->slack_access_token)->toBe('xoxp-token');
    expect($task->user->slack_refresh_token)->toBe('xoxr-refresh');
    expect($task->user->avatar_url)->toBe('https://example.com/avatar.jpg');
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

it('sets email_verified_at for new oauth users', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => null,
        'accessToken'  => 'xoxp-token',
        'refreshToken' => null,
    ]);

    $task->handle();

    expect($task->user->email_verified_at)->not->toBeNull();
});

it('handles missing avatar gracefully', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => null,
        'accessToken'  => 'xoxp-token',
        'refreshToken' => 'xoxr-refresh',
    ]);

    $task->handle();

    expect($task->user)->toBeInstanceOf(User::class);
    expect($task->user->avatar_url)->toBeNull();
});

it('handles missing refresh token', function (): void {
    $task = new FindOrCreateUserFromSlackTask([
        'slackId'      => 'U12345678',
        'email'        => 'test@example.com',
        'name'         => 'Test User',
        'avatar'       => 'https://example.com/avatar.jpg',
        'accessToken'  => 'xoxp-token',
        'refreshToken' => null,
    ]);

    $task->handle();

    expect($task->user)->toBeInstanceOf(User::class);
    expect($task->user->slack_refresh_token)->toBeNull();
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
