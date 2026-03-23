<?php

namespace Modules\ChurchCouncil\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CouncilMember extends Model
{
    protected $fillable = [
        'user_id',
        'council_position',
        'council_role',
        'term_start',
        'term_end',
        'is_active',
        'responsibilities',
        'permissions',
    ];

    protected $casts = [
        'term_start' => 'date',
        'term_end' => 'date',
        'is_active' => 'boolean',
        'permissions' => 'array',
    ];

    // Council roles constants
    const ROLE_PRESIDENT = 'president';

    const ROLE_VICE_PRESIDENT = 'vice_president';

    const ROLE_SECRETARY = 'secretary';

    const ROLE_TREASURER = 'treasurer';

    const ROLE_MEMBER = 'member';

    const ROLE_lideranca = 'lideranca';

    const ROLE_DEACON = 'deacon';

    /**
     * Get the user associated with this council member
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the meetings where this member is the president
     */
    public function presidedMeetings(): HasMany
    {
        return $this->hasMany(CouncilMeeting::class, 'president_id');
    }

    /**
     * Get the agendas presented by this member
     */
    public function presentedAgendas(): HasMany
    {
        return $this->hasMany(CouncilAgenda::class, 'presented_by');
    }

    /**
     * Get the decisions made by this member
     */
    public function decidedAgendas(): HasMany
    {
        return $this->hasMany(CouncilAgenda::class, 'decided_by');
    }

    /**
     * Get the approvals made by this member
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(CouncilApproval::class, 'approved_by');
    }

    /**
     * Get the votes cast by this member
     */
    public function votes(): HasMany
    {
        return $this->hasMany(CouncilVote::class, 'council_member_id');
    }

    /**
     * Check if member has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    /**
     * Check if member is active
     */
    public function isActive(): bool
    {
        return $this->is_active &&
               ($this->term_end === null || $this->term_end->isFuture());
    }

    /**
     * Check if member has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->council_role === $role;
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return self::getRoleDisplayName($this->council_role);
    }

    /**
     * Display name for a role (static, for grouped queries).
     */
    public static function getRoleDisplayName(?string $role): string
    {
        return match ($role) {
            self::ROLE_PRESIDENT => 'Presidente',
            self::ROLE_VICE_PRESIDENT => 'Vice-Presidente',
            self::ROLE_SECRETARY => 'Secretário',
            self::ROLE_TREASURER => 'Tesoureiro',
            self::ROLE_lideranca => 'lideranca',
            self::ROLE_DEACON => 'Diácono',
            self::ROLE_MEMBER => 'Membro',
            default => 'Membro'
        };
    }

    /**
     * Scope for active members
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('term_end')
                    ->orWhere('term_end', '>', now());
            });
    }

    /**
     * Scope for specific role
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->where('council_role', $role);
    }
}
