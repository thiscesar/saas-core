<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\User;
use Brain\Task;

/**
 * Task UpdateUserTask
 *
 * @property-read int $userId
 * @property-read string $name
 * @property-read string $email
 * @property-read string|null $password
 * @property-read bool $is_admin
 *
 * @property User $user
 */
class UpdateUserTask extends Task
{
    public function handle(): self
    {
        $this->user = User::findOrFail($this->userId);

        $data = [
            'name'     => $this->name,
            'email'    => $this->email,
            'is_admin' => $this->is_admin ?? false,
        ];

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        $this->user->update($data);

        return $this;
    }
}
