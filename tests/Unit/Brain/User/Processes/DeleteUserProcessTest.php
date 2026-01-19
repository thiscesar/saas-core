<?php

declare(strict_types = 1);

use App\Brain\User\Processes\DeleteUserProcess;
use App\Models\User;

it('deletes a user', function () {
    $user = User::factory()->create();

    DeleteUserProcess::dispatchSync([
        'userId' => $user->id,
    ]);

    expect(User::find($user->id))->toBeNull();
});

it('throws exception when user not found', function () {
    DeleteUserProcess::dispatchSync([
        'userId' => 99999,
    ]);
})->throws(Illuminate\Database\Eloquent\ModelNotFoundException::class);
