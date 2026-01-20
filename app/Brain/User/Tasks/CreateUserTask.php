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
 * @property-read string|null $password
 * @property-read bool|null $is_admin
 * @property-read int|null $invitationId
 *
 * @property User $user
 */
class CreateUserTask extends Task
{
    public function handle(): self
    {
        $invitationId = $this->invitationId ?? null;
        $password     = $this->password ?? null;

        $data = [
            'name'     => $this->name,
            'email'    => $this->email,
            'is_admin' => $this->is_admin ?? false,
            'status'   => $invitationId ? 'pending' : 'active',
        ];

        if ($password) {
            $data['password'] = bcrypt($password);
        }

        if ($invitationId) {
            $data['invitation_id'] = $invitationId;
        }

        $this->user = User::create($data);

        return $this;
    }
}
