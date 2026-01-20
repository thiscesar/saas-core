<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\Invitation;
use App\Models\User;
use Brain\Task;

/**
 * @property-read int $userId
 */
class DeleteOldInvitationTask extends Task
{
    public function handle(): self
    {
        $user = User::findOrFail($this->userId);

        if ($user->invitation_id) {
            Invitation::find($user->invitation_id)?->delete();
        }

        return $this;
    }
}
