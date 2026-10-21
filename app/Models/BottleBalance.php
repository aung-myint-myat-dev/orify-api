<?php

namespace App\Models;

use Database\Factories\BottleBalanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BottleBalance extends Model
{
    /** @use HasFactory<BottleBalanceFactory> */
    use HasFactory;

    protected $fillable = [
        'address_id',
        'balance',
    ];

    protected $casts = [
        'address_id' => 'integer',
        'balance' => 'integer',
    ];

    protected $attributes = [
        'balance' => 0,
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
