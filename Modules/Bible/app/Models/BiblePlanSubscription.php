<?php

namespace Modules\Bible\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiblePlanSubscription extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'projected_end_date' => 'date',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(BiblePlan::class, 'plan_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(BibleUserProgress::class, 'subscription_id');
    }

    public function prayerRequest(): BelongsTo
    {
        return $this->belongsTo(\Modules\Intercessor\App\Models\PrayerRequest::class, 'prayer_request_id');
    }
}
