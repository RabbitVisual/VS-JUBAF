<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class diretoriaProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'justification',
        'goals',
        'status',
        'estimated_cost',
        'currency',
        'budget_details',
        'proposer_id',
        'department',
        'ministry_id',
        'reviewed_by',
        'reviewed_at',
        'diretoria_comments',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget_details' => 'array',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';

    const STATUS_SUBMITTED = 'submitted';

    const STATUS_UNDER_REVIEW = 'under_review';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    public function proposer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'proposer_id');
    }

    /**
     * Ministry (commission) this project is linked to.
     */
    public function ministry(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(diretoriaMember::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_SUBMITTED, self::STATUS_UNDER_REVIEW]);
    }
}
