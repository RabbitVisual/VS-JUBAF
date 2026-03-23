<?php

namespace Modules\EBD\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBDTeacher extends Model
{
    use SoftDeletes;

    protected $table = 'ebd_teachers';

    protected $fillable = [
        'user_id',
        'class_id',
        'role',
        'start_date',
        'end_date',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Role constants
    const ROLE_TEACHER = 'teacher';

    const ROLE_ASSISTANT = 'assistant';

    const ROLE_SUBSTITUTE = 'substitute';

    /**
     * Get the user (teacher)
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
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_TEACHER => 'Professor',
            self::ROLE_ASSISTANT => 'Auxiliar',
            self::ROLE_SUBSTITUTE => 'Substituto',
            default => 'Professor'
        };
    }

    /**
     * Scope for active teachers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if a user is an active teacher
     */
    public static function isTeacher($userId): bool
    {
        return self::where('user_id', $userId)
            ->where('is_active', true)
            ->exists();
    }
}
