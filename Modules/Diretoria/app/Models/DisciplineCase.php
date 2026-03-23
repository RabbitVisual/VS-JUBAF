<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisciplineCase extends Model
{
    protected $fillable = [
        'user_id',
        'case_type',
        'status',
        'current_stage',
        'summary',
        'opened_by',
        'closed_at',
        'metadata',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Case type constants
    public const TYPE_ADMONITION = 'admonition';
    public const TYPE_TEMPORARY_SUSPENSION = 'temporary_suspension';
    public const TYPE_EXCLUSION = 'exclusion';
    public const TYPE_RESTORATION = 'restoration';

    // Status constants
    public const STATUS_OPENED = 'opened';
    public const STATUS_UNDER_CARE = 'under_care';
    public const STATUS_RECOMMENDED_TO_ASSEMBLY = 'recommended_to_assembly';
    public const STATUS_DECIDED_BY_ASSEMBLY = 'decided_by_assembly';
    public const STATUS_CLOSED = 'closed';

    public function member(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(DisciplineAction::class, 'discipline_case_id')->orderBy('performed_at')->orderBy('id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DisciplineCaseFile::class, 'discipline_case_id')->orderBy('created_at');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', [
            self::STATUS_OPENED,
            self::STATUS_UNDER_CARE,
            self::STATUS_RECOMMENDED_TO_ASSEMBLY,
        ]);
    }
}
