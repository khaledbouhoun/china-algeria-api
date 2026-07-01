<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusType: string
{
    case ITEM = 'ITEM';
    case PACKAGE_ITEM = 'PACKAGE_ITEM';
    case PACKAGE = 'PACKAGE';
    case INSPECTION = 'INSPECTION';
}
