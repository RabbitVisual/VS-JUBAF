<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class EventSpeaker extends Model
{
    protected $fillable = ['event_id', 'name', 'role', 'photo_path', 'order'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }
}
