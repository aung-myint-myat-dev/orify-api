<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum AddressVerificationStatus: string
{
    use EnumToArray;

    case PENDING = "pending";
    case VERIFIED = "verified";
    case REJECTED = "rejected";
}
