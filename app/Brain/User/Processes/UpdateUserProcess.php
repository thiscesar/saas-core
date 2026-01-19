<?php

declare(strict_types = 1);

namespace App\Brain\User\Processes;

use App\Brain\User\Tasks\UpdateUserTask;
use Brain\Process;

class UpdateUserProcess extends Process
{
    protected array $tasks = [
        UpdateUserTask::class,
    ];
}
