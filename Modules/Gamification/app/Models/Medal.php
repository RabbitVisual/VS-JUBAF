<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medal extends Model
{
    protected $fillable = [
        'title',
        'description',
        'explanation',
        'tips',
        'incentive_message',
        'icon_name',
        'trigger_key',
        'color',
        'rarity',
        'difficulty',
        'is_pro_only',
        'is_active',
    ];

    public const DIFFICULTIES = ['easy', 'medium', 'hard', 'advanced'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pro_only' => 'boolean',
    ];

    public function userMedals(): HasMany
    {
        return $this->hasMany(UserMedal::class);
    }
}
