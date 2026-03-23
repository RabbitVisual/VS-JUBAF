<?php

namespace Modules\Assets\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'responsible_id',
        'previous_location_id',
        'new_location_id',
        'type',
        'notes',
        'date',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    // Who performed the action
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Who is responsible now
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function previousLocation()
    {
        return $this->belongsTo(AssetLocation::class, 'previous_location_id');
    }

    public function newLocation()
    {
        return $this->belongsTo(AssetLocation::class, 'new_location_id');
    }
}
