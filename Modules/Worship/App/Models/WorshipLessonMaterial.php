<?php

namespace Modules\Worship\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorshipLessonMaterial extends Model
{
    protected $table = 'worship_lesson_materials';

    protected $fillable = [
        'lesson_id',
        'type',
        'label',
        'file_path',
        'order',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(AcademyLesson::class, 'lesson_id');
    }
}
