<?php

declare(strict_types=1);

namespace App\Enums;

enum ZoneType: string
{
    case ZONE_A = 'ZONE_A';
    case ZONE_B = 'ZONE_B';
    case ZONE_C = 'ZONE_C';
    case EVERYWHERE = 'EVERYWHERE';
}
