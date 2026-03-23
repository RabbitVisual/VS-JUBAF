<?php

namespace Modules\Bible\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReadingLog extends Model
{
    protected $table = 'user_reading_logs';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'plan_day_id',
        'day_number',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BiblePlanSubscription::class);
    }

    public function planDay(): BelongsTo
    {
        return $this->belongsTo(BiblePlanDay::class, 'plan_day_id');
    }
}
