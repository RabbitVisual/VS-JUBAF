<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class EbdGamificationPoint extends Model
{
    protected $table = 'ebd_gamification_points';

    protected $fillable = [
        'user_id',
        'points',
        'source_type',
        'description',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
