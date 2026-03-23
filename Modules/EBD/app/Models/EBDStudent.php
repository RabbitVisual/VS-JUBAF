<?php

namespace Modules\EBD\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBDStudent extends Model
{
    use SoftDeletes;

    protected $table = 'ebd_students';

    protected $fillable = [
        'user_id',
        'class_id',
        'enrollment_date',
        'graduation_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'enrollment_date' => 'date',
        'graduation_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user (student)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class
     */
    public function ebdClass(): BelongsTo
    {
        return $this->belongsTo(EBDClass::class, 'class_id');
    }

    /**
     * Get attendance records
     */
    public function attendance(): HasMany
    {
        return $this->hasMany(EBDAttendance::class, 'student_id');
    }

    /**
     * Get evaluations
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(EBDEvaluation::class, 'student_id');
    }

    /**
     * Scope for active students
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a user is an active student
     */
    public static function isStudent($userId): bool
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }
}
