<?php

namespace Modules\Diretoria\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    protected $fillable = [
        'agenda_id',
        'diretoria_member_id',
        'vote',
        'comments',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    // Vote constants
    const VOTE_YES = 'yes';

    const VOTE_NO = 'no';

    const VOTE_ABSTAIN = 'abstain';

    const VOTE_ABSENT = 'absent';

    /**
     * Get the agenda this vote belongs to
     */
    public function agenda(): BelongsTo
    {
        return $this->belongsTo(Pauta::class, 'agenda_id');
    }

    /**
     * Get the diretoria member who cast this vote
     */
    public function DiretoriaMember(): BelongsTo
    {
        return $this->belongsTo(DiretoriaMember::class, 'diretoria_member_id');
    }

    /**
     * Check if vote is yes
     */
    public function isYes(): bool
    {
        return $this->vote === self::VOTE_YES;
    }

    /**
     * Check if vote is no
     */
    public function isNo(): bool
    {
        return $this->vote === self::VOTE_NO;
    }

    /**
     * Check if vote is abstain
     */
    public function isAbstain(): bool
    {
        return $this->vote === self::VOTE_ABSTAIN;
    }

    /**
     * Check if member was absent
     */
    public function isAbsent(): bool
    {
        return $this->vote === self::VOTE_ABSENT;
    }

    /**
     * Get vote display name
     */
    public function getVoteDisplayAttribute(): string
    {
        return match ($this->vote) {
            self::VOTE_YES => 'Sim',
            self::VOTE_NO => 'Não',
            self::VOTE_ABSTAIN => 'Abstenção',
            self::VOTE_ABSENT => 'Ausente',
            default => 'Não votou'
        };
    }

    /**
     * Scope for specific vote type
     */
    public function scopeWithVote($query, string $vote)
    {
        return $query->where('vote', $vote);
    }

    /**
     * Scope for votes by agenda
     */
    public function scopeForAgenda($query, int $agendaId)
    {
        return $query->where('agenda_id', $agendaId);
    }

    /**
     * Scope for votes by diretoria member
     */
    public function scopeByMember($query, int $memberId)
    {
        return $query->where('diretoria_member_id', $memberId);
    }
}
