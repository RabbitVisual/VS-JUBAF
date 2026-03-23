<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    protected $table = 'user_achievements';

    protected $fillable = [
        'user_id',
        'achievement_key',
        'triggered_at',
        'metadata',
    ];

    protected $casts = [
        'triggered_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user has already achieved this key within the given period.
     * If $since is null, checks if achieved ever.
     */
    public static function hasAchieved(User $user, string $key, ?Carbon $since = null): bool
    {
        $query = self::where('user_id', $user->id)->where('achievement_key', $key);

        if ($since !== null) {
            $query->where('triggered_at', '>=', $since);
        }

        return $query->exists();
    }
}
