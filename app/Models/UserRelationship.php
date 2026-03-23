<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserRelationship extends Model
{
    public const TYPE_PAI = 'pai';

    public const TYPE_MAE = 'mae';

    public const TYPE_CONJUGE = 'conjuge';

    public const TYPE_FILHO = 'filho';

    public const TYPE_IRMAO = 'irmao';

    public const TYPE_OUTRO = 'outro';

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'related_user_id',
        'related_name',
        'relationship_type',
        'status',
        'invited_by',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'related_user_id' => 'integer',
        'invited_by' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'related_user_id');
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Label do tipo de parentesco para exibição.
     */
    public static function relationshipTypeLabels(): array
    {
        return [
            self::TYPE_PAI => 'Pai',
            self::TYPE_MAE => 'Mãe',
            self::TYPE_CONJUGE => 'Cônjuge',
            self::TYPE_FILHO => 'Filho(a)',
            self::TYPE_IRMAO => 'Irmão(ã)',
            self::TYPE_OUTRO => 'Outro',
        ];
    }

    public function getRelationshipTypeLabelAttribute(): string
    {
        return self::relationshipTypeLabels()[$this->relationship_type] ?? $this->relationship_type;
    }

    /**
     * Nome exibível do parente (membro ou related_name).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->related_user_id && $this->relatedUser) {
            return $this->relatedUser->name;
        }

        return $this->related_name ?? '—';
    }
}
