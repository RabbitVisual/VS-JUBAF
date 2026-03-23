<?php

namespace Modules\Diretoria\App\Observers;

use Modules\Diretoria\App\Models\diretoriaApproval;
use Modules\Diretoria\App\Models\diretoriaMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class diretoriaApprovalObserver
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Notify diretoria members when a new approval request is submitted.
     */
    public function created(diretoriaApproval $approval): void
    {
        if ($approval->status !== diretoriaApproval::STATUS_PENDING) {
            return;
        }
        $diretoriaUsers = diretoriaMember::active()->with('user')->get()->pluck('user')->filter();
        // Notificar apenas quem pode acessar o admin; nunca enviar ao solicitante (ex.: filiação a ministério)
        $requestedById = (int) $approval->requested_by;
        $users = $diretoriaUsers->filter(fn ($u) => $u && $u->hasAdminAccess() && (int) $u->id !== $requestedById);
        if ($users->isEmpty()) {
            return;
        }
        $typeLabel = $approval->approval_type_display ?? 'Solicitação';
        $this->inApp->sendToUsers(
            $users,
            'Nova solicitação de aprovação',
            "{$typeLabel} aguardando análise da diretoria.",
            [
                'type' => 'warning',
                'priority' => 'normal',
                'action_url' => url('/admin/conselho/aprovacoes/'.$approval->id),
                'action_text' => 'Ver solicitação',
            ]
        );
    }

    /**
     * When a ministry plan approval is rejected/requires revision: sync plan to draft and notify leaders.
     * When approved: notify ministry leaders.
     * When ebd_curriculum: update EBDCourse homologation_status.
     */
    public function updated(diretoriaApproval $approval): void
    {
        if ($approval->approval_type === diretoriaApproval::TYPE_EBD_CURRICULUM) {
            $this->handleEbdCurriculumApproval($approval);

            return;
        }

        if ($approval->approval_type !== diretoriaApproval::TYPE_MINISTRY_PLAN) {
            return;
        }

        $approvable = $approval->approvable;
        if (! $approvable || ! class_exists(\Modules\Ministries\App\Models\MinistryPlan::class) || ! $approvable instanceof \Modules\Ministries\App\Models\MinistryPlan) {
            return;
        }

        $ministry = $approvable->ministry;
        $leaders = $this->ministryLeaders($ministry);
        $planTitle = $approvable->title;
        $ministryName = $ministry ? $ministry->name : 'Ministério';

        if (in_array($approval->status, [diretoriaApproval::STATUS_REJECTED, diretoriaApproval::STATUS_REQUIRES_REVISION], true)) {
            $approvable->update(['status' => \Modules\Ministries\App\Models\MinistryPlan::STATUS_DRAFT]);
            if (class_exists(\Modules\Diretoria\App\Services\diretoriaAuditService::class)) {
                app(\Modules\Diretoria\App\Services\diretoriaAuditService::class)->log('ministry_plan_returned_to_draft', $approvable, [
                    'plan_id' => $approvable->id,
                    'approval_status' => $approval->status,
                ]);
            }
            if ($leaders->isNotEmpty()) {
                $msg = $approval->status === diretoriaApproval::STATUS_REJECTED
                    ? "O plano \"{$planTitle}\" ({$ministryName}) foi rejeitado pela diretoria."
                    : "O plano \"{$planTitle}\" ({$ministryName}) foi devolvido para revisão pela diretoria.";
                $planUrl = $ministry && function_exists('route') ? route('admin.ministries.plans.show', [$ministry, $approvable]) : null;
                $this->inApp->sendToUsers($leaders, 'Plano de ministério – Diretoria', $msg, [
                    'type' => 'warning',
                    'action_url' => $planUrl,
                    'action_text' => 'Ver plano',
                ]);
            }
            return;
        }

        if ($approval->status === diretoriaApproval::STATUS_APPROVED && $leaders->isNotEmpty()) {
            $planUrl = $ministry && function_exists('route') ? route('admin.ministries.plans.show', [$ministry, $approvable]) : null;
            $this->inApp->sendToUsers($leaders, 'Plano aprovado', "O plano \"{$planTitle}\" ({$ministryName}) foi aprovado pela diretoria e está em execução.", [
                'type' => 'success',
                'action_url' => $planUrl,
                'action_text' => 'Ver plano',
            ]);
        }
    }

    private function ministryLeaders($ministry): \Illuminate\Support\Collection
    {
        if (! $ministry) {
            return collect();
        }
        $ids = array_filter([$ministry->leader_id, $ministry->co_leader_id]);
        if (empty($ids)) {
            return collect();
        }
        return \App\Models\User::whereIn('id', $ids)->get();
    }

    private function handleEbdCurriculumApproval(diretoriaApproval $approval): void
    {
        $course = $approval->approvable;
        if (! $course || ! $course instanceof \Modules\EBD\App\Models\EBDCourse) {
            return;
        }

        if (in_array($approval->status, [diretoriaApproval::STATUS_REJECTED, diretoriaApproval::STATUS_REQUIRES_REVISION], true)) {
            $course->update([
                'homologation_status' => \Modules\EBD\App\Models\EBDCourse::HOMOLOGATION_DRAFT,
                'approved_at' => null,
                'approved_by' => null,
            ]);
            return;
        }

        if ($approval->status === diretoriaApproval::STATUS_APPROVED) {
            $course->update([
                'homologation_status' => \Modules\EBD\App\Models\EBDCourse::HOMOLOGATION_APPROVED,
                'approved_at' => now(),
                'approved_by' => $approval->approver?->user_id,
            ]);
        }
    }
}
