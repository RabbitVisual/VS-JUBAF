<?php

namespace Modules\Ministries\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryServicePoint extends Model
{
    protected $fillable = [
        'user_id',
        'ministry_id',
        'points',
        'ministry_report_id',
        'period_year',
        'period_month',
    ];

    protected $casts = [
        'points' => 'integer',
        'period_year' => 'integer',
        'period_month' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function ministryReport(): BelongsTo
    {
        return $this->belongsTo(MinistryReport::class, 'ministry_report_id');
    }

    public static function getPointsForUser(int $userId): int
    {
        return (int) static::where('user_id', $userId)->sum('points');
    }
}
