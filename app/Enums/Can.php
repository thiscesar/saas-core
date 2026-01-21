<?php

declare(strict_types = 1);

namespace App\Enums;

enum Can: string
{
    // User Management
    case ViewUser   = 'view-user';
    case CreateUser = 'create-user';
    case UpdateUser = 'update-user';
    case DeleteUser = 'delete-user';

    // System
    case ViewLogs = 'view-logs';
}
