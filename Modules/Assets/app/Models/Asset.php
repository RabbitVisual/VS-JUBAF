<?php

namespace Modules\Assets\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'category_id',
        'location_id',
        'purchase_date',
        'purchase_value',
        'invoice_number',
        'status',
        'condition',
        'photo_path',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function movements()
    {
        return $this->hasMany(AssetMovement::class, 'asset_id');
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class, 'asset_id');
    }

    public function reservations()
    {
        return $this->hasMany(AssetReservation::class, 'asset_id');
    }

    public function responsibilityTerms()
    {
        return $this->hasMany(AssetResponsibilityTerm::class, 'asset_id');
    }
}
