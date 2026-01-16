<?php

declare(strict_types = 1);

use App\Models\Login;
use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

it('redirects to slack oauth', function (): void {
    $response = $this->get(route('auth.slack.redirect'));

    $response->assertRedirect();
});

it('creates new user from slack callback', function (): void {
    $abstractUser = mock(SocialiteUserContract::class);
    $abstractUser->shouldReceive('getId')->andReturn('U12345678');
    $abstractUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('New User');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $abstractUser->token        = 'xoxp-slack-token';
    $abstractUser->refreshToken = 'xoxr-refresh-token';

    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($abstractUser);

    $response = $this->get(route('auth.slack.callback'));

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'slack_id' => 'U12345678',
        'email'    => 'newuser@example.com',
        'name'     => 'New User',
    ]);

    $this->assertAuthenticated();
});

it('logs in existing slack user', function (): void {
    $user = User::factory()->create([
        'slack_id'            => 'U12345678',
        'email'               => 'existing@example.com',
        'slack_access_token'  => 'old-token',
        'slack_refresh_token' => 'old-refresh',
    ]);

    $abstractUser = mock(SocialiteUserContract::class);
    $abstractUser->shouldReceive('getId')->andReturn('U12345678');
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Existing User');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $abstractUser->token        = 'new-token';
    $abstractUser->refreshToken = 'new-refresh';

    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($abstractUser);

    $response = $this->get(route('auth.slack.callback'));

    $response->assertRedirect('/dashboard');

    $user->refresh();

    expect($user->slack_access_token)->toBe('new-token');
    expect($user->slack_refresh_token)->toBe('new-refresh');

    $this->assertAuthenticatedAs($user);
});

it('links slack to existing email account', function (): void {
    $user = User::factory()->create([
        'email'    => 'existing@example.com',
        'slack_id' => null,
    ]);

    $abstractUser = mock(SocialiteUserContract::class);
    $abstractUser->shouldReceive('getId')->andReturn('U12345678');
    $abstractUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Existing User');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $abstractUser->token        = 'xoxp-token';
    $abstractUser->refreshToken = 'xoxr-refresh';

    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($abstractUser);

    $response = $this->get(route('auth.slack.callback'));

    $response->assertRedirect('/dashboard');

    $user->refresh();

    expect($user->slack_id)->toBe('U12345678');
    expect($user->slack_access_token)->toBe('xoxp-token');

    $this->assertAuthenticatedAs($user);
});

it('creates login record after oauth', function (): void {
    $abstractUser = mock(SocialiteUserContract::class);
    $abstractUser->shouldReceive('getId')->andReturn('U12345678');
    $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $abstractUser->token        = 'xoxp-token';
    $abstractUser->refreshToken = 'xoxr-refresh';

    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($abstractUser);

    $this->get(route('auth.slack.callback'));

    $this->assertDatabaseHas('logins', [
        'user_id' => User::where('slack_id', 'U12345678')->first()->id,
    ]);

    $login = Login::latest()->first();

    expect($login)->not->toBeNull();
    expect($login->user_id)->toBe(User::where('slack_id', 'U12345678')->first()->id);
});

it('handles invalid state exception', function (): void {
    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andThrow(new InvalidStateException());

    $response = $this->get(route('auth.slack.callback'));

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'Autenticação inválida. Por favor, tente novamente.');

    $this->assertGuest();
});

it('handles general oauth exceptions', function (): void {
    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andThrow(new Exception('OAuth error'));

    $response = $this->get(route('auth.slack.callback'));

    $response->assertRedirect('/login');
    $response->assertSessionHas('error', 'Erro ao autenticar com Slack.');

    $this->assertGuest();
});

it('regenerates session after oauth login', function (): void {
    $abstractUser = mock(SocialiteUserContract::class);
    $abstractUser->shouldReceive('getId')->andReturn('U12345678');
    $abstractUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->shouldReceive('getAvatar')->andReturn('https://example.com/avatar.jpg');
    $abstractUser->token        = 'xoxp-token';
    $abstractUser->refreshToken = 'xoxr-refresh';

    Socialite::shouldReceive('driver')
        ->with('slack')
        ->andReturnSelf()
        ->shouldReceive('stateless')
        ->andReturnSelf()
        ->shouldReceive('user')
        ->andReturn($abstractUser);

    $oldSessionId = session()->getId();

    $this->get(route('auth.slack.callback'));

    $newSessionId = session()->getId();

    expect($newSessionId)->not->toBe($oldSessionId);
});
