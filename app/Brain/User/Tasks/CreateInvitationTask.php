<?php

declare(strict_types = 1);

namespace App\Brain\User\Tasks;

use App\Models\Invitation;
use Brain\Task;

/**
 * Task CreateInvitationTask
 *
 * @property-read string $email
 *
 * @property Invitation $invitation
 * @property string $pin
 */
class CreateInvitationTask extends Task
{
    public function handle(): self
    {
        $this->pin = Invitation::generatePin();

        $this->invitation = Invitation::create([
            'email'      => $this->email,
            'token'      => Invitation::generateToken(),
            'pin'        => $this->pin,
            'expires_at' => now()->addHours(24),
        ]);

        return $this;
    }
}
