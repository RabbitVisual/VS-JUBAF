<?php

namespace Modules\Assets\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetResponsibilityTerm extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'user_id',
        'type',
        'generated_at',
        'signed_at',
        'file_path',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
