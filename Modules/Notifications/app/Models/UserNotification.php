<?php

namespace Modules\Notifications\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'uuid',
        'user_id',
        'notification_id',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com notificação
     */
    public function notification(): BelongsTo
    {
        return $this->belongsTo(SystemNotification::class, 'notification_id');
    }

    /**
     * Marca como lida
     */
    public function markAsRead(): void
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    protected static function booted(): void
    {
        static::creating(function (UserNotification $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}
