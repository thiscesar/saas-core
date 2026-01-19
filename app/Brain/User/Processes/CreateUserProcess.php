<?php

declare(strict_types = 1);

namespace App\Brain\User\Processes;

use App\Brain\User\Tasks\CreateUserTask;
use Brain\Process;

class CreateUserProcess extends Process
{
    protected array $tasks = [
        CreateUserTask::class,
    ];
}
