<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\User;
use Brain\Task;

/**
 * Task DeleteUserTask
 *
 * @property-read int $userId
 */
class DeleteUserTask extends Task
{
    public function handle(): self
    {
        $user = User::findOrFail($this->userId);

        $user->delete();

        return $this;
    }
}
