<?php

namespace Modules\Notifications\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class SystemNotification extends Model
{
    protected $table = 'system_notifications';

    protected $fillable = [
        'uuid',
        'title',
        'message',
        'type',
        'priority',
        'target_users',
        'target_roles',
        'target_ministries',
        'action_url',
        'action_text',
        'scheduled_at',
        'expires_at',
        'is_read',
        'created_by',
        'notification_type',
        'group_count',
    ];

    protected $casts = [
        'target_users' => 'array',
        'target_roles' => 'array',
        'target_ministries' => 'array',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Relacionamento com criador
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relacionamento com usuários que receberam a notificação
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_notifications', 'notification_id', 'user_id')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }

    /**
     * Notificação global (para todos), sem destino específico.
     * Só deve aparecer na homepage pública quando for global.
     */
    public function isGlobal(): bool
    {
        return empty($this->target_users) && empty($this->target_roles) && empty($this->target_ministries);
    }

    /**
     * Verifica se a notificação está ativa
     */
    public function isActive(): bool
    {
        $now = now();

        if ($this->scheduled_at && $this->scheduled_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Verifica se o usuário deve receber a notificação
     */
    public function shouldNotifyUser(User $user): bool
    {
        // Verifica se já recebeu
        if (UserNotification::where('user_id', $user->id)->where('notification_id', $this->id)->exists()) {
            return false;
        }

        // Verifica se está ativa
        if (! $this->isActive()) {
            return false;
        }

        // Se não tem targets específicos, notifica todos
        if (empty($this->target_users) && empty($this->target_roles) && empty($this->target_ministries)) {
            return true;
        }

        // Verifica por usuário específico
        if (! empty($this->target_users) && in_array($user->id, $this->target_users)) {
            return true;
        }

        // Verifica por role
        if (! empty($this->target_roles) && $user->role && in_array($user->role->slug, $this->target_roles)) {
            return true;
        }

        // Verifica por ministério
        if (! empty($this->target_ministries)) {
            $userMinistries = $user->ministries()->pluck('ministries.id')->toArray();
            if (! empty(array_intersect($this->target_ministries, $userMinistries))) {
                return true;
            }
        }

        return false;
    }

    protected static function booted(): void
    {
        static::creating(function (SystemNotification $model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Scope para notificações ativas
     */
    public function scopeActive($query)
    {
        $now = now();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('scheduled_at')
                ->orWhere('scheduled_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>=', $now);
        });
    }
}
