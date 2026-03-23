<?php

namespace Modules\EBD\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EBDEvaluation extends Model
{
    protected $table = 'ebd_evaluations';

    protected $fillable = [
        'lesson_id',
        'student_id',
        'score',
        'answers',
        'feedback',
        'status',
        'graded_by',
        'completed_at',
        'graded_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'answers' => 'array',
        'completed_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_COMPLETED = 'completed';

    const STATUS_GRADED = 'graded';

    /**
     * Get the lesson
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(EBDLesson::class, 'lesson_id');
    }

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(EBDStudent::class, 'student_id');
    }

    /**
     * Get who graded
     */
    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_COMPLETED => 'Concluída',
            self::STATUS_GRADED => 'Avaliada',
            default => 'Pendente'
        };
    }
}
