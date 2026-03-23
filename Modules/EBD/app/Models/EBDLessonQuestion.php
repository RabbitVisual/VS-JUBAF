<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBDLessonQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'ebd_lesson_questions';

    protected $fillable = [
        'lesson_id',
        'question',
        'type',
        'options',
        'correct_answer',
        'points',
        'order',
        'is_required',
    ];

    protected $casts = [
        'options' => 'array',
        'points' => 'integer',
        'order' => 'integer',
        'is_required' => 'boolean',
    ];

    // Type constants
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';

    const TYPE_TRUE_FALSE = 'true_false';

    const TYPE_SHORT_ANSWER = 'short_answer';

    const TYPE_ESSAY = 'essay';

    /**
     * Get the lesson
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(EBDLesson::class, 'lesson_id');
    }

    /**
     * Get type display name
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_MULTIPLE_CHOICE => 'Múltipla Escolha',
            self::TYPE_TRUE_FALSE => 'Verdadeiro/Falso',
            self::TYPE_SHORT_ANSWER => 'Resposta Curta',
            self::TYPE_ESSAY => 'Dissertativa',
            default => 'Resposta Curta'
        };
    }
}
