<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum BottleTransactionTypes: string
{
    use EnumToArray;

    case REGULAR = "regular";
    case INITIAL_REGISTRATION = "initial_registration";
}
