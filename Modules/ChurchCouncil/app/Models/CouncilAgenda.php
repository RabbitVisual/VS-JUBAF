<?php

namespace Modules\ChurchCouncil\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CouncilAgenda extends Model
{
    protected $fillable = [
        'meeting_id',
        'title',
        'description',
        'order',
        'status',
        'requires_assembly_vote',
        'assembly_decision',
        'assembly_decided_at',
        'assembly_votes_for',
        'assembly_votes_against',
        'assembly_votes_abstain',
        'priority',
        'presented_by',
        'discussion_notes',
        'decision',
        'decided_by',
        'discussed_at',
    ];

    protected $casts = [
        'discussed_at' => 'datetime',
        'requires_assembly_vote' => 'boolean',
        'assembly_decided_at' => 'datetime',
        'assembly_votes_for' => 'integer',
        'assembly_votes_against' => 'integer',
        'assembly_votes_abstain' => 'integer',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_DISCUSSED = 'discussed';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_POSTPONED = 'postponed';

    // Priority constants
    const PRIORITY_LOW = 'low';

    const PRIORITY_NORMAL = 'normal';

    const PRIORITY_HIGH = 'high';

    const PRIORITY_URGENT = 'urgent';

    /**
     * Get the meeting this agenda belongs to
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(CouncilMeeting::class, 'meeting_id');
    }

    /**
     * Get the member who presented this agenda
     */
    public function presenter(): BelongsTo
    {
        return $this->belongsTo(CouncilMember::class, 'presented_by');
    }

    /**
     * Get the member who made the decision
     */
    public function decisionMaker(): BelongsTo
    {
        return $this->belongsTo(CouncilMember::class, 'decided_by');
    }

    /**
     * Get the votes for this agenda
     */
    public function votes(): HasMany
    {
        return $this->hasMany(CouncilVote::class, 'agenda_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(CouncilAgendaVersion::class, 'council_agenda_id')->orderByDesc('version');
    }

    /**
     * Check if agenda is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if agenda is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if agenda is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Approve the agenda
     */
    public function approve(CouncilMember $approvedBy, ?string $decision = null): bool
    {
        if ($this->status !== self::STATUS_DISCUSSED) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'decided_by' => $approvedBy->id,
            'decision' => $decision,
            'discussed_at' => now(),
        ]);

        return true;
    }

    /**
     * Reject the agenda
     */
    public function reject(CouncilMember $rejectedBy, ?string $reason = null): bool
    {
        if ($this->status !== self::STATUS_DISCUSSED) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'decided_by' => $rejectedBy->id,
            'decision' => $reason,
            'discussed_at' => now(),
        ]);

        return true;
    }

    /**
     * Postpone the agenda
     */
    public function postpone(): bool
    {
        if (! in_array($this->status, [self::STATUS_PENDING, self::STATUS_DISCUSSED])) {
            return false;
        }

        $this->update(['status' => self::STATUS_POSTPONED]);

        return true;
    }

    /**
     * Mark as discussed
     */
    public function markAsDiscussed(): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_DISCUSSED,
            'discussed_at' => now(),
        ]);

        return true;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_DISCUSSED => 'Em Discussão',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_POSTPONED => 'Adiado',
            default => 'Pendente'
        };
    }

    /**
     * Get priority display name
     */
    public function getPriorityDisplayAttribute(): string
    {
        return match ($this->priority) {
            self::PRIORITY_LOW => 'Baixa',
            self::PRIORITY_NORMAL => 'Normal',
            self::PRIORITY_HIGH => 'Alta',
            self::PRIORITY_URGENT => 'Urgente',
            default => 'Normal'
        };
    }

    /**
     * Get vote summary
     */
    public function getVoteSummaryAttribute(): array
    {
        $votes = $this->votes;

        return [
            'total' => $votes->count(),
            'yes' => $votes->where('vote', CouncilVote::VOTE_YES)->count(),
            'no' => $votes->where('vote', CouncilVote::VOTE_NO)->count(),
            'abstain' => $votes->where('vote', CouncilVote::VOTE_ABSTAIN)->count(),
            'absent' => $votes->where('vote', CouncilVote::VOTE_ABSENT)->count(),
        ];
    }

    /**
     * Scope for pending agendas
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved agendas
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for high priority agendas
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', [self::PRIORITY_HIGH, self::PRIORITY_URGENT]);
    }

    /**
     * Snapshot current agenda state into a version record.
     */
    public function snapshotVersion(User $actor): CouncilAgendaVersion
    {
        $nextVersion = ($this->versions()->max('version') ?? 0) + 1;

        $payload = [
            'meeting_id' => $this->meeting_id,
            'title' => $this->title,
            'description' => $this->description,
            'order' => $this->order,
            'status' => $this->status,
            'requires_assembly_vote' => (bool) $this->requires_assembly_vote,
            'assembly_decision' => $this->assembly_decision,
            'assembly_decided_at' => $this->assembly_decided_at?->toIso8601String(),
            'assembly_votes_for' => $this->assembly_votes_for,
            'assembly_votes_against' => $this->assembly_votes_against,
            'assembly_votes_abstain' => $this->assembly_votes_abstain,
            'priority' => $this->priority,
            'presented_by' => $this->presented_by,
            'discussion_notes' => $this->discussion_notes,
            'decision' => $this->decision,
            'decided_by' => $this->decided_by,
            'discussed_at' => $this->discussed_at?->toIso8601String(),
        ];

        return $this->versions()->create([
            'version' => $nextVersion,
            'payload' => $payload,
            'created_by' => $actor->id,
        ]);
    }
}

