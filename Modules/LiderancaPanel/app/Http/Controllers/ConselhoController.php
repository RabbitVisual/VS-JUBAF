<?php

namespace Modules\LiderancaPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Diretoria\App\Models\Pauta;
use Modules\Diretoria\App\Models\DiretoriaApproval;
use Modules\Diretoria\App\Models\AtaDocumento;
use Modules\Diretoria\App\Models\Reuniao;
use Modules\Diretoria\App\Models\DiretoriaMember;
use Modules\Diretoria\App\Services\DiretoriaApiService;
use Modules\Diretoria\App\Services\DiretoriaPdfService;
use Modules\Diretoria\App\Services\DiretoriaSettings;
use Modules\Diretoria\App\Services\DiretoriaAuditService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ConselhoController extends Controller
{
    /**
     * Conselho - visão liderancaal (layout liderancaal).
     */
    public function index(): View
    {
        if (! class_exists(Reuniao::class)) {
            return view('diretoria::liderancapanel.index', [
                'stats' => ['total_members' => 0, 'upcoming_meetings' => 0, 'pending_approvals' => 0, 'completed_meetings' => 0],
                'recentMeetings' => collect(),
                'pendingApprovals' => collect(),
            ]);
        }

        $stats = [
            'total_members' => DiretoriaMember::active()->count(),
            'upcoming_meetings' => Reuniao::upcoming()->count(),
            'pending_approvals' => DiretoriaApproval::pending()->count(),
            'completed_meetings' => Reuniao::completed()->count(),
        ];

        $recentMeetings = Reuniao::with('creator')
            ->orderBy('scheduled_date', 'desc')
            ->limit(5)
            ->get();

        $pendingApprovals = DiretoriaApproval::with(['requester', 'approver'])
            ->pending()
            ->orderBy('submitted_at', 'asc')
            ->limit(10)
            ->get();

        return view('diretoria::liderancapanel.index', compact('stats', 'recentMeetings', 'pendingApprovals'));
    }

    /**
     * Aprovações pendentes (layout liderancaal).
     */
    public function approvals(): View
    {
        $pendingApprovals = DiretoriaApproval::with(['requester', 'approver'])
            ->pending()
            ->orderBy('submitted_at', 'asc')
            ->paginate(15);

        return view('diretoria::liderancapanel.approvals.index', compact('pendingApprovals'));
    }

    /**
     * Detalhe de uma aprovação (layout liderancaal).
     */
    public function showApproval($approval): View
    {
        $approval = DiretoriaApproval::with(['requester', 'approver'])->findOrFail($approval);

        return view('diretoria::liderancapanel.approvals.show', compact('approval'));
    }

    /**
     * Aprovar solicitação (mantém lideranca no layout liderancaal).
     */
    public function approve(Request $request, $approval): RedirectResponse
    {
        $approval = DiretoriaApproval::findOrFail($approval);
        $request->validate(['notes' => 'nullable|string']);

        $user = auth()->user();
        $DiretoriaMember = $user->DiretoriaMember ?? null;
        $allowAdminApproval = class_exists(DiretoriaSettings::class) ? DiretoriaSettings::allowAdminApproval() : true;
        $isAdminOrlideranca = $user->hasRole('admin') || $user->hasRole('lideranca');

        if ($DiretoriaMember) {
            $approval->approve($DiretoriaMember, $request->input('notes'));
        } elseif ($allowAdminApproval && $isAdminOrlideranca) {
            $approval->update([
                'status' => DiretoriaApproval::STATUS_APPROVED,
                'approved_by' => null,
                'approval_notes' => $request->input('notes'),
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => $user->id]),
            ]);
            $approval->runApprovalAction();
        } else {
            return redirect()->route('lideranca.conselho.approvals')->with('error', 'Você não tem permissão para aprovar.');
        }

        if (class_exists(DiretoriaAuditService::class)) {
            app(DiretoriaAuditService::class)->log('approval_approved', $approval, ['approval_type' => $approval->approval_type]);
        }

        return redirect()->route('lideranca.conselho.approvals')->with('success', 'Solicitação aprovada com sucesso.');
    }

    /**
     * Rejeitar solicitação (mantém lideranca no layout liderancaal).
     */
    public function reject(Request $request, $approval): RedirectResponse
    {
        $approval = DiretoriaApproval::findOrFail($approval);
        $request->validate(['reason' => 'required|string']);

        $user = auth()->user();
        $DiretoriaMember = $user->DiretoriaMember ?? null;
        $allowAdminApproval = class_exists(DiretoriaSettings::class) ? DiretoriaSettings::allowAdminApproval() : true;
        $isAdminOrlideranca = $user->hasRole('admin') || $user->hasRole('lideranca');

        if ($DiretoriaMember) {
            $approval->reject($DiretoriaMember, $request->input('reason'));
        } elseif ($allowAdminApproval && $isAdminOrlideranca) {
            $approval->update([
                'status' => DiretoriaApproval::STATUS_REJECTED,
                'approved_by' => null,
                'rejection_reason' => $request->input('reason'),
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => $user->id]),
            ]);
        } elseif ($isAdminOrlideranca) {
            $approval->update([
                'status' => DiretoriaApproval::STATUS_REJECTED,
                'approved_by' => null,
                'rejection_reason' => $request->input('reason'),
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => $user->id, 'dismissed_by_admin' => true]),
            ]);
        } else {
            return redirect()->route('lideranca.conselho.approvals')->with('error', 'Você não tem permissão para rejeitar.');
        }

        if (class_exists(DiretoriaAuditService::class)) {
            app(DiretoriaAuditService::class)->log('approval_rejected', $approval, ['approval_type' => $approval->approval_type]);
        }

        return redirect()->route('lideranca.conselho.approvals')->with('success', 'Solicitação rejeitada.');
    }

    /**
     * Lista de reuniões (somente leitura).
     */
    public function meetings(Request $request): View
    {
        $api = app(DiretoriaApiService::class);
        $meetings = $api->listMeetings(
            15,
            $request->input('status'),
            $request->input('type'),
            $request->input('date_from')
        );

        return view('diretoria::liderancapanel.meetings.index', compact('meetings'));
    }

    /**
     * Detalhe da reunião (somente leitura).
     */
    public function showMeeting(Reuniao $meeting): View
    {
        $meeting->load([
            'creator',
            'president.user',
            'agendas.presenter',
            'agendas.decisionMaker',
            'minutesVersions.creator',
            'minutesVersions.signatures.user',
        ]);

        return view('diretoria::liderancapanel.meetings.show', compact('meeting'));
    }

    /**
     * Download PDF da ata da reunião.
     */
    public function exportMinutesPdf(Reuniao $meeting): StreamedResponse
    {
        return app(DiretoriaPdfService::class)->downloadMinutesPdf($meeting);
    }

    /**
     * Download PDF da convocação.
     */
    public function exportConvocationPdf(Reuniao $meeting): StreamedResponse
    {
        return app(DiretoriaPdfService::class)->downloadConvocationPdf($meeting);
    }

    /**
     * Lista de pautas (somente leitura).
     */
    public function agendas(Request $request): View
    {
        $query = Pauta::with('meeting')->orderByDesc('meeting_id')->orderBy('order');

        if ($request->filled('meeting_id')) {
            $query->where('meeting_id', $request->meeting_id);
        }

        $agendas = $query->paginate(20);
        $meetings = Reuniao::orderBy('scheduled_date', 'desc')->limit(50)->get();

        return view('diretoria::liderancapanel.agendas.index', compact('agendas', 'meetings'));
    }

    /**
     * Lista de documentos (somente leitura).
     */
    public function documentsIndex(Request $request): View
    {
        $query = AtaDocumento::with(['uploader', 'meeting']);

        if ($request->has('type') && $request->type !== '') {
            $query->where('document_type', $request->type);
        }

        $documents = $query->orderBy('document_date', 'desc')->paginate(15);

        return view('diretoria::liderancapanel.documents.index', compact('documents'));
    }

    /**
     * Detalhe do documento (somente leitura).
     */
    public function documentShow(AtaDocumento $document): View
    {
        $document->load('meeting');

        return view('diretoria::liderancapanel.documents.show', compact('document'));
    }

    /**
     * Download do documento.
     */
    public function documentDownload(AtaDocumento $document): StreamedResponse
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $document->file_path,
            $document->title . '.' . $document->file_type
        );
    }

    /**
     * Lista de membros do conselho (somente leitura).
     */
    public function membersIndex(Request $request): View
    {
        $query = DiretoriaMember::with('user');

        if ($request->has('status') && $request->status === 'active') {
            $query->active();
        }

        $members = $query->orderBy('diretoria_role')->orderBy('term_start')->paginate(20);

        return view('diretoria::liderancapanel.members.index', compact('members'));
    }
}
