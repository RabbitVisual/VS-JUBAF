<?php

namespace Modules\Ministries\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryReport extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_UNDER_COUNCIL_REVIEW = 'under_council_review';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'ministry_id',
        'plan_id',
        'report_year',
        'report_month',
        'period_start',
        'period_end',
        'quantitative_data',
        'qualitative_summary',
        'prayer_requests',
        'highlights',
        'challenges',
        'status',
        'submitted_at',
        'submitted_by',
        'reviewed_at',
        'reviewed_by',
        'treasury_summary',
        'attachments',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'quantitative_data' => 'array',
        'treasury_summary' => 'array',
        'attachments' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(MinistryPlan::class, 'plan_id');
    }

    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', self::STATUS_SUBMITTED);
    }

    public function scopeForMonth($query, int $year, int $month)
    {
        return $query->where('report_year', $year)->where('report_month', $month);
    }
}
