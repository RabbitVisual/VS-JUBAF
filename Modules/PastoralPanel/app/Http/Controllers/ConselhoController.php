<?php

namespace Modules\PastoralPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilAgenda;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
use Modules\ChurchCouncil\App\Models\CouncilDocument;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Models\CouncilMember;
use Modules\ChurchCouncil\App\Models\CouncilProject;
use Modules\ChurchCouncil\App\Services\ChurchCouncilApiService;
use Modules\ChurchCouncil\App\Services\ChurchCouncilPdfService;
use Modules\ChurchCouncil\App\Services\ChurchCouncilSettings;
use Modules\ChurchCouncil\App\Services\CouncilAuditService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConselhoController extends Controller
{
    /**
     * Conselho - visão pastoral (layout pastoral).
     */
    public function index(): View
    {
        if (! class_exists(CouncilMeeting::class)) {
            return view('churchcouncil::pastoralpanel.index', [
                'stats' => ['total_members' => 0, 'upcoming_meetings' => 0, 'pending_approvals' => 0, 'completed_meetings' => 0],
                'recentMeetings' => collect(),
                'pendingApprovals' => collect(),
            ]);
        }

        $stats = [
            'total_members' => CouncilMember::active()->count(),
            'upcoming_meetings' => CouncilMeeting::upcoming()->count(),
            'pending_approvals' => CouncilApproval::pending()->count(),
            'completed_meetings' => CouncilMeeting::completed()->count(),
        ];

        $recentMeetings = CouncilMeeting::with('creator')
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        $pendingApprovals = CouncilApproval::with(['requester', 'approver'])
            ->pending()
            ->orderBy('submitted_at', 'asc')
            ->limit(10)
            ->get();

        return view('churchcouncil::pastoralpanel.index', compact('stats', 'recentMeetings', 'pendingApprovals'));
    }

    /**
     * Aprovações pendentes (layout pastoral).
     */
    public function approvals(): View
    {
        $pendingApprovals = CouncilApproval::with(['requester', 'approver'])
            ->pending()
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);

        return view('churchcouncil::pastoralpanel.approvals.index', compact('pendingApprovals'));
    }

    /**
     * Detalhe de uma aprovação (layout pastoral).
     */
    public function showApproval($approval): View
    {
        $approval = CouncilApproval::with(['requester', 'approver'])->findOrFail($approval);

        return view('churchcouncil::pastoralpanel.approvals.show', compact('approval'));
    }

    /**
     * Aprovar solicitação (mantém pastor no layout pastoral).
     */
    public function approve(Request $request, $approval): RedirectResponse
    {
        $approval = CouncilApproval::findOrFail($approval);
        $request->validate(['notes' => 'nullable|string']);

        $user = auth()->user();
        $councilMember = $user->councilMember ?? null;
        $allowAdminApproval = class_exists(ChurchCouncilSettings::class) ? ChurchCouncilSettings::allowAdminApproval() : true;
        $isAdminOrPastor = $user->hasRole('admin') || $user->hasRole('pastor');

        if ($councilMember) {
            $approval->approve($councilMember, $request->input('notes'));
        } elseif ($allowAdminApproval && $isAdminOrPastor) {
            $approval->update([
                'status' => CouncilApproval::STATUS_APPROVED,
                'approved_by' => null,
                'approval_notes' => $request->input('notes'),
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => $user->id]),
            ]);
            $approval->runApprovalAction();
        } else {
            return redirect()->route('pastor.conselho.approvals')->with('error', 'Você não tem permissão para aprovar.');
        }

        if (class_exists(CouncilAuditService::class)) {
            app(CouncilAuditService::class)->log('approval_approved', $approval, ['approval_type' => $approval->approval_type]);
        }

        return redirect()->route('pastor.conselho.approvals')->with('success', 'Solicitação aprovada com sucesso.');
    }

    /**
     * Rejeitar solicitação (mantém pastor no layout pastoral).
     */
    public function reject(Request $request, $approval): RedirectResponse
    {
        $approval = CouncilApproval::findOrFail($approval);
        $request->validate(['reason' => 'required|string']);

        $user = auth()->user();
        $councilMember = $user->councilMember ?? null;
        $allowAdminApproval = class_exists(ChurchCouncilSettings::class) ? ChurchCouncilSettings::allowAdminApproval() : true;
        $isAdminOrPastor = $user->hasRole('admin') || $user->hasRole('pastor');

        if ($councilMember) {
            $approval->reject($councilMember, $request->input('reason'));
        } elseif ($allowAdminApproval && $isAdminOrPastor) {
            $approval->update([
                'status' => CouncilApproval::STATUS_REJECTED,
                'approved_by' => null,
                'rejection_reason' => $request->input('reason'),
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => $user->id]),
            ]);
        } elseif ($isAdminOrPastor) {
            $approval->update([
                'status' => CouncilApproval::STATUS_REJECTED,
                'approved_by' => null,
                'rejection_reason' => $request->input('reason'),
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => $user->id, 'dismissed_by_admin' => true]),
            ]);
        } else {
            return redirect()->route('pastor.conselho.approvals')->with('error', 'Você não tem permissão para rejeitar.');
        }

        if (class_exists(CouncilAuditService::class)) {
            app(CouncilAuditService::class)->log('approval_rejected', $approval, ['approval_type' => $approval->approval_type]);
        }

        return redirect()->route('pastor.conselho.approvals')->with('success', 'Solicitação rejeitada.');
    }

    /**
     * Lista de reuniões (somente leitura).
     */
    public function meetings(Request $request): View
    {
        $api = app(ChurchCouncilApiService::class);
        $meetings = $api->listMeetings(
            15,
            $request->input('status'),
            $request->input('type'),
            $request->input('date_from')
        );

        return view('churchcouncil::pastoralpanel.meetings.index', compact('meetings'));
    }

    /**
     * Detalhe da reunião (somente leitura).
     */
    public function showMeeting(CouncilMeeting $meeting): View
    {
        $meeting->load([
            'creator',
            'president.user',
            'agendas.presenter',
            'agendas.decisionMaker',
            'minutesVersions.creator',
            'minutesVersions.signatures.user',
        ]);

        return view('churchcouncil::pastoralpanel.meetings.show', compact('meeting'));
    }

    /**
     * Download PDF da ata da reunião.
     */
    public function exportMinutesPdf(CouncilMeeting $meeting): StreamedResponse
    {
        return app(ChurchCouncilPdfService::class)->downloadMinutesPdf($meeting);
    }

    /**
     * Download PDF da convocação.
     */
    public function exportConvocationPdf(CouncilMeeting $meeting): StreamedResponse
    {
        return app(ChurchCouncilPdfService::class)->downloadConvocationPdf($meeting);
    }

    /**
     * Lista de pautas (somente leitura).
     */
    public function agendas(Request $request): View
    {
        $query = CouncilAgenda::with('meeting')->orderByDesc('meeting_id')->orderBy('order');

        if ($request->filled('meeting_id')) {
            $query->where('meeting_id', $request->meeting_id);
        }

        $agendas = $query->paginate(20);
        $meetings = CouncilMeeting::orderBy('scheduled_date', 'desc')->limit(50)->get();

        return view('churchcouncil::pastoralpanel.agendas.index', compact('agendas', 'meetings'));
    }

    /**
     * Lista de documentos (somente leitura).
     */
    public function documentsIndex(Request $request): View
    {
        $query = CouncilDocument::with(['uploader', 'meeting']);

        if ($request->has('type') && $request->type !== '') {
            $query->where('document_type', $request->type);
        }

        $documents = $query->orderBy('document_date', 'desc')->paginate(15);

        return view('churchcouncil::pastoralpanel.documents.index', compact('documents'));
    }

    /**
     * Detalhe do documento (somente leitura).
     */
    public function documentShow(CouncilDocument $document): View
    {
        $document->load('meeting');

        return view('churchcouncil::pastoralpanel.documents.show', compact('document'));
    }

    /**
     * Download do documento.
     */
    public function documentDownload(CouncilDocument $document): StreamedResponse
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $document->file_path,
            $document->title . '.' . $document->file_type
        );
    }

    /**
     * Lista de projetos (somente leitura).
     */
    public function projectsIndex(Request $request): View
    {
        $projects = CouncilProject::with(['proposer', 'ministry', 'reviewer.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('churchcouncil::pastoralpanel.projects.index', compact('projects'));
    }

    /**
     * Detalhe do projeto (somente leitura).
     */
    public function projectShow(CouncilProject $project): View
    {
        $project->load(['proposer', 'ministry', 'reviewer.user']);

        return view('churchcouncil::pastoralpanel.projects.show', compact('project'));
    }

    /**
     * Lista de membros do conselho (somente leitura).
     */
    public function membersIndex(Request $request): View
    {
        $query = CouncilMember::with('user');

        if ($request->has('status') && $request->status === 'active') {
            $query->active();
        }

        $members = $query->orderBy('council_role')->orderBy('term_start')->paginate(20);

        return view('churchcouncil::pastoralpanel.members.index', compact('members'));
    }
}
