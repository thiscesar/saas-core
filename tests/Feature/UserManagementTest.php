<?php

declare(strict_types = 1);

use App\Models\User;
use Livewire\Livewire;

test('admin can update user name', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin);

    Livewire::test('pages::users.edit', ['user' => $user])
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->name)->toBe('New Name');
});

test('admin cannot edit another user email', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['email' => 'original@example.com']);

    $this->actingAs($admin);

    $component = Livewire::test('pages::users.edit', ['user' => $user])
        ->set('name', 'Some Name')
        ->set('email', 'newemail@example.com')
        ->call('save');

    // Email should remain unchanged even if submitted
    expect($user->fresh()->email)->toBe('original@example.com');
});

test('admin cannot edit another user password', function (): void {
    $admin       = User::factory()->create(['is_admin' => true]);
    $user        = User::factory()->create();
    $oldPassword = $user->password;

    $this->actingAs($admin);

    Livewire::test('pages::users.edit', ['user' => $user])
        ->set('name', 'Some Name')
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('save');

    // Password should remain unchanged
    expect($user->fresh()->password)->toBe($oldPassword);
});

test('user can edit own password', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::users.edit', ['user' => $user])
        ->set('name', $user->name)
        ->set('password', 'NewPassword123!')
        ->set('password_confirmation', 'NewPassword123!')
        ->call('save')
        ->assertHasNoErrors();

    expect($user->fresh()->password_set_at)->not->toBeNull();
});

test('admin can soft delete user', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->create(['status' => 'active']);

    $this->actingAs($admin);

    Livewire::test('pages::users.edit', ['user' => $user])
        ->call('delete')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->trashed())->toBeTrue();
});

test('admin cannot delete themselves', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin);

    Livewire::test('pages::users.edit', ['user' => $admin])
        ->call('delete');

    expect($admin->fresh()->trashed())->toBeFalse();
});

test('admin can restore deleted user', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->trashed()->create();

    $this->actingAs($admin);

    Livewire::test('pages::users.edit', ['user' => $user->withTrashed()->find($user->id)])
        ->call('restore')
        ->assertHasNoErrors()
        ->assertRedirect(route('users.index'));

    expect($user->fresh()->trashed())->toBeFalse();
});

test('users list shows deleted badge', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->trashed()->create(['name' => 'Deleted User']);

    $this->actingAs($admin);

    Livewire::test('pages::users.index')
        ->set('showDeleted', true)
        ->assertSee('Deleted User')
        ->assertSee('Removido');
});

test('users list hides deleted by default', function (): void {
    $admin = User::factory()->create(['is_admin' => true]);
    $user  = User::factory()->trashed()->create(['name' => 'Deleted User']);

    $this->actingAs($admin);

    Livewire::test('pages::users.index')
        ->assertDontSee('Deleted User');
});
