<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\Invitation;
use App\Models\User;
use Brain\Task;

/**
 * @property-read int $userId
 * @property-read Invitation $invitation
 */
class UpdateUserInvitationIdTask extends Task
{
    public function handle(): self
    {
        $user = User::findOrFail($this->userId);

        $user->update([
            'invitation_id' => $this->invitation->id,
        ]);

        return $this;
    }
}
