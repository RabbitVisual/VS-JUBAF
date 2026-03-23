<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class EbdGameScore extends Model
{
    protected $table = 'ebd_game_scores';

    protected $fillable = [
        'user_id',
        'game_id',
        'score',
        'metadata',
    ];

    protected $casts = [
        'score' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(EbdGame::class);
    }
}
