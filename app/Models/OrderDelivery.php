<?php

namespace App\Models;

use App\Enums\OrderDeliveryStatus;
use Database\Factories\OrderDeliveryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDelivery extends Model
{
    /** @use HasFactory<OrderDeliveryFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivered_by',
        'delivered_bottles_quantity',
        'status',
        'delivered_at',
        'failure_reason',
        'delivery_note',
    ];

    protected $casts = [
        'status' => OrderDeliveryStatus::class,
        'delivered_bottles_quantity' => 'integer',
        'delivered_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => OrderDeliveryStatus::PENDING->value,
        'delivered_bottles_quantity' => 0,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveredBy()
    {
        return $this->belongsTo(DeliveryTeam::class, 'delivered_by');
    }
}
