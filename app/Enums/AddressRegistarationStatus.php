<?php

namespace App\Enums;

use App\Trait\EnumToArray;

enum AddressRegistarationStatus: string
{
    use EnumToArray;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    
    // User action
    case CANCELLED = 'cancelled';
}
