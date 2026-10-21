<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum OrderStatus: string
{
    use EnumToArray;
    
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case DELIVERY_FAILED = 'delivery_failed';
}
