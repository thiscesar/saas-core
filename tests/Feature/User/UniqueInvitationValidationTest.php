<?php

declare(strict_types = 1);

use App\Models\Invitation;
use App\Rules\UniqueInvitation;
use Illuminate\Support\Facades\Validator;

it('allows email when no invitation exists', function () {
    $validator = Validator::make(
        ['email' => 'test@example.com'],
        ['email' => ['required', 'email', new UniqueInvitation()]]
    );

    expect($validator->passes())->toBeTrue();
});

it('allows email when invitation is expired', function () {
    Invitation::factory()->expired()->create(['email' => 'test@example.com']);

    $validator = Validator::make(
        ['email' => 'test@example.com'],
        ['email' => ['required', 'email', new UniqueInvitation()]]
    );

    expect($validator->passes())->toBeTrue();
});

it('allows email when invitation was accepted', function () {
    Invitation::factory()->accepted()->create(['email' => 'test@example.com']);

    $validator = Validator::make(
        ['email' => 'test@example.com'],
        ['email' => ['required', 'email', new UniqueInvitation()]]
    );

    expect($validator->passes())->toBeTrue();
});

it('rejects email when valid invitation exists', function () {
    Invitation::factory()->create(['email' => 'test@example.com']);

    $validator = Validator::make(
        ['email' => 'test@example.com'],
        ['email' => ['required', 'email', new UniqueInvitation()]]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('email'))->toContain('Já existe um convite pendente');
});
