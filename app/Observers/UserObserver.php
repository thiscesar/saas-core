<?php

declare(strict_types = 1);

namespace App\Observers;

use App\Models\Invitation;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if ($user->invitation_id && $user->status === 'pending') {
            Invitation::find($user->invitation_id)?->delete();
        }
    }
}
