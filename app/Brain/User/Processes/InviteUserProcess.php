<?php

declare(strict_types = 1);

namespace App\Brain\User\Processes;

use App\Brain\User\Tasks\CreateInvitationTask;
use App\Brain\User\Tasks\CreatePendingUserTask;
use App\Brain\User\Tasks\SendInvitationNotificationTask;
use Brain\Process;

class InviteUserProcess extends Process
{
    protected array $tasks = [
        CreateInvitationTask::class,
        CreatePendingUserTask::class,
        SendInvitationNotificationTask::class,
    ];
}
