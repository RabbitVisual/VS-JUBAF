<?php

namespace Modules\Ministries\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ministry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'leader_id',
        'co_leader_id',
        'is_active',
        'requires_approval',
        'max_members',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'max_members' => 'integer',
        'settings' => 'array',
    ];

    /**
     * Relacionamento com líder
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Relacionamento com co-líder
     */
    public function coLeader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'co_leader_id');
    }

    /**
     * Relacionamento com membros
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ministry_members')
            ->withPivot('role', 'status', 'joined_at', 'approved_at', 'approved_by', 'left_at', 'notes')
            ->withTimestamps();
    }

    /**
     * Relacionamento com planos de ação
     */
    public function plans(): HasMany
    {
        return $this->hasMany(MinistryPlan::class);
    }

    /**
     * Relacionamento com relatórios mensais
     */
    public function reports(): HasMany
    {
        return $this->hasMany(MinistryReport::class);
    }

    public function ebdClasses(): HasMany
    {
        return $this->hasMany(\Modules\EBD\App\Models\EBDClass::class, 'ministry_id');
    }

    public function worshipSetlists(): HasMany
    {
        return $this->hasMany(\Modules\Worship\App\Models\WorshipSetlist::class, 'ministry_id');
    }

    public function socialCampaigns(): HasMany
    {
        return $this->hasMany(\Modules\SocialAction\App\Models\SocialCampaign::class, 'ministry_id');
    }

    public function prayerRequests(): HasMany
    {
        return $this->hasMany(\Modules\Intercessor\App\Models\PrayerRequest::class, 'ministry_id');
    }

    /**
     * Relacionamento com membros ativos
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'active');
    }

    /**
     * Relacionamento com membros pendentes
     */
    public function pendingMembers(): BelongsToMany
    {
        return $this->members()->wherePivot('status', 'pending');
    }

    /**
     * Verifica se o usuário é membro
     */
    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Verifica se o usuário é líder
     */
    public function isLeader(User $user): bool
    {
        return $this->leader_id === $user->id ||
               $this->co_leader_id === $user->id ||
               $this->members()->where('user_id', $user->id)
                   ->wherePivot('role', 'leader')
                   ->exists();
    }

    /**
     * Conta membros ativos
     */
    public function getActiveMembersCountAttribute(): int
    {
        return $this->activeMembers()->count();
    }

    /**
     * Verifica se pode adicionar mais membros
     */
    public function canAddMembers(): bool
    {
        if ($this->max_members === null) {
            return true;
        }

        return $this->active_members_count < $this->max_members;
    }

    /**
     * Scope: apenas ministérios ativos
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
