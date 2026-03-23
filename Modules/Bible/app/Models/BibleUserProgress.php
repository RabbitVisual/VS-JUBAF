<?php

namespace Modules\Bible\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibleUserProgress extends Model
{
    protected $table = 'bible_user_progress';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BiblePlanSubscription::class, 'subscription_id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(BiblePlanDay::class, 'plan_day_id');
    }
}
