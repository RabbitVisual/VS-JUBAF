<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventCertificate extends Model
{
    protected $fillable = [
        'event_id',
        'template_html',
        'release_after',
    ];

    protected $casts = [
        'release_after' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
