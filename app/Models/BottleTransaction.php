<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BottleTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\BottleTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'address_id',
        'order_id',
        'bottle_balance_before',
        'bottle_balance_after',
        'remark',
    ];

    protected $casts = [
        'bottle_balance_before' => 'integer',
        'bottle_balance_after' => 'integer',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

}
