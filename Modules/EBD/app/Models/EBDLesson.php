<?php

namespace Modules\EBD\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBDLesson extends Model
{
    use SoftDeletes;

    protected $table = 'ebd_lessons';

    protected $fillable = [
        'class_id',
        'course_id',
        'title',
        'description',
        'order',
        'video_url',
        'lesson_date',
        'lesson_time',
        'bible_book',
        'bible_chapter',
        'bible_verses',
        'bible_version',
        'objective',
        'introduction',
        'development',
        'conclusion',
        'application',
        'status',
        'created_by',
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'lesson_time' => 'datetime',
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the class
     */
    public function ebdClass(): BelongsTo
    {
        return $this->belongsTo(EBDClass::class, 'class_id');
    }

    /**
     * Get the course
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(EBDCourse::class, 'course_id');
    }

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get attendance records
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(EBDAttendance::class, 'lesson_id');
    }

    /**
     * Get evaluations
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(EBDEvaluation::class, 'lesson_id');
    }

    /**
     * Get materials
     */
    public function materials(): HasMany
    {
        return $this->hasMany(EBDLessonMaterial::class, 'lesson_id');
    }

    /**
     * Get media (LMS 2.0)
     */
    public function media(): HasMany
    {
        return $this->hasMany(EbdLessonMedia::class, 'lesson_id');
    }

    /**
     * Get questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(EBDLessonQuestion::class, 'lesson_id');
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'Agendada',
            self::STATUS_IN_PROGRESS => 'Em Andamento',
            self::STATUS_COMPLETED => 'Concluída',
            self::STATUS_CANCELLED => 'Cancelada',
            default => 'Agendada'
        };
    }

    /**
     * Get embed URL for video_url (YouTube or Vimeo), or null.
     */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }
        $url = trim($this->video_url);
        if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]+)#', $url, $m)) {
            return 'https://www.youtube.com/embed/'.$m[1];
        }
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $url, $m)) {
            return 'https://player.vimeo.com/video/'.$m[1];
        }
        return null;
    }

    /**
     * Get Bible reference formatted
     */
    public function getBibleReferenceAttribute(): string
    {
        if (! $this->bible_book || ! $this->bible_chapter) {
            return '';
        }

        $reference = $this->bible_book.' '.$this->bible_chapter;
        if ($this->bible_verses) {
            $reference .= ':'.$this->bible_verses;
        }

        return $reference;
    }

    /**
     * Scope for scheduled lessons
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    /**
     * Scope for completed lessons
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Check if lesson is completed by user
     */
    public function isCompletedByUser($userId): bool
    {
        return $this->hasOne(EbdStudentProgress::class, 'lesson_id')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }
}
