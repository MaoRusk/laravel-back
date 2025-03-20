<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum BuildingStatus: string
{
    use EnumHelper;

    case ENABLED = 'Enabled';
    case DISABLED = 'Disabled';
    case PENDING = 'Pending';
    case DRAFT = 'Draft';
}
