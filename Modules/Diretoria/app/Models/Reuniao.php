<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reuniao extends Model
{
    protected $fillable = [
        'title',
        'description',
        'scheduled_date',
        'actual_start_time',
        'actual_end_time',
        'location',
        'status',
        'meeting_type',
        'created_by',
        'president_id',
        'minutes',
        'participants',
        'meeting_link',
        'quorum_present',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'actual_start_time' => 'datetime',
        'actual_end_time' => 'datetime',
        'participants' => 'array',
        'quorum_present' => 'integer',
    ];

    // Status constants
    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_COMPLETED = 'completed';

    const STATUS_CANCELLED = 'cancelled';

    // Meeting type constants
    const TYPE_ORDINARY = 'ordinary';

    const TYPE_EXTRAORDINARY = 'extraordinary';

    const TYPE_EMERGENCY = 'emergency';

    /**
     * Get the creator of this meeting
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the president of this meeting
     */
    public function president(): BelongsTo
    {
        return $this->belongsTo(DiretoriaMember::class, 'president_id');
    }

    /**
     * Get the agendas for this meeting
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Pauta::class, 'meeting_id')->orderBy('order');
    }

    public function minutesVersions(): HasMany
    {
        return $this->hasMany(MeetingMinutesVersion::class, 'diretoria_meeting_id')->orderByDesc('version');
    }

    /**
     * Check if meeting is in progress
     */
    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    /**
     * Check if meeting is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if meeting is scheduled
     */
    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    /**
     * Check if meeting is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Start the meeting
     */
    public function start(): bool
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'actual_start_time' => now(),
        ]);

        return true;
    }

    /**
     * End the meeting
     */
    public function end(): bool
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'actual_end_time' => now(),
        ]);

        return true;
    }

    /**
     * Cancel the meeting
     */
    public function cancel(): bool
    {
        if (in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED])) {
            return false;
        }

        $this->update(['status' => self::STATUS_CANCELLED]);

        return true;
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return self::getStatusDisplayName($this->status);
    }

    /**
     * Display name for a status (static, for grouped queries).
     */
    public static function getStatusDisplayName(?string $status): string
    {
        return match ($status) {
            self::STATUS_SCHEDULED => 'Agendada',
            self::STATUS_IN_PROGRESS => 'Em Andamento',
            self::STATUS_COMPLETED => 'Concluída',
            self::STATUS_CANCELLED => 'Cancelada',
            default => 'Desconhecido'
        };
    }

    /**
     * Get meeting type display name
     */
    public function getMeetingTypeDisplayAttribute(): string
    {
        return self::getMeetingTypeDisplayName($this->meeting_type);
    }

    /**
     * Display name for meeting type (static).
     */
    public static function getMeetingTypeDisplayName(?string $type): string
    {
        return match ($type) {
            self::TYPE_ORDINARY => 'Ordinária',
            self::TYPE_EXTRAORDINARY => 'Extraordinária',
            self::TYPE_EMERGENCY => 'Emergencial',
            default => 'Ordinária'
        };
    }

    /**
     * Get duration of the meeting in minutes
     */
    public function getDurationAttribute(): ?int
    {
        if (! $this->actual_start_time || ! $this->actual_end_time) {
            return null;
        }

        return $this->actual_start_time->diffInMinutes($this->actual_end_time);
    }

    /**
     * Scope for upcoming meetings
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>', now())
            ->where('status', '!=', self::STATUS_CANCELLED);
    }

    /**
     * Scope for completed meetings
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope for meetings by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('meeting_type', $type);
    }

    /**
     * Get the participant members as a collection of models
     */
    public function getParticipantMembersAttribute()
    {
        if (empty($this->participants)) {
            return collect([]);
        }

        return DiretoriaMember::with('user')->whereIn('id', $this->participants)->get();
    }

    /**
     * Snapshot current minutes content into a version record.
     */
    public function snapshotMinutesVersion(User $actor, string $content, string $state = 'draft'): MeetingMinutesVersion
    {
        $nextVersion = ($this->minutesVersions()->max('version') ?? 0) + 1;

        return $this->minutesVersions()->create([
            'version' => $nextVersion,
            'content' => $content,
            'state' => $state,
            'created_by' => $actor->id,
        ]);
    }
}
