<?php

declare(strict_types = 1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class)->group('models');

it('can be created with factory', function () {
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

it('has correct fillable attributes', function () {
    $fillable = ['name', 'email', 'password'];

    expect((new User())->getFillable())->toBe($fillable);
});

it('hides password and remember_token from array', function () {
    $user      = User::factory()->create();
    $userArray = $user->toArray();

    expect($userArray)
        ->not->toHaveKey('password')
        ->not->toHaveKey('remember_token');
});

it('hashes password when set', function () {
    $plainPassword = 'password123';

    $user = User::factory()->create([
        'password' => $plainPassword,
    ]);

    expect($user->password)
        ->not->toBe($plainPassword)
        ->and(Hash::check($plainPassword, $user->password))
        ->toBeTrue();
});

it('casts email_verified_at to datetime', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)
        ->toBeInstanceOf(Illuminate\Support\Carbon::class);
});

it('can be notified', function () {
    $user = User::factory()->create();

    expect(method_exists($user, 'notify'))->toBeTrue();
});

it('requires name', function () {
    User::factory()->create([
        'name' => null,
    ]);
})->throws(Illuminate\Database\QueryException::class);

it('requires email', function () {
    User::factory()->create([
        'email' => null,
    ]);
})->throws(Illuminate\Database\QueryException::class);

it('requires unique email', function () {
    $email = 'test@example.com';

    User::factory()->create(['email' => $email]);

    User::factory()->create(['email' => $email]);
})->throws(Illuminate\Database\QueryException::class);

it('requires password', function () {
    User::factory()->create([
        'password' => null,
    ]);
})->throws(Illuminate\Database\QueryException::class);

it('uses HasFactory trait', function () {
    expect(User::class)
        ->toUse(Illuminate\Database\Eloquent\Factories\HasFactory::class);
});

it('uses Notifiable trait', function () {
    expect(User::class)
        ->toUse(Illuminate\Notifications\Notifiable::class);
});
