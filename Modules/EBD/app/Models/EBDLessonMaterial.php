<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EBDLessonMaterial extends Model
{
    use SoftDeletes;

    protected $table = 'ebd_lesson_materials';

    protected $fillable = [
        'lesson_id',
        'title',
        'type',
        'file_path',
        'url',
        'description',
        'order',
        'is_required',
        'is_public',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_public' => 'boolean',
        'order' => 'integer',
    ];

    // Type constants
    const TYPE_PDF = 'pdf';

    const TYPE_IMAGE = 'image';

    const TYPE_VIDEO = 'video';

    const TYPE_LINK = 'link';

    const TYPE_DOCUMENT = 'document';

    const TYPE_PRESENTATION = 'presentation';

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
            self::TYPE_PDF => 'PDF',
            self::TYPE_IMAGE => 'Imagem',
            self::TYPE_VIDEO => 'Vídeo',
            self::TYPE_LINK => 'Link',
            self::TYPE_DOCUMENT => 'Documento',
            self::TYPE_PRESENTATION => 'Apresentação',
            default => 'Documento'
        };
    }
}
