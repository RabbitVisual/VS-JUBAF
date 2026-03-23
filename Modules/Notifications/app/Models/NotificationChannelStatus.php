<?php

namespace Modules\Notifications\App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannelStatus extends Model
{
    protected $table = 'notification_channel_status';

    protected $fillable = [
        'channel',
        'provider',
        'last_failure_at',
        'failure_count',
        'open_until',
    ];

    protected $casts = [
        'last_failure_at' => 'datetime',
        'open_until' => 'datetime',
    ];

    public function isOpen(): bool
    {
        return $this->open_until && $this->open_until->isFuture();
    }
}
