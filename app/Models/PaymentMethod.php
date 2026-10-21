<?php

namespace App\Models;

use App\Enums\PaymentMethodTypes;
use Database\Factories\PaymentMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    /** @use HasFactory<PaymentMethodFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'method_name',
        'method_type',
        'receiver_name',
        'receiver_phone_number',
        'receiver_account_number',
        'qr_code_path',
        'is_active',
    ];

    protected $casts = [
        'method_type' => PaymentMethodTypes::class,
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];
}
