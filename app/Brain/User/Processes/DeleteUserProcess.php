<?php

declare(strict_types = 1);

namespace App\Brain\User\Processes;

use App\Brain\User\Tasks\DeleteUserTask;
use Brain\Process;

class DeleteUserProcess extends Process
{
    protected array $tasks = [
        DeleteUserTask::class,
    ];
}
