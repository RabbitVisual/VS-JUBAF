<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EbdQuizSession extends Model
{
    use HasUuids;

    protected $table = 'ebd_quiz_sessions';

    protected $fillable = [
        'class_id',
        'status',
        'current_question_index',
        'show_answer',
    ];

    protected $casts = [
        'current_question_index' => 'integer',
        'show_answer' => 'boolean',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(EBDClass::class, 'class_id');
    }
}
