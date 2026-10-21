<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum PaymentStatus: string
{
    use EnumToArray;

    case PENDING = 'pending';
    case PAID = 'paid';
    case REJECTED = 'rejected';
    case FAILED = 'failed';
}