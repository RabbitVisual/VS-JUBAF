<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorshipSetlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'setlist_id',
        'type',
        'title',
        'content',
        'metadata',
        'song_id',
        'custom_slide_id',
        'override_key',
        'arrangement_note',
        'order',
    ];

    protected $casts = [
        'type' => 'string',
        'content' => 'array',
        'metadata' => 'array',
        'override_key' => \Modules\Worship\App\Enums\MusicalKey::class,
        'order' => 'integer',
    ];

    public function getEffectiveKeyAttribute()
    {
        return $this->override_key ?: $this->song->original_key;
    }

    public function setlist()
    {
        return $this->belongsTo(WorshipSetlist::class);
    }

    public function song()
    {
        return $this->belongsTo(WorshipSong::class);
    }

    public function customSlide()
    {
        return $this->belongsTo(WorshipCustomSlide::class, 'custom_slide_id');
    }
}
