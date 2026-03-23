<?php

namespace Modules\Diretoria\App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class diretoriaApproval extends Model
{
    protected $fillable = [
        'approvable_type',
        'approvable_id',
        'approval_type',
        'status',
        'request_details',
        'approval_notes',
        'rejection_reason',
        'requested_by',
        'approved_by',
        'submitted_at',
        'reviewed_at',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'expires_at' => 'date',
        'metadata' => 'array',
    ];

    // Approval type constants
    const TYPE_ACCOUNT_ACTIVATION = 'account_activation';

    const TYPE_MINISTRY_MEMBERSHIP = 'ministry_membership';

    const TYPE_EVENT_CREATION = 'event_creation';

    const TYPE_FINANCIAL_REQUEST = 'financial_request';

    const TYPE_DOCUMENT_APPROVAL = 'document_approval';

    const TYPE_POLICY_CHANGE = 'policy_change';

    const TYPE_MEMBERSHIP_TRANSFER_OUT = 'membership_transfer_out';

    const TYPE_MINISTRY_PLAN = 'ministry_plan';

    const TYPE_EBD_CURRICULUM = 'ebd_curriculum';

    const TYPE_OTHER = 'other';

    // Status constants
    const STATUS_PENDING = 'pending';

    const STATUS_APPROVED = 'approved';

    const STATUS_REJECTED = 'rejected';

    const STATUS_REQUIRES_REVISION = 'requires_revision';

    /**
     * Get the approvable model (polymorphic relationship)
     */
    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who requested this approval
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the diretoria member who approved/rejected this request
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(diretoriaMember::class, 'approved_by');
    }

    /**
     * Check if approval is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if approval is approved
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if approval is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Check if approval requires revision
     */
    public function requiresRevision(): bool
    {
        return $this->status === self::STATUS_REQUIRES_REVISION;
    }

    /**
     * Check if approval is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Approve the request
     */
    public function approve(diretoriaMember $approvedBy, ?string $notes = null): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approvedBy->id,
            'approval_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        // Execute approval action based on type
        $this->executeApproval();

        return true;
    }

    /**
     * Reject the request
     */
    public function reject(diretoriaMember $rejectedBy, string $reason): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $rejectedBy->id,
            'rejection_reason' => $reason,
            'reviewed_at' => now(),
        ]);

        return true;
    }

    /**
     * Request revision
     */
    public function requestRevision(diretoriaMember $reviewedBy, string $notes): bool
    {
        if ($this->status !== self::STATUS_PENDING) {
            return false;
        }

        $this->update([
            'status' => self::STATUS_REQUIRES_REVISION,
            'approved_by' => $reviewedBy->id,
            'approval_notes' => $notes,
            'reviewed_at' => now(),
        ]);

        return true;
    }

    /**
     * Public entry point to run the approval action (e.g. when approved by admin fallback).
     */
    public function runApprovalAction(): void
    {
        if ($this->status === self::STATUS_APPROVED) {
            $this->executeApproval();
        }
    }

    /**
     * Execute the approval action based on type.
     * Only runs when approvable is set (polymorphic relation).
     */
    private function executeApproval(): void
    {
        if (! $this->approvable_type || ! $this->approvable_id) {
            return;
        }

        $approvable = $this->approvable;
        if (! $approvable) {
            return;
        }

        switch ($this->approval_type) {
            case self::TYPE_ACCOUNT_ACTIVATION:
                if ($approvable instanceof \App\Models\User) {
                    $approvable->update(['email_verified_at' => now()]);
                }
                break;

            case self::TYPE_MINISTRY_MEMBERSHIP:
                if (class_exists(\Modules\Ministries\App\Models\Ministry::class)) {
                    $this->executeMinistryMembershipApproval($approvable);
                }
                break;

            case self::TYPE_EVENT_CREATION:
                if ($approvable instanceof \Modules\Events\App\Models\Event) {
                    $approvable->update(['status' => \Modules\Events\App\Models\Event::STATUS_PUBLISHED]);
                }
                break;

            case self::TYPE_FINANCIAL_REQUEST:
                if (class_exists(\Modules\Treasury\App\Models\FinancialEntry::class) && $approvable instanceof \Modules\Treasury\App\Models\FinancialEntry) {
                    $this->executeFinancialRequestApproval($approvable);
                }
                break;

            case self::TYPE_MINISTRY_PLAN:
                if (class_exists(\Modules\Ministries\App\Models\MinistryPlan::class) && $approvable instanceof \Modules\Ministries\App\Models\MinistryPlan) {
                    $approverUserId = $this->approver?->user_id ?? $this->metadata['approved_by_user_id'] ?? auth()->id();
                    $approvable->update([
                        'status' => \Modules\Ministries\App\Models\MinistryPlan::STATUS_IN_EXECUTION,
                        'approved_at' => now(),
                        'approved_by' => $approverUserId,
                    ]);
                    if (class_exists(\Modules\Diretoria\App\Services\diretoriaAuditService::class)) {
                        app(\Modules\Diretoria\App\Services\diretoriaAuditService::class)->log('ministry_plan_approved', $approvable, [
                            'plan_id' => $approvable->id,
                            'ministry_id' => $approvable->ministry_id,
                            'approved_by' => $approverUserId,
                        ]);
                    }
                }
                break;

            case self::TYPE_MEMBERSHIP_TRANSFER_OUT:
                if ($approvable instanceof \Modules\Diretoria\App\Models\TransferLetter) {
                    $approvable->update([
                        'status' => \Modules\Diretoria\App\Models\TransferLetter::STATUS_SENT,
                        'issued_at' => now(),
                    ]);

                    if (class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
                        try {
                            $member = $approvable->member;
                            if ($member) {
                                app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToUser(
                                    $member,
                                    'Carta de transferência emitida',
                                    'Sua carta de transferência foi emitida pela igreja. Procure a secretaria para receber o documento físico ou arquivo digital.',
                                    [
                                        'type' => 'success',
                                        'priority' => 'normal',
                                    ]
                                );
                            }
                        } catch (\Throwable $e) {
                            \Log::warning('Failed to send transfer letter notification: '.$e->getMessage());
                        }
                    }
                }
                break;

            default:
                break;
        }
    }

    /**
     * Approve ministry membership (pivot ministry_members: pending -> active).
     * approved_by on pivot is user_id; we have diretoria_member id in $this->approved_by.
     */
    private function executeMinistryMembershipApproval(mixed $approvable): void
    {
        $metadata = $this->metadata ?? [];
        $userId = $metadata['user_id'] ?? null;
        $ministryId = $metadata['ministry_id'] ?? ($approvable->id ?? null);
        $approverUserId = $this->approver?->user_id ?? auth()->id();
        if ($userId && $ministryId && class_exists(\Modules\Ministries\App\Models\Ministry::class)) {
            $pivot = \DB::table('ministry_members')
                ->where('user_id', $userId)
                ->where('ministry_id', $ministryId)
                ->where('status', 'pending')
                ->first();
            if ($pivot) {
                \DB::table('ministry_members')
                    ->where('user_id', $userId)
                    ->where('ministry_id', $ministryId)
                    ->update([
                        'status' => 'active',
                        'approved_at' => now(),
                        'approved_by' => $approverUserId,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    /**
     * Mark financial entry as diretoria-approved (status or flag). CBAV2026: set expense_status = approved.
     */
    private function executeFinancialRequestApproval(\Modules\Treasury\App\Models\FinancialEntry $entry): void
    {
        $updates = [];
        if (\Schema::hasColumn($entry->getTable(), 'diretoria_approval_id')) {
            $updates['diretoria_approval_id'] = $this->id;
        }
        if (\Schema::hasColumn($entry->getTable(), 'diretoria_approved_at')) {
            $updates['diretoria_approved_at'] = now();
        }
        if (\Schema::hasColumn($entry->getTable(), 'expense_status')) {
            $updates['expense_status'] = \Modules\Treasury\App\Models\FinancialEntry::EXPENSE_STATUS_APPROVED;
        }
        if ($updates !== []) {
            $entry->update($updates);
        }
    }

    /**
     * Get approval type display name
     */
    public function getApprovalTypeDisplayAttribute(): string
    {
        return match ($this->approval_type) {
            self::TYPE_ACCOUNT_ACTIVATION => 'Ativação de Conta',
            self::TYPE_MINISTRY_MEMBERSHIP => 'Filiação a Ministério',
            self::TYPE_EVENT_CREATION => 'Criação de Evento',
            self::TYPE_FINANCIAL_REQUEST => 'Solicitação Financeira',
            self::TYPE_DOCUMENT_APPROVAL => 'Aprovação de Documento',
            self::TYPE_POLICY_CHANGE => 'Mudança de Política',
            self::TYPE_MEMBERSHIP_TRANSFER_OUT => 'Carta de Transferência (Saída)',
            self::TYPE_MINISTRY_PLAN => 'Plano de Ministério',
            self::TYPE_EBD_CURRICULUM => 'Currículo EBD',
            self::TYPE_OTHER => 'Outro',
            default => 'Outro'
        };
    }

    /**
     * Get status display name
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendente',
            self::STATUS_APPROVED => 'Aprovado',
            self::STATUS_REJECTED => 'Rejeitado',
            self::STATUS_REQUIRES_REVISION => 'Requer Revisão',
            default => 'Pendente'
        };
    }

    /**
     * Scope for pending approvals
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for approved approvals
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope for specific approval type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('approval_type', $type);
    }

    /**
     * Scope for expired approvals
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<', now());
    }
}
