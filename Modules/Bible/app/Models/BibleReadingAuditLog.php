<?php

namespace Modules\Bible\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BibleReadingAuditLog extends Model
{
    protected $table = 'bible_reading_audit_log';

    protected $fillable = [
        'user_id',
        'subscription_id',
        'action',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public const ACTION_RECALCULATE_ROUTE = 'recalculate_route';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(BiblePlanSubscription::class);
    }
}
