<?php

namespace Modules\Sermons\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SermonCollaborator extends Model
{
    protected $fillable = [
        'sermon_id',
        'user_id',
        'role',
        'can_edit',
        'can_delete',
        'can_invite',
        'status',
        'invited_at',
        'accepted_at',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_invite' => 'boolean',
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_ACCEPTED = 'accepted';

    const STATUS_REJECTED = 'rejected';

    // Role constants
    const ROLE_VIEWER = 'viewer';

    const ROLE_EDITOR = 'editor';

    const ROLE_CO_AUTHOR = 'co_author';

    /**
     * Get the sermon
     */
    public function sermon(): BelongsTo
    {
        return $this->belongsTo(Sermon::class, 'sermon_id');
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accept the invitation
     */
    public function accept(): void
    {
        $this->update([
            'status' => self::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);
    }

    /**
     * Reject the invitation
     */
    public function reject(): void
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
        ]);
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match ($this->role) {
            self::ROLE_VIEWER => 'Visualizador',
            self::ROLE_EDITOR => 'Editor',
            self::ROLE_CO_AUTHOR => 'Co-autor',
            default => 'Visualizador'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_ACCEPTED => 'Aceito',
            self::STATUS_REJECTED => 'Rejeitado',
            default => 'Pendente'
        };
    }
}
