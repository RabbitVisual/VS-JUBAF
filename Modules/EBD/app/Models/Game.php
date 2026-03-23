<?php

namespace Modules\EBD\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\EBD\Database\Factories\GameFactory;

class Game extends Model
{
    protected $table = 'ebd_games';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(GameQuestion::class, 'game_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(UserGameSession::class, 'game_id');
    }
}
