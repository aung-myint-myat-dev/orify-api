<?php

namespace App\Models;

use App\Enums\AddressVerificationStatus;
use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'label',
        'recipient_name',
        'recipient_phone_number',
        'state',
        'city',
        'township',
        'ward',
        'street',
        'house_number',
        'building_number',
        'room_number',
        'floor_number',
        'landmark',
        'remark',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_default' => 'boolean',
        'verification_status' => AddressVerificationStatus::class,
        'verified_at' => 'datetime',
    ];

    protected $attributes = [
        'is_default' => false,
        'verification_status' => AddressVerificationStatus::PENDING,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bottleBalance(): HasOne
    {
        return $this->hasOne(BottleBalance::class);
    }
}
