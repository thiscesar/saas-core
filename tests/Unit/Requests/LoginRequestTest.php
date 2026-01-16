<?php

declare(strict_types = 1);

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class)->group('unit', 'auth', 'validation');

it('has correct validation rules', function (): void {
    $request = new LoginRequest();

    expect($request->rules())->toBe([
        'email'    => 'required|email',
        'password' => 'required|string|min:8',
    ]);
});

it('authorizes all requests', function (): void {
    $request = new LoginRequest();

    expect($request->authorize())->toBeTrue();
});

it('validates required email', function (array $data, bool $shouldPass): void {
    $request   = new LoginRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBe($shouldPass);

    if (! $shouldPass) {
        expect($validator->errors()->has('email'))->toBeTrue();
    }
})->with([
    'missing email fails' => [
        ['password' => 'password123'],
        false,
    ],
    'empty email fails' => [
        ['email' => '', 'password' => 'password123'],
        false,
    ],
    'valid email passes' => [
        ['email' => 'test@example.com', 'password' => 'password123'],
        true,
    ],
]);

it('validates email format', function (string $email, bool $shouldPass): void {
    $request   = new LoginRequest();
    $validator = Validator::make(
        ['email' => $email, 'password' => 'password123'],
        $request->rules()
    );

    expect($validator->passes())->toBe($shouldPass);

    if (! $shouldPass) {
        expect($validator->errors()->has('email'))->toBeTrue();
    }
})->with([
    'invalid - not an email'   => ['not-an-email', false],
    'invalid - missing @'      => ['testexample.com', false],
    'invalid - missing domain' => ['test@', false],
    'invalid - double @@'      => ['test@@example.com', false],
    'valid - simple'           => ['test@example.com', true],
    'valid - subdomain'        => ['test@mail.example.com', true],
    'valid - with plus'        => ['test+tag@example.com', true],
    'valid - with dots'        => ['test.user@example.com', true],
    'valid - with numbers'     => ['user123@example.com', true],
]);

it('validates required password', function (array $data, bool $shouldPass): void {
    $request   = new LoginRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->passes())->toBe($shouldPass);

    if (! $shouldPass) {
        expect($validator->errors()->has('password'))->toBeTrue();
    }
})->with([
    'missing password fails' => [
        ['email' => 'test@example.com'],
        false,
    ],
    'empty password fails' => [
        ['email' => 'test@example.com', 'password' => ''],
        false,
    ],
    'valid password passes' => [
        ['email' => 'test@example.com', 'password' => 'password123'],
        true,
    ],
]);

it('validates password minimum length', function (string $password, bool $shouldPass): void {
    $request   = new LoginRequest();
    $validator = Validator::make(
        ['email' => 'test@example.com', 'password' => $password],
        $request->rules()
    );

    expect($validator->passes())->toBe($shouldPass);

    if (! $shouldPass) {
        expect($validator->errors()->has('password'))->toBeTrue();
    }
})->with([
    'empty string fails'            => ['', false],
    'one character fails'           => ['a', false],
    'seven characters fails'        => ['passwor', false],
    'exactly 8 characters passes'   => ['password', true],
    'more than 8 characters passes' => ['password123', true],
    'with special chars passes'     => ['p@ssw0rd!', true],
]);

it('validates multiple fields at once', function (array $data, array $expectedErrors): void {
    $request   = new LoginRequest();
    $validator = Validator::make($data, $request->rules());

    expect($validator->fails())->toBeTrue();

    foreach ($expectedErrors as $field) {
        expect($validator->errors()->has($field))->toBeTrue();
    }
})->with([
    'both missing' => [
        [],
        ['email', 'password'],
    ],
    'both invalid' => [
        ['email' => 'not-an-email', 'password' => 'short'],
        ['email', 'password'],
    ],
    'email invalid, password missing' => [
        ['email' => 'invalid'],
        ['email', 'password'],
    ],
]);
