<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\User;
use Brain\Task;

/**
 * @property-read int $userId
 * @property string $email
 * @property string $name
 */
class GetUserDataTask extends Task
{
    public function handle(): self
    {
        $user = User::findOrFail($this->userId);

        $this->email = $user->email;
        $this->name  = $user->name;

        return $this;
    }
}
