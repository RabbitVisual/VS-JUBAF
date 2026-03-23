<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameQuestion extends Model
{
    protected $table = 'ebd_game_questions';

    protected $fillable = [
        'game_id',
        'question_text',
        'media_url',
        'difficulty',
        'points',
        'time_limit',
        'category',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(GameAnswer::class, 'question_id');
    }
}
