<?php

namespace Modules\Notifications\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class NotificationFailedDelivery extends Model
{
    protected $table = 'notification_failed_deliveries';

    protected $fillable = [
        'uuid',
        'user_id',
        'notification_id',
        'channel',
        'provider',
        'error_message',
        'payload',
        'attempts',
        'last_attempt_at',
        'retry_pending',
    ];

    protected $casts = [
        'payload' => 'array',
        'last_attempt_at' => 'datetime',
        'retry_pending' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(SystemNotification::class, 'notification_id');
    }

    protected static function booted(): void
    {
        static::creating(function (NotificationFailedDelivery $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
