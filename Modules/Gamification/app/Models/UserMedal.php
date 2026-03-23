<?php

declare(strict_types=1);

namespace Modules\Gamification\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMedal extends Model
{
    protected $fillable = [
        'user_id',
        'medal_id',
        'unlocked_at',
    ];

    protected $casts = [
        'unlocked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function medal(): BelongsTo
    {
        return $this->belongsTo(Medal::class);
    }

    public static function hasUnlocked(User $user, int $medalId): bool
    {
        return self::where('user_id', $user->id)->where('medal_id', $medalId)->exists();
    }
}
