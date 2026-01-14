<?php

declare(strict_types = 1);

namespace App\Brain\Auth\Processes;

use App\Brain\Auth\Tasks\LoginTask;
use App\Brain\Auth\Tasks\LogLoginTask;
use Brain\Process;

class AuthProcess extends Process
{
    protected array $tasks = [
        LoginTask::class,
        LogLoginTask::class,
    ];
}
