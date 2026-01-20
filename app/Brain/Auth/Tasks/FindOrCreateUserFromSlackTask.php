<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Tasks;

use App\Models\Invitation;
use App\Models\User;
use Brain\Task;

/**
 * Task FindOrCreateUserFromSlackTask
 *
 * Finds an existing user by Slack ID or email, or creates a new user from Slack OAuth data.
 * Handles account linking when a user logs in via Slack with an email that already exists.
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

        // Priority 1: Find user by slack_id (already linked)
        $user = User::where('slack_id', $this->slackId)->first();

        if ($user) {
            // Update tokens for existing Slack user
            $user->update([
                'slack_access_token'  => $this->accessToken,
                'slack_refresh_token' => $this->refreshToken,
            ]);

            $this->user = $user;

            return $this;
        }

        // Priority 2: Find user by email (account linking scenario)
        $user = User::where('email', $this->email)->first();

        if ($user) {
            // Link Slack to existing account
            $user->update([
                'slack_id'            => $this->slackId,
                'slack_access_token'  => $this->accessToken,
                'slack_refresh_token' => $this->refreshToken,
                'avatar_url'          => $this->avatar,
            ]);

            $this->user = $user;

            return $this;
        }

        // Priority 3: Create new user from Slack data
        $this->user = User::create([
            'name'                => $this->name,
            'email'               => $this->email,
            'slack_id'            => $this->slackId,
            'slack_access_token'  => $this->accessToken,
            'slack_refresh_token' => $this->refreshToken,
            'avatar_url'          => $this->avatar,
            'email_verified_at'   => now(), // OAuth emails are verified by provider
            'status'              => 'active',
        ]);

        return $this;
    }
}
