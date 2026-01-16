<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Tasks;

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
        ]);

        return $this;
    }
}
