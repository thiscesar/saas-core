<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\User;
use Brain\Task;

/**
 * Task CreateUserTask
 *
 * @property-read string $name
 * @property-read string $email
 * @property-read string $password
 * @property-read bool $is_admin
 *
 * @property User $user
 */
class CreateUserTask extends Task
{
    public function handle(): self
    {
        $this->user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => bcrypt($this->password),
            'is_admin' => $this->is_admin ?? false,
        ]);

        return $this;
    }
}
