<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum OrderDeliveryStatus: string
{
    use EnumToArray;
    
    case PENDING = 'pending';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
}