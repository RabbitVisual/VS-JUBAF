<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachingRule extends Model
{
    protected $fillable = [
        'trigger_key',
        'condition_type',
        'condition_params',
        'insight_id',
        'level',
        'medal_id',
        'priority',
        'is_active',
        'message_override',
    ];

    protected $casts = [
        'condition_params' => 'array',
        'is_active' => 'boolean',
    ];

    public function insight(): BelongsTo
    {
        return $this->belongsTo(Insight::class, 'insight_id');
    }

    public function medal(): BelongsTo
    {
        return $this->belongsTo(Medal::class);
    }
}
