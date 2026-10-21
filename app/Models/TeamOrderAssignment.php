<?php

namespace App\Models;

use Database\Factories\TeamOrderAssignmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamOrderAssignment extends Model
{
    /** @use HasFactory<TeamOrderAssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_team_id',
        'assigned_by',
        'assigned_at',
        'unassigned_at',
        'remark',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryTeam(): BelongsTo
    {
        return $this->belongsTo(DeliveryTeam::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
