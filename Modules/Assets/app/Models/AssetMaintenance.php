<?php

namespace Modules\Assets\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'supplier_name',
        'supplier_id',
        'description',
        'cost',
        'start_date',
        'expected_return_date',
        'actual_return_date',
        'status',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'start_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }
}
