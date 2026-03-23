<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;

class EbdGamificationLevel extends Model
{
    protected $table = 'ebd_gamification_levels';

    protected $fillable = [
        'level_number',
        'name',
        'xp_required',
        'icon_path',
        'tier',
    ];

    protected $casts = [
        'level_number' => 'integer',
        'xp_required' => 'integer',
    ];
}
