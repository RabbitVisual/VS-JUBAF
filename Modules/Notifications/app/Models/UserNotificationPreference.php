<?php

namespace Modules\Notifications\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserNotificationPreference extends Model
{
    protected $table = 'user_notification_preferences';

    protected $fillable = [
        'uuid',
        'user_id',
        'notification_type',
        'channels',
        'dnd_from',
        'dnd_to',
    ];

    protected $casts = [
        'channels' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if we are currently in DND window (no intrusive channels).
     */
    public function isInDndWindow(): bool
    {
        if (empty($this->dnd_from) || empty($this->dnd_to)) {
            return false;
        }
        $now = now()->format('H:i:s');
        $from = is_string($this->dnd_from) ? $this->dnd_from : \Carbon\Carbon::parse($this->dnd_from)->format('H:i:s');
        $to = is_string($this->dnd_to) ? $this->dnd_to : \Carbon\Carbon::parse($this->dnd_to)->format('H:i:s');
        if ($from <= $to) {
            return $now >= $from && $now <= $to;
        }
        // overnight window (e.g. 22:00 to 07:00)
        return $now >= $from || $now <= $to;
    }

    protected static function booted(): void
    {
        static::creating(function (UserNotificationPreference $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
