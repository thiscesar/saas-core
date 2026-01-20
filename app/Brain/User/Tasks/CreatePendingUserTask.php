<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\Invitation;
use App\Models\User;
use Brain\Task;

/**
 * Task CreatePendingUserTask
 *
 * @property-read string $name
 * @property-read Invitation $invitation
 * @property-read bool|null $is_admin
 *
 * @property User $user
 */
class CreatePendingUserTask extends Task
{
    public function handle(): self
    {
        $this->user = User::create([
            'name'          => $this->name,
            'email'         => $this->invitation->email,
            'is_admin'      => $this->is_admin ?? false,
            'status'        => 'pending',
            'invitation_id' => $this->invitation->id,
        ]);

        return $this;
    }
}
