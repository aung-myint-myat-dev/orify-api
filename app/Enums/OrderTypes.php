<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum OrderTypes: string
{
    use EnumToArray;

    case REGULAR = 'regular';
    case INITIAL = 'initial';
}
