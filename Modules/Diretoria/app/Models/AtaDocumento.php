<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AtaDocumento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'document_type',
        'document_date',
        'uploaded_by',
        'meeting_id',
        'is_public',
        'is_active',
    ];

    protected $casts = [
        'document_date' => 'date',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'file_size' => 'integer',
    ];

    // Document types
    const TYPE_STATUTE = 'statute';

    const TYPE_REGIMENT = 'regiment';

    const TYPE_MINUTE = 'minute';

    const TYPE_RESOLUTION = 'resolution';

    const TYPE_DECLARACAO_DOUTRINARIA = 'declaracao_doutrinaria';

    const TYPE_PACTO_IGREJAS = 'pacto_igrejas';

    const TYPE_REGIMENTO_INTERNO = 'regimento_interno';

    const TYPE_OTHER = 'other';

    /**
     * Document type display labels (CBB-aligned).
     */
    public static function getDocumentTypeLabel(?string $type): string
    {
        return match ($type) {
            self::TYPE_STATUTE => __('Diretoria::messages.doc_type_statute'),
            self::TYPE_REGIMENT => __('Diretoria::messages.doc_type_regiment'),
            self::TYPE_MINUTE => __('Diretoria::messages.doc_type_minute'),
            self::TYPE_RESOLUTION => __('Diretoria::messages.doc_type_resolution'),
            self::TYPE_DECLARACAO_DOUTRINARIA => __('Diretoria::messages.doc_type_declaracao_doutrinaria'),
            self::TYPE_PACTO_IGREJAS => __('Diretoria::messages.doc_type_pacto_igrejas'),
            self::TYPE_REGIMENTO_INTERNO => __('Diretoria::messages.doc_type_regimento_interno'),
            default => __('Diretoria::messages.doc_type_other'),
        };
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Reuniao::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
