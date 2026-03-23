<?php

namespace Modules\Marketplace\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickupLocation extends Model
{
    protected $table = 'marketplace_pickup_locations';

    protected $fillable = [
        'name',
        'address',
        'instructions',
        'availability',
        'is_active',
    ];

    protected $casts = [
        'availability' => 'array',
        'is_active' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'pickup_location_id');
    }
}
