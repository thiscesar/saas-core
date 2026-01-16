<?php

declare(strict_types = 1);

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class)->group('models');

it('can be created with factory', function (): void {
    $user = User::factory()->create();

    expect($user)
        ->toBeInstanceOf(User::class)
        ->id->toBeInt()
        ->email->toBeString()
        ->name->toBeString();

    $this->assertDatabaseHas('users', [
        'email' => $user->email,
    ]);
});

it('has correct fillable attributes', function (): void {
    $fillable = ['name', 'email', 'password', 'slack_id', 'slack_access_token', 'slack_refresh_token', 'avatar_url'];

    expect((new User())->getFillable())->toBe($fillable);
});

it('hides password and remember_token from array', function (): void {
    $user      = User::factory()->create();
    $userArray = $user->toArray();

    expect($userArray)
        ->not->toHaveKey('password')
        ->not->toHaveKey('remember_token')
        ->not->toHaveKey('slack_access_token')
        ->not->toHaveKey('slack_refresh_token');
});

it('hashes password when set', function (): void {
    $plainPassword = 'password123';

    $user = User::factory()->create([
        'password' => $plainPassword,
    ]);

    expect($user->password)
        ->not->toBe($plainPassword)
        ->and(Hash::check($plainPassword, $user->password))
        ->toBeTrue();
});

it('casts email_verified_at to datetime', function (): void {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)
        ->toBeInstanceOf(CarbonInterface::class);
});

it('can be notified', function (): void {
    $user = User::factory()->create();

    expect(method_exists($user, 'notify'))->toBeTrue();
});

it('requires name', function (): void {
    User::factory()->create([
        'name' => null,
    ]);
})->throws(Illuminate\Database\QueryException::class);

it('requires email', function (): void {
    User::factory()->create([
        'email' => null,
    ]);
})->throws(Illuminate\Database\QueryException::class);

it('requires unique email', function (): void {
    $email = 'test@example.com';

    User::factory()->create(['email' => $email]);

    User::factory()->create(['email' => $email]);
})->throws(Illuminate\Database\QueryException::class);

it('allows null password for oauth users', function (): void {
    $user = User::factory()->create([
        'password' => null,
        'slack_id' => 'U12345678',
    ]);

    expect($user->password)->toBeNull();
});

it('uses HasFactory trait', function (): void {
    expect(User::class)
        ->toUse(Illuminate\Database\Eloquent\Factories\HasFactory::class);
});

it('uses Notifiable trait', function (): void {
    expect(User::class)
        ->toUse(Illuminate\Notifications\Notifiable::class);
});
