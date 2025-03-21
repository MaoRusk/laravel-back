<?php

namespace App\Enums;

use App\Interfaces\Enums\HasTranslation;
use App\Traits\EnumHelper;

enum BuildingTenancy: string
{
    use EnumHelper;

    case SINGLE = 'Single';
    case MULTITENANT = 'Multitenant';

}
