<?php

namespace Modules\ChurchCouncil\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilAgenda;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Models\CouncilMember;
use Modules\ChurchCouncil\App\Services\ChurchCouncilApiService;
use Modules\ChurchCouncil\App\Models\MeetingMinutesVersion;
use Modules\ChurchCouncil\App\Models\MeetingMinutesSignature;
use Modules\ChurchCouncil\App\Services\CouncilAuditService;
use Modules\Notifications\App\Services\InAppNotificationService;

class CouncilController extends Controller
{
    public function __construct(
        private ChurchCouncilApiService $api,
        private CouncilAuditService $audit,
        private InAppNotificationService $inApp
    ) {}

    /**
     * Display the council dashboard
     */
    public function index(): View
    {
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

        $membersByRole = CouncilMember::active()
            ->selectRaw('council_role, count(*) as count')
            ->groupBy('council_role')
            ->get()
            ->map(function ($row) {
                $row->council_role_display = CouncilMember::getRoleDisplayName($row->council_role);
                return $row;
            });

        $meetingsByStatus = CouncilMeeting::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->map(function ($row) {
                $row->status_display = CouncilMeeting::getStatusDisplayName($row->status);
                return $row;
            });

        return view('churchcouncil::admin.dashboard', compact('stats', 'recentMeetings', 'pendingApprovals', 'membersByRole', 'meetingsByStatus'));
    }

    // =================== COUNCIL MEMBERS ===================

    /**
     * Display council members
     */
    public function members(Request $request): View
    {
        $query = CouncilMember::with('user');

        // Filter by role
        if ($request->has('role') && ! empty($request->role)) {
            $query->where('council_role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $members = $query->orderBy('council_role')->orderBy('term_start')->paginate(15);

        return view('churchcouncil::admin.members.index', compact('members'));
    }

    /**
     * Show form to create council member
     */
    public function createMember(): View
    {
        $users = User::whereDoesntHave('councilMember')->get();

        return view('churchcouncil::admin.members.create', compact('users'));
    }

    /**
     * Store council member
     */
    public function storeMember(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'council_position' => 'required|string|max:255',
            'council_role' => 'required|in:president,vice_president,secretary,treasurer,member,pastor,deacon',
            'term_start' => 'required|date',
            'term_end' => 'nullable|date|after:term_start',
            'responsibilities' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        // Check if user already has a council position
        $existing = CouncilMember::where('user_id', $validated['user_id'])
            ->where('is_active', true)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuário já possui um cargo no conselho.',
            ], 422);
        }

        CouncilMember::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Membro do conselho criado com sucesso!',
            'redirect' => route('admin.churchcouncil.members.index'),
        ]);
    }

    /**
     * Show form to edit council member
     */
    public function editMember(CouncilMember $member): View
    {
        return view('churchcouncil::admin.members.edit', compact('member'));
    }

    /**
     * Update council member
     */
    public function updateMember(Request $request, CouncilMember $member): JsonResponse
    {
        $validated = $request->validate([
            'council_position' => 'required|string|max:255',
            'council_role' => 'required|in:president,vice_president,secretary,treasurer,member,pastor,deacon',
            'term_start' => 'required|date',
            'term_end' => 'nullable|date|after:term_start',
            'is_active' => 'boolean',
            'responsibilities' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $member->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Membro do conselho atualizado com sucesso!',
            'redirect' => route('admin.churchcouncil.members.index'),
        ]);
    }

    /**
     * Delete council member
     */
    public function destroyMember(CouncilMember $member): JsonResponse
    {
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Membro do conselho removido com sucesso!',
        ]);
    }

    // =================== COUNCIL MEETINGS ===================

    /**
     * Display council meetings (usa ChurchCouncilApiService – mesma lógica da API v1)
     */
    public function meetings(Request $request): View
    {
        $perPage = 15;
        $status = $request->input('status');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $meetings = $this->api->listMeetings($perPage, $status, $type, $dateFrom);

        return view('churchcouncil::admin.meetings.index', compact('meetings'));
    }

    /**
     * Show form to create meeting
     */
    public function createMeeting(): View
    {
        $presidents = CouncilMember::active()
            ->whereIn('council_role', ['president', 'vice_president'])
            ->with('user')
            ->get();

        $councilMembers = CouncilMember::active()
            ->with('user')
            ->orderBy('council_role')
            ->get();

        return view('churchcouncil::admin.meetings.create', compact('presidents', 'councilMembers'));
    }

    /**
     * Store meeting (usa ChurchCouncilApiService – sem duplicar regras)
     */
    public function storeMeeting(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'meeting_type' => 'required|in:ordinary,extraordinary,emergency',
            'president_id' => 'nullable|exists:council_members,id',
            'participant_ids' => 'nullable|array',
            'participant_ids.*' => 'exists:council_members,id',
        ]);

        $validated['created_by'] = auth()->id();

        $meeting = $this->api->createMeeting($validated);

        $this->audit->log('meeting_created', $meeting, [
            'scheduled_date' => $meeting->scheduled_date,
            'meeting_type' => $meeting->meeting_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reunião criada com sucesso!',
            'redirect' => route('admin.churchcouncil.meetings.index'),
        ]);
    }

    /**
     * Show meeting details
     */
    public function showMeeting(CouncilMeeting $meeting): View
    {
        $meeting->load([
            'creator',
            'president',
            'agendas.presenter',
            'agendas.decisionMaker',
            'agendas.votes.councilMember.user',
            'minutesVersions.creator',
            'minutesVersions.signatures.user',
        ]);

        $user = auth()->user();
        $canSignMinutes = $user && $user->relationLoaded('councilMember')
            ? (bool) $user->councilMember?->is_active
            : (bool) ($user?->councilMember?->is_active ?? false);

        return view('churchcouncil::admin.meetings.show', compact('meeting', 'canSignMinutes'));
    }

    /**
     * Start meeting
     */
    public function startMeeting(CouncilMeeting $meeting): JsonResponse
    {
        if (! $meeting->start()) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível iniciar a reunião.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reunião iniciada com sucesso!',
        ]);
    }

    /**
     * End meeting
     */
    public function endMeeting(Request $request, CouncilMeeting $meeting): JsonResponse
    {
        $validated = $request->validate([
            'minutes' => 'required|string',
        ]);

        $meeting->update(['minutes' => $validated['minutes']]);

        if (auth()->user()) {
            $version = $meeting->snapshotMinutesVersion(auth()->user(), $validated['minutes'], 'council_approved');
            event(new \Modules\ChurchCouncil\App\Events\MinutesPendingSignature($meeting, $version));
        }

        if (! $meeting->end()) {
            return response()->json([
                'success' => false,
                'message' => 'Não foi possível encerrar a reunião.',
            ], 422);
        }

        $this->audit->log('meeting_closed', $meeting, [
            'status' => $meeting->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reunião encerrada com sucesso!',
        ]);
    }

    // =================== COUNCIL AGENDAS ===================

    /**
     * Display agendas for a meeting
     */
    public function agendas(Request $request, CouncilMeeting $meeting): View
    {
        $query = $meeting->agendas()->orderBy('order');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $agendas = $query->paginate(10)->withQueryString();

        return view('churchcouncil::admin.agendas.index', compact('meeting', 'agendas'));
    }

    /**
     * Show create agenda form
     */
    public function createAgenda(CouncilMeeting $meeting): View
    {
        $members = CouncilMember::with('user')->active()->get();

        return view('churchcouncil::admin.agendas.create', compact('meeting', 'members'));
    }

    /**
     * Show edit agenda form
     */
    public function editAgenda(CouncilMeeting $meeting, CouncilAgenda $agenda): View
    {
        $members = CouncilMember::with('user')->active()->get();

        return view('churchcouncil::admin.agendas.edit', compact('meeting', 'agenda', 'members'));
    }

    /**
     * Store agenda item
     */
    public function storeAgenda(Request $request, CouncilMeeting $meeting): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'presented_by' => 'nullable|exists:council_members,id',
            'requires_assembly_vote' => 'nullable|boolean',
        ]);

        $maxOrder = $meeting->agendas()->max('order') ?? 0;

        $agenda = $meeting->agendas()->create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'presented_by' => $validated['presented_by'] ?? null,
            'requires_assembly_vote' => $request->boolean('requires_assembly_vote'),
            'order' => $maxOrder + 1,
        ]);

        if (auth()->user()) {
            $agenda->snapshotVersion(auth()->user());
        }

        $this->audit->log('agenda_created', $agenda, [
            'requires_assembly_vote' => $agenda->requires_assembly_vote,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pauta adicionada com sucesso!',
        ]);
    }

    /**
     * Update agenda item
     */
    public function updateAgenda(Request $request, CouncilMeeting $meeting, CouncilAgenda $agenda): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'presented_by' => 'nullable|exists:council_members,id',
            'status' => 'required|in:pending,discussed,approved,rejected,postponed',
            'requires_assembly_vote' => 'nullable|boolean',
        ]);

        $agenda->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'presented_by' => $validated['presented_by'] ?? null,
            'status' => $validated['status'],
            'requires_assembly_vote' => $request->boolean('requires_assembly_vote', $agenda->requires_assembly_vote),
        ]);

        if (auth()->user()) {
            $agenda->snapshotVersion(auth()->user());
        }

        $this->audit->log('agenda_updated', $agenda, [
            'status' => $agenda->status,
            'requires_assembly_vote' => $agenda->requires_assembly_vote,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pauta atualizada com sucesso!',
        ]);
    }

    /**
     * Update agenda decision
     */
    public function updateAgendaDecision(Request $request, CouncilAgenda $agenda): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'decision' => 'nullable|string',
        ]);

        $councilMember = auth()->user()->councilMember;

        if (! $councilMember) {
            return response()->json([
                'success' => false,
                'message' => 'Você não é membro do conselho.',
            ], 403);
        }

        if ($validated['status'] === 'approved') {
            if (! $agenda->approve($councilMember, $validated['decision'] ?? null)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pauta deve estar em discussão para ser aprovada.',
                ], 422);
            }

            // Council-approved items that depend on assembly are now recommendations.
            if ($agenda->requires_assembly_vote && $agenda->assembly_decision === null) {
                $agenda->update([
                    'assembly_decision' => 'pending',
                ]);
            }
        } else {
            if (! $agenda->reject($councilMember, $validated['decision'] ?? 'Rejeitado')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pauta deve estar em discussão para ser rejeitada.',
                ], 422);
            }
        }

        $this->audit->log('agenda_decision_recorded', $agenda, [
            'status' => $agenda->status,
            'decision' => $validated['decision'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Decisão registrada com sucesso!',
        ]);
    }

    // =================== APPROVALS ===================

    /**
     * Display approvals
     */
    public function approvals(Request $request): View
    {
        $query = CouncilApproval::with(['requester', 'approver']);

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && ! empty($request->type)) {
            $query->where('approval_type', $request->type);
        }

        $approvals = $query->orderBy('submitted_at', 'desc')->paginate(15);

        return view('churchcouncil::admin.approvals.index', compact('approvals'));
    }

    /**
     * Painel de Homologação de Planejamento – eventos que exigem aprovação do conselho.
     */
    public function planningApprovals(Request $request): View
    {
        $query = CouncilApproval::with(['requester', 'approver', 'approvable' => function ($q) {
            if (class_exists(\Modules\Events\App\Models\Event::class)) {
                $q->with(['ministry', 'ministryPlan']);
            }
        }])->where('approval_type', CouncilApproval::TYPE_EVENT_CREATION)
            ->whereIn('status', [CouncilApproval::STATUS_PENDING, CouncilApproval::STATUS_REQUIRES_REVISION]);

        if ($request->filled('ministry_id') && class_exists(\Modules\Events\App\Models\Event::class)) {
            $ministryId = (int) $request->input('ministry_id');
            $query->whereHas('approvable', function ($q) use ($ministryId) {
                $q->where('ministry_id', $ministryId);
            });
        }

        if (class_exists(\Modules\Events\App\Models\Event::class)) {
            $query->whereHas('approvable', function ($q) {
                $q->where('requires_council_approval', true)
                    ->whereIn('status', [
                        \Modules\Events\App\Models\Event::STATUS_WAITING_APPROVAL,
                        \Modules\Events\App\Models\Event::STATUS_DRAFT,
                    ]);
            });
        }

        $approvals = $query->orderBy('submitted_at', 'asc')->paginate(15)->withQueryString();

        $ministries = class_exists(\Modules\Ministries\App\Models\Ministry::class)
            ? \Modules\Ministries\App\Models\Ministry::orderBy('name')->get()
            : collect();

        return view('churchcouncil::admin.planning.index', compact('approvals', 'ministries'));
    }

    /**
     * Registrar visto digital em uma versão de ata.
     */
    public function signMinutes(Request $request, CouncilMeeting $meeting, MeetingMinutesVersion $minutesVersion): JsonResponse
    {
        $user = auth()->user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Autenticação necessária.',
            ], 401);
        }

        if ($minutesVersion->council_meeting_id !== $meeting->id) {
            return response()->json([
                'success' => false,
                'message' => 'Versão de ata não pertence a esta reunião.',
            ], 404);
        }

        $councilMember = $user->councilMember;
        if (! $councilMember || ! $councilMember->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas membros ativos do conselho podem dar visto na ata.',
            ], 403);
        }

        MeetingMinutesSignature::firstOrCreate(
            [
                'minutes_version_id' => $minutesVersion->id,
                'user_id' => $user->id,
            ],
            [
                'signed_at' => now(),
            ]
        );

        if (class_exists(\Modules\ChurchCouncil\App\Services\CouncilAuditService::class)) {
            try {
                app(\Modules\ChurchCouncil\App\Services\CouncilAuditService::class)->log('minutes_signed', $meeting, [
                    'minutes_version_id' => $minutesVersion->id,
                    'state' => $minutesVersion->state,
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Council audit for minutes_signed failed: '.$e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Visto registrado com sucesso.',
        ]);
    }

    /**
     * Show approval details
     */
    public function showApproval(CouncilApproval $approval): View
    {
        $approval->load(['requester', 'approver']);

        return view('churchcouncil::admin.approvals.show', compact('approval'));
    }

    // =================== ASSEMBLY RECOMMENDATIONS ===================

    /**
     * List council agendas that were approved by the council and require assembly vote.
     */
    public function assemblyRecommendations(Request $request): View
    {
        $query = CouncilAgenda::with('meeting')
            ->where('requires_assembly_vote', true)
            ->where(function ($q) {
                $q->whereNull('assembly_decision')
                    ->orWhere('assembly_decision', 'pending');
            });

        if ($request->filled('meeting_id')) {
            $query->where('meeting_id', $request->input('meeting_id'));
        }

        $agendas = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('churchcouncil::admin.assembly.index', compact('agendas'));
    }

    /**
     * Show form to record assembly decision for a given agenda.
     */
    public function editAssemblyAgenda(CouncilAgenda $agenda): View
    {
        if (! $agenda->requires_assembly_vote) {
            abort(404);
        }

        $agenda->load('meeting');

        return view('churchcouncil::admin.assembly.edit', compact('agenda'));
    }

    /**
     * Store assembly decision and aggregated vote counts.
     */
    public function storeAssemblyDecision(Request $request, CouncilAgenda $agenda): JsonResponse
    {
        if (! $agenda->requires_assembly_vote) {
            return response()->json([
                'success' => false,
                'message' => 'Esta pauta não está marcada para deliberação em assembleia.',
            ], 422);
        }

        $validated = $request->validate([
            'assembly_decision' => 'required|in:approved,rejected',
            'assembly_votes_for' => 'nullable|integer|min:0',
            'assembly_votes_against' => 'nullable|integer|min:0',
            'assembly_votes_abstain' => 'nullable|integer|min:0',
        ]);

        $agenda->update([
            'assembly_decision' => $validated['assembly_decision'],
            'assembly_decided_at' => now(),
            'assembly_votes_for' => $validated['assembly_votes_for'] ?? null,
            'assembly_votes_against' => $validated['assembly_votes_against'] ?? null,
            'assembly_votes_abstain' => $validated['assembly_votes_abstain'] ?? null,
        ]);

        // Optionally snapshot minutes as assembly-approved when there is an ata.
        $meeting = $agenda->meeting;
        if ($meeting && $meeting->minutes && auth()->user()) {
            $meeting->snapshotMinutesVersion(auth()->user(), $meeting->minutes, 'assembly_approved');
        }

        $this->audit->log('assembly_decision_recorded', $agenda, [
            'assembly_decision' => $agenda->assembly_decision,
            'votes_for' => $agenda->assembly_votes_for,
            'votes_against' => $agenda->assembly_votes_against,
            'votes_abstain' => $agenda->assembly_votes_abstain,
        ]);

        $councilUsers = CouncilMember::active()->with('user')->get()->pluck('user')->filter();
        if ($councilUsers->isNotEmpty()) {
            $this->inApp->sendToUsers(
                $councilUsers,
                'Decisão de assembleia registrada',
                "A assembleia decidiu sobre a pauta \"{$agenda->title}\".",
                [
                    'type' => 'info',
                    'priority' => 'normal',
                    'action_url' => route('admin.churchcouncil.assembly.index'),
                    'action_text' => 'Ver recomendações',
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Decisão da assembleia registrada com sucesso!',
            'redirect' => route('admin.churchcouncil.assembly.index'),
        ]);
    }

    /**
     * Approve request (council member or admin/pastor when allow_admin_approval is on).
     */
    public function approveRequest(Request $request, CouncilApproval $approval): JsonResponse
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        $councilMember = auth()->user()->councilMember;
        $allowAdminApproval = \Modules\ChurchCouncil\App\Services\ChurchCouncilSettings::allowAdminApproval();
        $isAdminOrPastor = auth()->user()->hasRole('admin') || auth()->user()->hasRole('pastor');

        if ($councilMember) {
            $approval->approve($councilMember, $validated['notes'] ?? null);
        } elseif ($allowAdminApproval && $isAdminOrPastor) {
            $approval->update([
                'status' => CouncilApproval::STATUS_APPROVED,
                'approved_by' => null,
                'approval_notes' => $validated['notes'] ?? null,
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => auth()->id()]),
            ]);
            $approval->runApprovalAction();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Você não é membro do conselho.',
            ], 403);
        }

        $this->audit->log('approval_approved', $approval, [
            'approval_type' => $approval->approval_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação aprovada com sucesso!',
        ]);
    }

    /**
     * Reject request (council member, or admin/pastor to dismiss wrongly-placed items).
     * Admin/pastor can always reject so they can clear requests that don't belong in the council queue.
     */
    public function rejectRequest(Request $request, CouncilApproval $approval): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        $councilMember = auth()->user()->councilMember;
        $allowAdminApproval = \Modules\ChurchCouncil\App\Services\ChurchCouncilSettings::allowAdminApproval();
        $isAdminOrPastor = auth()->user()->hasRole('admin') || auth()->user()->hasRole('pastor');

        if ($councilMember) {
            $approval->reject($councilMember, $validated['reason']);
        } elseif ($allowAdminApproval && $isAdminOrPastor) {
            $approval->update([
                'status' => CouncilApproval::STATUS_REJECTED,
                'approved_by' => null,
                'rejection_reason' => $validated['reason'],
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => auth()->id()]),
            ]);
        } elseif ($isAdminOrPastor) {
            // Admin/pastor can always reject (dismiss) to clear wrongly-placed requests from the queue
            $approval->update([
                'status' => CouncilApproval::STATUS_REJECTED,
                'approved_by' => null,
                'rejection_reason' => $validated['reason'],
                'reviewed_at' => now(),
                'metadata' => array_merge($approval->metadata ?? [], ['approved_by_user_id' => auth()->id(), 'dismissed_by_admin' => true]),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Você não é membro do conselho.',
            ], 403);
        }

        $this->audit->log('approval_rejected', $approval, [
            'approval_type' => $approval->approval_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação rejeitada.',
        ]);
    }

    // =================== SETTINGS ===================

    /**
     * Display council settings
     */
    public function settings(): View
    {
        $settings = \Modules\ChurchCouncil\App\Services\ChurchCouncilSettings::getAll();

        return view('churchcouncil::admin.settings.index', compact('settings'));
    }

    /**
     * Update council settings (persist via Settings model).
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'council_name' => 'nullable|string|max:255',
            'meeting_frequency' => 'nullable|in:weekly,biweekly,monthly,quarterly',
            'quorum_percentage' => 'nullable|integer|min:1|max:100',
            'voting_deadline_days' => 'nullable|integer|min:1|max:30',
            'auto_approve_budget_limit' => 'nullable|numeric|min:0',
            'approval_deadline_days' => 'nullable|integer|min:1|max:90',
            'enabled_approval_types' => 'nullable|array',
            'enabled_approval_types.*' => 'string|in:budget,project,personnel,policy,facility,other',
            'email_notifications' => 'nullable|boolean',
            'reminder_notifications' => 'nullable|boolean',
            'voting_reminders' => 'nullable|boolean',
            'allow_admin_approval' => 'nullable|boolean',
        ]);

        $store = \App\Models\Settings::class;
        $store::set('church_council_name', $request->input('council_name', 'Conselho da Igreja'), 'string', 'church_council');
        $store::set('church_council_meeting_frequency', $request->input('meeting_frequency', 'monthly'), 'string', 'church_council');
        $store::set('church_council_quorum_percentage', (int) $request->input('quorum_percentage', 50), 'integer', 'church_council');
        $store::set('church_council_voting_deadline_days', (int) $request->input('voting_deadline_days', 7), 'integer', 'church_council');
        $store::set('church_council_auto_approve_budget_limit', (float) $request->input('auto_approve_budget_limit', 1000), 'float', 'church_council');
        $store::set('church_council_approval_deadline_days', (int) $request->input('approval_deadline_days', 15), 'integer', 'church_council');
        $store::set('church_council_enabled_approval_types', json_encode($request->input('enabled_approval_types', ['budget', 'project', 'policy'])), 'string', 'church_council');
        $store::set('church_council_email_notifications', $request->boolean('email_notifications', true), 'boolean', 'church_council');
        $store::set('church_council_reminder_notifications', $request->boolean('reminder_notifications', true), 'boolean', 'church_council');
        $store::set('church_council_voting_reminders', $request->boolean('voting_reminders', true), 'boolean', 'church_council');
        $store::set('church_council_allow_admin_approval', $request->boolean('allow_admin_approval', false), 'boolean', 'church_council');

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Configurações atualizadas com sucesso!',
            ]);
        }

        return redirect()->route('admin.churchcouncil.settings.index')
            ->with('success', 'Configurações atualizadas com sucesso!');
    }
}
