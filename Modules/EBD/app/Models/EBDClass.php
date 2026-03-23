<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBDClass extends Model
{
    use SoftDeletes;

    protected $table = 'ebd_classes';

    protected $fillable = [
        'name',
        'age_group',
        'description',
        'room',
        'schedule_time',
        'max_students',
        'is_active',
        'order',
        'ministry_id',
        'course_id',
    ];

    protected $casts = [
        'schedule_time' => 'string', // Time format (H:i)
        'is_active' => 'boolean',
        'max_students' => 'integer',
        'order' => 'integer',
    ];

    // Age group constants
    const AGE_ADULT = 'adult';

    const AGE_YOUTH = 'youth';

    const AGE_TEEN = 'teen';

    const AGE_CHILDREN = 'children';

    public function ministry(): BelongsTo
    {
        return $this->belongsTo(\Modules\Ministries\App\Models\Ministry::class, 'ministry_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(EBDCourse::class, 'course_id');
    }

    /**
     * Get teachers for this class
     */
    public function teachers(): HasMany
    {
        return $this->hasMany(EBDTeacher::class, 'class_id');
    }

    /**
     * Get active teachers
     */
    public function activeTeachers(): HasMany
    {
        return $this->teachers()->where('is_active', true);
    }

    /**
     * Get students for this class
     */
    public function students(): HasMany
    {
        return $this->hasMany(EBDStudent::class, 'class_id');
    }

    /**
     * Get active students
     */
    public function activeStudents(): HasMany
    {
        return $this->students()->where('is_active', true);
    }

    /**
     * Get lessons for this class
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(EBDLesson::class, 'class_id');
    }

    /**
     * Get age group display name
     */
    public function getAgeGroupDisplayAttribute(): string
    {
        return match ($this->age_group) {
            self::AGE_ADULT => 'Adultos',
            self::AGE_YOUTH => 'Jovens',
            self::AGE_TEEN => 'Adolescentes',
            self::AGE_CHILDREN => 'Crianças',
            default => 'Outro'
        };
    }

    /**
     * Scope for active classes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for age group
     */
    public function scopeAgeGroup($query, string $ageGroup)
    {
        return $query->where('age_group', $ageGroup);
    }
}
