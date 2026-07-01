<?php

declare(strict_types=1);

namespace App\Enums;

enum UserStatus: string
{
    case ENABLED = 'ENABLED';
    case DISABLED = 'DISABLED';
    case PENDING = 'PENDING';
    case CREATED = 'CREATED';
    case DELETED = 'DELETED';
}
