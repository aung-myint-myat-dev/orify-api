<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryTeamMember extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryTeamMemberFactory> */
    use HasFactory;

    protected $fillable = [
        'delivery_team_id',
        'employee_id',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'left_at' => 'date',
    ];

    public function deliveryTeam(): BelongsTo
    {
        return $this->belongsTo(DeliveryTeam::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
