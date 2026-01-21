<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Tasks;

use App\Models\Invitation;
use App\Models\User;
use Brain\Task;

/**
 * Task FindOrCreateUserFromSlackTask
 *
 * Finds an existing user with a pending invitation or by Slack ID.
 * Only users with valid invitations or existing Slack accounts can authenticate.
 * Throws exception if no valid user is found.
 *
 * @property-read string $slackId
 * @property-read string $email
 * @property-read string $name
 * @property-read string|null $avatar
 * @property-read string $accessToken
 * @property-read string|null $refreshToken
 *
 * @property User $user
 */
class FindOrCreateUserFromSlackTask extends Task
{
    public function handle(): self
    {
        // Priority 0: Check for pending invitation in session
        $pendingInvitationId = session('pending_invitation_id');

        if ($pendingInvitationId) {
            $invitation = Invitation::find($pendingInvitationId);

            if ($invitation && $invitation->isValid() && $invitation->email === $this->email) {
                // Find the pending user associated with this invitation
                $user = User::where('invitation_id', $invitation->id)
                    ->where('status', 'pending')
                    ->first();

                if ($user) {
                    // Activate the user and link Slack account
                    $user->update([
                        'slack_id'            => $this->slackId,
                        'slack_access_token'  => $this->accessToken,
                        'slack_refresh_token' => $this->refreshToken,
                        'avatar_url'          => $this->avatar,
                        'status'              => 'active',
                        'email_verified_at'   => now(),
                    ]);

                    // Mark invitation as accepted
                    $invitation->markAsAccepted();

                    // Clear the session
                    session()->forget('pending_invitation_id');

                    $this->user = $user;

                    return $this;
                }
            }

            // Clear invalid invitation from session
            session()->forget('pending_invitation_id');
        }

        // Priority 1: Find user by slack_id (already linked and active)
        $user = User::where('slack_id', $this->slackId)->first();

        if ($user) {
            // Check if user has activated their account
            if ($user->status !== 'active') {
                throw new \Exception('Não foi possível realizar o login. Contate o suporte.');
            }

            // Update tokens for existing Slack user
            $user->update([
                'slack_access_token'  => $this->accessToken,
                'slack_refresh_token' => $this->refreshToken,
                'avatar_url'          => $this->avatar,
            ]);

            $this->user = $user;

            return $this;
        }

        // Priority 2: Check if user exists by email but not activated
        $user = User::where('email', $this->email)->first();

        if ($user && $user->status === 'pending') {
            throw new \Exception('Não foi possível realizar o login. Contate o suporte.');
        }

        // No valid user found - require invitation
        throw new \Exception('Não foi possível realizar o login. Contate o suporte.');
    }
}
