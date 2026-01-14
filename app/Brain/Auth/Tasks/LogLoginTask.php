<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Tasks;

use App\Models\User;
use Brain\Task;

/**
 * Task LogLoginTask
 *
 * @property-read User $user
 */
class LogLoginTask extends Task
{
    public function handle(): self
    {
        $this->user->logins()->create([
            'ip'         => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $this;
    }
}
