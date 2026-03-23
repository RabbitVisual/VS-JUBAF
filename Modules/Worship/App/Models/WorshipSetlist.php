<?php

namespace Modules\Worship\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorshipSetlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'scheduled_at',
        'leader_id',
        'description',
        'producer_notes',
        'stage_layout_pdf',
        'status',
        'background_image',
        'ministry_id',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'status' => \Modules\Worship\App\Enums\SetlistStatus::class,
    ];

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function ministry()
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function items()
    {
        return $this->hasMany(WorshipSetlistItem::class, 'setlist_id')->orderBy('order');
    }

    public function roster()
    {
        return $this->hasMany(WorshipRoster::class, 'setlist_id');
    }
}
