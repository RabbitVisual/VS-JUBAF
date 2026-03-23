<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EbdLessonMedia extends Model
{
    protected $table = 'ebd_lesson_media';

    protected $fillable = [
        'lesson_id',
        'type',
        'path',
        'title',
        'duration_seconds',
    ];

    protected $casts = [
        'duration_seconds' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(EBDLesson::class, 'lesson_id');
    }
}
