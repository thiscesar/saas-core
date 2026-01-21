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
 * @property-read string|null $password
 * @property-read bool $is_admin
 * @property-read int|null $role_id
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
            'is_admin' => $this->is_admin ?? false,
        ];

        // Only allow password updates if editing own account
        if ($this->password && $this->userId === auth()->id()) {
            $data['password']        = bcrypt($this->password);
            $data['password_set_at'] = now();
        }

        // Email is immutable (comes from Slack, cannot be changed)
        // Remove email from update data

        $this->user->update($data);

        // Sync role (one role per user)
        $this->user->roles()->sync($this->role_id ? [$this->role_id] : []);

        return $this;
    }
}
