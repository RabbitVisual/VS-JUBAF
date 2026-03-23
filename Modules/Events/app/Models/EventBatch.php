<?php

namespace Modules\Events\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventBatch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'quantity_available',
        'start_date',
        'end_date',
        'auto_switch_to_batch_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'quantity_available' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'batch_id');
    }

    public function nextBatch(): BelongsTo
    {
        return $this->belongsTo(EventBatch::class, 'auto_switch_to_batch_id');
    }
}
