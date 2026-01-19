<?php

declare(strict_types = 1);

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('shows users list to admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    User::factory()->count(5)->create();

    actingAs($admin)
        ->get(route('users.index'))
        ->assertOk()
        ->assertSeeLivewire('pages::users.index');
});

it('denies access to non-admin users', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();
});

it('allows searching users by name', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['name' => 'John Doe']);

    actingAs($admin);

    Livewire::test('pages::users.index', ['search' => 'John'])
        ->assertSee('John Doe');
});

it('allows searching users by email', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['email' => 'test@example.com']);

    actingAs($admin);

    Livewire::test('pages::users.index', ['search' => 'test@example'])
        ->assertSee('test@example.com');
});

it('shows create user page to admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin)
        ->get(route('users.create'))
        ->assertOk()
        ->assertSeeLivewire('pages::users.create');
});

it('denies access to create user page for non-admin', function () {
    $user = User::factory()->create(['is_admin' => false]);

    actingAs($user)
        ->get(route('users.create'))
        ->assertForbidden();
});

it('allows admin to create a new user', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin);

    Livewire::test('pages::users.create')
        ->set('name', 'New User')
        ->set('email', 'newuser@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('is_admin', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
});

it('shows edit user page to admin', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create();

    actingAs($admin)
        ->get(route('users.edit', $user))
        ->assertOk()
        ->assertSeeLivewire('pages::users.edit');
});

it('allows admin to edit a user', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['name' => 'Old Name']);

    actingAs($admin);

    Livewire::test('pages::users.edit', ['user' => $user])
        ->set('name', 'Updated Name')
        ->set('email', $user->email)
        ->set('is_admin', false)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->name)->toBe('Updated Name');
});

it('allows admin to delete a user', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create();

    actingAs($admin);

    Livewire::test('pages::users.index')
        ->call('deleteUser', $user->id)
        ->assertHasNoErrors();

    expect(User::find($user->id))->toBeNull();
});

it('prevents admin from deleting themselves', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    actingAs($admin);

    Livewire::test('pages::users.index')
        ->call('deleteUser', $admin->id)
        ->assertHasNoErrors();

    expect(User::find($admin->id))->not->toBeNull();
});
