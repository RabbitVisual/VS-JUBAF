<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Insight extends Model
{
    protected $table = 'insights_bank';

    protected $fillable = [
        'trigger_event',
        'content',
        'level',
        'is_active',
        'is_pro_only',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_pro_only' => 'boolean',
    ];

    public function scopeForTrigger(Builder $query, string $event): Builder
    {
        return $query->where('trigger_event', $event);
    }

    public function scopeByLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get a random active insight for the given trigger event.
     * When $proOnly is false, exclude is_pro_only insights. When true, include all.
     */
    public static function getRandomForTrigger(string $triggerEvent, ?bool $proOnly = null): ?self
    {
        $query = self::forTrigger($triggerEvent)->active();

        if ($proOnly === false) {
            $query->where('is_pro_only', false);
        }

        return $query->inRandomOrder()->first();
    }
}
