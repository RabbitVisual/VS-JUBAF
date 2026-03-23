<?php

namespace Modules\EBD\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EBDAttendance extends Model
{
    protected $table = 'ebd_attendance';

    protected $fillable = [
        'lesson_id',
        'student_id',
        'status',
        'arrival_time',
        'notes',
        'registered_by',
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
    ];

    // Status constants
    const STATUS_PRESENT = 'present';

    const STATUS_ABSENT = 'absent';

    const STATUS_LATE = 'late';

    const STATUS_EXCUSED = 'excused';

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
     * Get who registered
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PRESENT => 'Presente',
            self::STATUS_ABSENT => 'Ausente',
            self::STATUS_LATE => 'Atrasado',
            self::STATUS_EXCUSED => 'Justificado',
            default => 'Ausente'
        };
    }
}
