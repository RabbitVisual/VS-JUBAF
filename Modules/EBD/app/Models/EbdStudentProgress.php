<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class EbdStudentProgress extends Model
{
    protected $table = 'ebd_student_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'status',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(EBDLesson::class, 'lesson_id');
    }
}
