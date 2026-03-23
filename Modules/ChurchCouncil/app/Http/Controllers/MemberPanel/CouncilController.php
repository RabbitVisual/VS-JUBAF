<?php

namespace Modules\ChurchCouncil\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilAgenda;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
use Modules\ChurchCouncil\App\Models\CouncilDocument;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Models\CouncilProject;
use Modules\ChurchCouncil\App\Services\ChurchCouncilApiService;

class CouncilController extends Controller
{
    public function __construct(
        private ChurchCouncilApiService $api
    ) {}

    /**
     * Display member council dashboard
     */
    public function index(): View
    {
        $member = auth()->user()->councilMember;

        if (! $member || ! $member->isActive()) {
            abort(403, 'Você não é um membro ativo do conselho.');
        }

        $stats = [
            'my_meetings' => CouncilMeeting::where('president_id', $member->id)->count(),
            'pending_agendas' => CouncilAgenda::where('presented_by', $member->id)
                ->where('status', 'pending')
                ->count(),
            'my_votes' => $member->votes()->count(),
            'my_approvals' => CouncilApproval::where('approved_by', $member->id)->count(),
        ];

        $myAgendas = CouncilAgenda::where('presented_by', $member->id)
            ->with('meeting')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $myVotes = $member->votes()->with('agenda')->orderBy('created_at', 'desc')->get();

        $upcomingMeetings = CouncilMeeting::upcoming()
            ->with('creator')
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        $recentAgendas = CouncilAgenda::where('presented_by', $member->id)
            ->with('meeting')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $pendingApprovals = CouncilApproval::pending()
            ->with(['requester'])
            ->orderBy('submitted_at')
            ->limit(10)
            ->get();

        return view('churchcouncil::memberpanel.index', compact('member', 'stats', 'upcomingMeetings', 'recentAgendas', 'myAgendas', 'myVotes', 'pendingApprovals'));
    }

    // =================== MEETINGS ===================

    /**
     * Display meetings for council member (usa ChurchCouncilApiService)
     */
    public function meetings(): View
    {
        $member = auth()->user()->councilMember;
        $meetings = $this->api->listMeetings(10);

        return view('churchcouncil::memberpanel.meetings.index', compact('meetings', 'member'));
    }

    /**
     * Show meeting details for member
     */
    public function showMeeting(CouncilMeeting $meeting): View
    {
        $member = auth()->user()->councilMember;

        $meeting->load([
            'creator',
            'president.user',
            'agendas.presenter.user',
            'agendas.decisionMaker.user',
            'agendas.votes.councilMember.user',
        ]);

        // Check if member has voted on each agenda
        foreach ($meeting->agendas as $agenda) {
            $agenda->member_vote = $agenda->votes()->where('council_member_id', $member->id)->first();
        }

        return view('churchcouncil::memberpanel.meetings.show', compact('meeting', 'member'));
    }

    /**
     * Cast vote on agenda
     */
    public function castVote(Request $request, CouncilAgenda $agenda): JsonResponse
    {
        $validated = $request->validate([
            'vote' => 'required|in:yes,no,abstain',
            'comments' => 'nullable|string|max:500',
        ]);

        $member = auth()->user()->councilMember;

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
        $existingVote = $agenda->votes()->where('council_member_id', $member->id)->first();

        if ($existingVote) {
            return response()->json([
                'success' => false,
                'message' => 'Você já votou nesta pauta.',
            ], 422);
        }

        // Cast vote
        $agenda->votes()->create([
            'council_member_id' => $member->id,
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
        $member = auth()->user()->councilMember;

        $agendas = CouncilAgenda::where('presented_by', $member->id)
            ->with(['meeting', 'decisionMaker.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('churchcouncil::memberpanel.agendas.index', compact('agendas', 'member'));
    }

    /**
     * Create agenda item
     */
    public function createAgenda(): View
    {
        $member = auth()->user()->councilMember;
        $upcomingMeetings = CouncilMeeting::upcoming()->get();

        return view('churchcouncil::memberpanel.agendas.create', compact('member', 'upcomingMeetings'));
    }

    /**
     * Store agenda item
     */
    public function storeAgenda(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'meeting_id' => 'required|exists:council_meetings,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $member = auth()->user()->councilMember;

        // Check if meeting exists and is upcoming
        $meeting = CouncilMeeting::findOrFail($validated['meeting_id']);

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
            'redirect' => route('memberpanel.churchcouncil.agendas.index'),
        ]);
    }

    // =================== APPROVALS ===================

    /**
     * Display approvals handled by member
     */
    public function approvals(): View
    {
        $member = auth()->user()->councilMember;

        $approvals = CouncilApproval::where('approved_by', $member->id)
            ->with(['requester'])
            ->orderBy('reviewed_at', 'desc')
            ->paginate(15);

        return view('churchcouncil::memberpanel.approvals.index', compact('approvals', 'member'));
    }

    /**
     * Display pending approvals for review
     */
    public function pendingApprovals(): View
    {
        $member = auth()->user()->councilMember;

        $approvals = CouncilApproval::pending()
            ->with(['requester'])
            ->orderBy('submitted_at')
            ->paginate(15);

        return view('churchcouncil::memberpanel.approvals.pending', compact('approvals', 'member'));
    }

    /**
     * Show approval details
     */
    public function showApproval(CouncilApproval $approval): View
    {
        $member = auth()->user()->councilMember;

        // Check if member can view this approval
        if ($approval->approved_by !== $member->id && $approval->isPending()) {
            abort(403);
        }

        $approval->load(['requester', 'approver.user']);

        return view('churchcouncil::memberpanel.approvals.show', compact('approval', 'member'));
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

        CouncilApproval::create([
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
        $member = auth()->user()->councilMember;
        $member->load('user');

        $stats = [
            'total_meetings' => CouncilMeeting::whereJsonContains('participants', (string) $member->id)->count(),
            'approved_agendas' => CouncilAgenda::where('presented_by', $member->id)
                ->where('status', 'approved')
                ->count(),
            'total_votes' => $member->votes()->count(),
            'total_projects' => CouncilProject::where('proposer_id', $member->user->id)->count(),
        ];

        $recentvotes = $member->votes()
            ->with(['agenda.meeting'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $myprojects = CouncilProject::where('proposer_id', $member->user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('churchcouncil::memberpanel.profile.index', compact('member', 'stats', 'recentvotes', 'myprojects'));
    }

    /**
     * Update member profile
     */
    public function updateProfile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $member = auth()->user()->councilMember;

        $validated = $request->validate([
            'responsibilities' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $member->update($validated);

        return redirect()->route('memberpanel.churchcouncil.profile.index')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    // =================== DOCUMENTS ===================

    /**
     * Display council documents
     */
    public function documents(Request $request): View
    {
        $member = auth()->user()->councilMember;

        $query = CouncilDocument::with('uploader');

        if ($request->has('type') && ! empty($request->type)) {
            $query->where('document_type', $request->type);
        }

        // Council members can see all active documents
        $documents = $query->active()
            ->orderBy('document_date', 'desc')
            ->paginate(15);

        return view('churchcouncil::memberpanel.documents.index', compact('documents', 'member'));
    }

    /**
     * Download document
     */
    public function downloadDocument(CouncilDocument $document)
    {
        $member = auth()->user()->councilMember;

        // Members can download any active document
        if (! $document->is_active) {
            abort(404);
        }

        return Storage::disk('public')->download($document->file_path, $document->title.'.'.$document->file_type);
    }

    // =================== PROJECTS ===================

    /**
     * Display council projects
     */
    public function projects(): View
    {
        $member = auth()->user()->councilMember;

        $projects = CouncilProject::with(['proposer', 'reviewer', 'ministry'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('churchcouncil::memberpanel.projects.index', compact('projects', 'member'));
    }

    /**
     * Create project proposal
     */
    public function createProject(): View
    {
        $member = auth()->user()->councilMember;
        $ministries = \Modules\Ministries\App\Models\Ministry::active()->orderBy('name')->get();

        return view('churchcouncil::memberpanel.projects.create', compact('member', 'ministries'));
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

        CouncilProject::create([
            ...$validated,
            'proposer_id' => auth()->id(),
            'status' => CouncilProject::STATUS_SUBMITTED,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Projeto submetido com sucesso!',
            'redirect' => route('memberpanel.churchcouncil.projects.index'),
        ]);
    }

    /**
     * Show project details
     */
    public function showProject(CouncilProject $project): View
    {
        $member = auth()->user()->councilMember;
        $project->load(['proposer', 'reviewer', 'ministry']);

        return view('churchcouncil::memberpanel.projects.show', compact('project', 'member'));
    }
}
