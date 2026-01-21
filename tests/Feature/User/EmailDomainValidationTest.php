<?php

declare(strict_types = 1);

use App\Rules\EmailDomain;
use Illuminate\Support\Facades\Validator;

it('allows any domain when USER_EMAIL_DOMAIN is not configured', function (): void {
    config(['app.user_email_domain' => null]);

    $validator = Validator::make(
        ['email' => 'test@anydomain.com'],
        ['email' => ['required', 'email', new EmailDomain()]]
    );

    expect($validator->passes())->toBeTrue();
});

it('allows email from configured domain', function (): void {
    config(['app.user_email_domain' => 'example.com']);

    $validator = Validator::make(
        ['email' => 'test@example.com'],
        ['email' => ['required', 'email', new EmailDomain()]]
    );

    expect($validator->passes())->toBeTrue();
});

it('rejects email from different domain', function (): void {
    config(['app.user_email_domain' => 'example.com']);

    $validator = Validator::make(
        ['email' => 'test@wrongdomain.com'],
        ['email' => ['required', 'email', new EmailDomain()]]
    );

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('email'))->toContain('example.com');
});

it('is case sensitive for domain validation', function (): void {
    config(['app.user_email_domain' => 'example.com']);

    $validator = Validator::make(
        ['email' => 'test@EXAMPLE.COM'],
        ['email' => ['required', 'email', new EmailDomain()]]
    );

    expect($validator->fails())->toBeTrue();
});
