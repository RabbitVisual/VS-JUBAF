<?php

namespace Modules\Ministries\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinistryMember extends Model
{
    protected $table = 'ministry_members';

    protected $fillable = [
        'ministry_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'approved_at',
        'approved_by',
        'left_at',
        'notes',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'approved_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    /**
     * Relacionamento com ministério
     */
    public function ministry(): BelongsTo
    {
        return $this->belongsTo(Ministry::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com aprovador
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope: membros ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
