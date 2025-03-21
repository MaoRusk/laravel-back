<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum BuildingLightning: string
{
    use EnumHelper;

    case LED_350_LUXES = 'LED';
    case T5 = 'T5';
    case METAL_HALIDE = 'Metal Halide';

}
