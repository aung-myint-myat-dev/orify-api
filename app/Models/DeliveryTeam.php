<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryTeam extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryTeamFactory> */
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'team_name',
        'team_code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected $hidden = [
        'deleted_at',
    ];

    public function deliveryTeamMembers(): HasMany
    {
        return $this->hasMany(DeliveryTeamMember::class);
    }
}
