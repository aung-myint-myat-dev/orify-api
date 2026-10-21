<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum PaymentMethodTypes: string
{
    use EnumToArray;

    case CASH_ON_DELIVERY = "cash_on_delivery";
    case BANKING = "banking";
    case MOBILE_WALLET = "mobile_wallet";
}
