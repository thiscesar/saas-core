<?php

declare(strict_types = 1);

namespace App\Brain\User\Processes;

use App\Brain\User\Tasks\CreateInvitationTask;
use App\Brain\User\Tasks\DeleteOldInvitationTask;
use App\Brain\User\Tasks\GetUserDataTask;
use App\Brain\User\Tasks\SendInvitationNotificationTask;
use App\Brain\User\Tasks\UpdateUserInvitationIdTask;
use Brain\Process;

class ResendInvitationProcess extends Process
{
    protected array $tasks = [
        GetUserDataTask::class,
        DeleteOldInvitationTask::class,
        CreateInvitationTask::class,
        UpdateUserInvitationIdTask::class,
        SendInvitationNotificationTask::class,
    ];
}
