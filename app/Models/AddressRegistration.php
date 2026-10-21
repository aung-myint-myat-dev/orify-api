<?php

namespace App\Models;

use App\Enums\AddressRegistarationStatus;
use Database\Factories\AddressRegistrationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AddressRegistration extends Model
{
    /** @use HasFactory<AddressRegistrationFactory> */
    use HasFactory;

    protected $fillable = [
        'address_id',
        'registration_number',
        'initial_bottle_quantity',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remark',
    ];

    protected $casts = [
        'initial_bottle_quantity' => 'integer',
        'status' => AddressRegistarationStatus::class,
        'reviewed_at' => 'datetime',
    ];

    protected $attributes = [
        'initial_bottle_quantity' => 0,
        'status' => AddressRegistarationStatus::PENDING->value,
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
