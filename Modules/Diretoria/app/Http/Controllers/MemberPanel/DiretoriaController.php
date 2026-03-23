<?php

namespace Modules\Diretoria\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\Diretoria\App\Models\Pauta;
use Modules\Diretoria\App\Models\DiretoriaApproval;
use Modules\Diretoria\App\Models\AtaDocumento;
use Modules\Diretoria\App\Models\Reuniao;
use Modules\Diretoria\App\Models\diretoriaProject;
use Modules\Diretoria\App\Services\DiretoriaApiService;

class diretoriaController extends Controller
{
    public function __construct(
        private DiretoriaApiService $api
    ) {}

    /**
     * Display member diretoria dashboard
     */
    public function index(): View
    {
        $member = auth()->user()->DiretoriaMember;

        if (! $member || ! $member->isActive()) {
            abort(403, 'Você não é um membro ativo da diretoria.');
        }

        $stats = [
            'my_meetings' => Reuniao::where('president_id', $member->id)->count(),
            'pending_agendas' => Pauta::where('presented_by', $member->id)
                ->where('status', 'pending')
                ->count(),
            'my_votes' => $member->votes()->count(),
            'my_approvals' => DiretoriaApproval::where('approved_by', $member->id)->count(),
        ];

        $myAgendas = Pauta::where('presented_by', $member->id)
            ->with('meeting')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $myVotes = $member->votes()->with('agenda')->orderBy('created_at', 'desc')->get();

        $upcomingMeetings = Reuniao::upcoming()
            ->with('creator')
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        $recentAgendas = Pauta::where('presented_by', $member->id)
            ->with('meeting')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingApprovals = DiretoriaApproval::pending()
            ->with(['requester'])
            ->orderBy('submitted_at')
            ->limit(10)
            ->get();

        return view('Diretoria::memberpanel.index', compact('member', 'stats', 'upcomingMeetings', 'recentAgendas', 'myAgendas', 'myVotes', 'pendingApprovals'));
    }

    // =================== MEETINGS ===================

    /**
     * Display meetings for diretoria member (usa DiretoriaApiService)
     */
    public function meetings(): View
    {
        $member = auth()->user()->DiretoriaMember;
        $meetings = $this->api->listMeetings(10);

        return view('Diretoria::memberpanel.meetings.index', compact('meetings', 'member'));
    }

    /**
     * Show meeting details for member
     */
    public function showMeeting(Reuniao $meeting): View
    {
        $member = auth()->user()->DiretoriaMember;

        $meeting->load([
            'creator',
            'president.user',
            'agendas.presenter.user',
            'agendas.decisionMaker.user',
            'agendas.votes.DiretoriaMember.user',
        ]);

        // Check if member has voted on each agenda
        foreach ($meeting->agendas as $agenda) {
            $agenda->member_vote = $agenda->votes()->where('diretoria_member_id', $member->id)->first();
        }

        return view('Diretoria::memberpanel.meetings.show', compact('meeting', 'member'));
    }

    /**
     * Cast vote on agenda
     */
    public function castVote(Request $request, Pauta $agenda): JsonResponse
    {
        $validated = $request->validate([
            'vote' => 'required|in:yes,no,abstain',
            'comments' => 'nullable|string|max:500',
        ]);

        $member = auth()->user()->DiretoriaMember;

        // Check if member can vote
        if (! $member || ! $member->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para votar.',
            ], 403);
        }

        // Check if meeting is in progress
        if (! $agenda->meeting->isInProgress()) {
            return response()->json([
                'success' => false,
                'message' => 'A reunião não está em andamento.',
            ], 422);
        }

        // Check if member already voted
        $existingVote = $agenda->votes()->where('diretoria_member_id', $member->id)->first();

        if ($existingVote) {
            return response()->json([
                'success' => false,
                'message' => 'Você já votou nesta pauta.',
            ], 422);
        }

        // Cast vote
        $agenda->votes()->create([
            'diretoria_member_id' => $member->id,
            'vote' => $validated['vote'],
            'comments' => $validated['comments'] ?? null,
            'voted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Voto registrado com sucesso!',
        ]);
    }

    // =================== AGENDAS ===================

    /**
     * Display agendas presented by member
     */
    public function agendas(): View
    {
        $member = auth()->user()->DiretoriaMember;

        $agendas = Pauta::where('presented_by', $member->id)
            ->with(['meeting', 'decisionMaker.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('Diretoria::memberpanel.agendas.index', compact('agendas', 'member'));
    }

    /**
     * Create agenda item
     */
    public function createAgenda(): View
    {
        $member = auth()->user()->DiretoriaMember;
        $upcomingMeetings = Reuniao::upcoming()->get();

        return view('Diretoria::memberpanel.agendas.create', compact('member', 'upcomingMeetings'));
    }

    /**
     * Store agenda item
     */
    public function storeAgenda(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meeting_id' => 'required|exists:diretoria_meetings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $member = auth()->user()->DiretoriaMember;

        // Check if meeting exists and is upcoming
        $meeting = Reuniao::findOrFail($validated['meeting_id']);

        if (! $meeting->isScheduled()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível adicionar pautas a esta reunião.',
            ], 422);
        }

        $maxOrder = $meeting->agendas()->max('order') ?? 0;

        $meeting->agendas()->create([
            ...$validated,
            'presented_by' => $member->id,
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pauta criada com sucesso!',
            'redirect' => route('memberpanel.Diretoria.agendas.index'),
        ]);
    }

    // =================== APPROVALS ===================

    /**
     * Display approvals handled by member
     */
    public function approvals(): View
    {
        $member = auth()->user()->DiretoriaMember;

        $approvals = DiretoriaApproval::where('approved_by', $member->id)
            ->with(['requester'])
            ->orderBy('reviewed_at', 'desc')
            ->paginate(15);

        return view('Diretoria::memberpanel.approvals.index', compact('approvals', 'member'));
    }

    /**
     * Display pending approvals for review
     */
    public function pendingApprovals(): View
    {
        $member = auth()->user()->DiretoriaMember;

        $approvals = DiretoriaApproval::pending()
            ->with(['requester'])
            ->orderBy('submitted_at')
            ->paginate(15);

        return view('Diretoria::memberpanel.approvals.pending', compact('approvals', 'member'));
    }

    /**
     * Show approval details
     */
    public function showApproval(DiretoriaApproval $approval): View
    {
        $member = auth()->user()->DiretoriaMember;

        // Check if member can view this approval
        if ($approval->approved_by !== $member->id && $approval->isPending()) {
            abort(403);
        }

        $approval->load(['requester', 'approver.user']);

        return view('Diretoria::memberpanel.approvals.show', compact('approval', 'member'));
    }

    /**
     * Submit approval request
     */
    public function submitApprovalRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'approval_type' => 'required|in:account_activation,ministry_membership,event_creation,financial_request,document_approval,policy_change,other',
            'request_details' => 'required|string',
            'metadata' => 'nullable|array',
        ]);

        DiretoriaApproval::create([
            'approvable_type' => null, // Will be set based on context
            'approvable_id' => null,   // Will be set based on context
            ...$validated,
            'requested_by' => auth()->id(),
            'submitted_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de aprovação enviada com sucesso!',
        ]);
    }

    // =================== PROFILE ===================

    /**
     * Display member profile
     */
    public function profile(): View
    {
        $member = auth()->user()->DiretoriaMember;
        $member->load('user');

        $stats = [
            'total_meetings' => Reuniao::whereJsonContains('participants', (string) $member->id)->count(),
            'approved_agendas' => Pauta::where('presented_by', $member->id)
                ->where('status', 'approved')
                ->count(),
            'total_votes' => $member->votes()->count(),
            'total_projects' => diretoriaProject::where('proposer_id', $member->user->id)->count(),
        ];

        $recentvotes = $member->votes()
            ->with(['agenda.meeting'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $myprojects = diretoriaProject::where('proposer_id', $member->user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('Diretoria::memberpanel.profile.index', compact('member', 'stats', 'recentvotes', 'myprojects'));
    }

    /**
     * Update member profile
     */
    public function updateProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $member = auth()->user()->DiretoriaMember;

        $validated = $request->validate([
            'responsibilities' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $member->update($validated);

        return redirect()->route('memberpanel.Diretoria.profile.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    // =================== DOCUMENTS ===================

    /**
     * Display diretoria documents
     */
    public function documents(Request $request): View
    {
        $member = auth()->user()->DiretoriaMember;

        $query = AtaDocumento::with('uploader');

        if ($request->has('type') && ! empty($request->type)) {
            $query->where('document_type', $request->type);
        }

        // diretoria members can see all active documents
        $documents = $query->active()
            ->orderBy('document_date', 'desc')
            ->paginate(15);

        return view('Diretoria::memberpanel.documents.index', compact('documents', 'member'));
    }

    /**
     * Download document
     */
    public function downloadDocument(AtaDocumento $document)
    {
        $member = auth()->user()->DiretoriaMember;

        // Members can download any active document
        if (! $document->is_active) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $document->title.'.'.$document->file_type);
    }

    // =================== PROJECTS ===================

    /**
     * Display diretoria projects
     */
    public function projects(): View
    {
        $member = auth()->user()->DiretoriaMember;

        $projects = diretoriaProject::with(['proposer', 'reviewer', 'ministry'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('Diretoria::memberpanel.projects.index', compact('projects', 'member'));
    }

    /**
     * Create project proposal
     */
    public function createProject(): View
    {
        $member = auth()->user()->DiretoriaMember;
        $ministries = \Modules\Ministries\App\Models\Ministry::active()->orderBy('name')->get();

        return view('Diretoria::memberpanel.projects.create', compact('member', 'ministries'));
    }

    /**
     * Store project proposal
     */
    public function storeProject(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'justification' => 'nullable|string',
            'goals' => 'nullable|string',
            'department' => 'nullable|string',
            'ministry_id' => 'nullable|exists:ministries,id',
            'estimated_cost' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        diretoriaProject::create([
            ...$validated,
            'proposer_id' => auth()->id(),
            'status' => diretoriaProject::STATUS_SUBMITTED,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Projeto submetido com sucesso!',
            'redirect' => route('memberpanel.Diretoria.projects.index'),
        ]);
    }

    /**
     * Show project details
     */
    public function showProject(diretoriaProject $project): View
    {
        $member = auth()->user()->DiretoriaMember;
        $project->load(['proposer', 'reviewer', 'ministry']);

        return view('Diretoria::memberpanel.projects.show', compact('project', 'member'));
    }
}
