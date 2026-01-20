<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\Invitation;
use App\Notifications\UserInvitationNotification;
use Brain\Task;
use Illuminate\Support\Facades\Notification;

/**
 * Task SendInvitationNotificationTask
 *
 * @property-read Invitation $invitation
 * @property-read string $pin
 */
class SendInvitationNotificationTask extends Task
{
    public function handle(): self
    {
        Notification::route('mail', $this->invitation->email)
            ->notify(new UserInvitationNotification($this->invitation, $this->pin));

        return $this;
    }
}
