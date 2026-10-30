<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderTypes;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'address_id',
        'order_type',
        'water_bottle_quantity',
        'water_bottle_unit_price',
        'water_total_amount',
        'new_empty_bottle_quantity',
        'empty_bottle_unit_price',
        'new_empty_bottle_total_amount',
        'subtotal',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'order_status',
        'expected_delivery_date',
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
        'customer_note',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
        'delivery_failure_reason',
        'hidden_at',
    ];

    protected $casts = [
        'order_type' => OrderTypes::class,
        'order_status' => OrderStatus::class,
        'water_bottle_quantity' => 'integer',
        'water_bottle_unit_price' => 'decimal:2',
        'water_total_amount' => 'decimal:2',
        'new_empty_bottle_quantity' => 'integer',
        'empty_bottle_unit_price' => 'decimal:2',
        'new_empty_bottle_total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expected_delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'hidden_at' => 'datetime',
    ];

    protected $attributes = [
        'order_type' => OrderTypes::REGULAR->value,
        'water_bottle_quantity' => 1,
        'water_bottle_unit_price' => 0,
        'water_total_amount' => 0,
        'new_empty_bottle_quantity' => 0,
        'empty_bottle_unit_price' => 0,
        'new_empty_bottle_total_amount' => 0,
        'subtotal' => 0,
        'delivery_fee' => 0,
        'discount_amount' => 0,
        'total_amount' => 0,
        'order_status' => OrderStatus::PENDING->value,
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
