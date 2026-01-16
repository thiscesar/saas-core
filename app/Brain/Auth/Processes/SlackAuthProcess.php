<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Processes;

use App\Brain\Auth\Tasks\FindOrCreateUserFromSlackTask;
use App\Brain\Auth\Tasks\LoginTask;
use App\Brain\Auth\Tasks\LogLoginTask;
use Brain\Process;

class SlackAuthProcess extends Process
{
    protected array $tasks = [
        FindOrCreateUserFromSlackTask::class,
        LoginTask::class,
        LogLoginTask::class,
    ];
}
