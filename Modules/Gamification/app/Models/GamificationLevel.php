<?php

namespace Modules\Gamification\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GamificationLevel extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'points_min',
        'points_max',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_min' => 'integer',
        'points_max' => 'integer',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('points_min');
    }

    /**
     * Retorna o nível cujo intervalo contém os pontos (maior points_min que ainda contém).
     */
    public static function getLevelByPoints($points)
    {
        return static::active()
            ->where('points_min', '<=', $points)
            ->where(function ($query) use ($points) {
                $query->whereNull('points_max')
                    ->orWhere('points_max', '>=', $points);
            })
            ->orderByDesc('points_min')
            ->first();
    }

    /**
     * Retorna o próximo nível na sequência (por order), ou null se for o último.
     */
    public static function getNextLevelAfter(self $currentLevel): ?self
    {
        return static::active()
            ->where('order', '>', $currentLevel->order)
            ->ordered()
            ->first();
    }
}
