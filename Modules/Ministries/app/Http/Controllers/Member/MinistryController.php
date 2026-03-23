<?php

namespace Modules\Ministries\App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\ChurchCouncil\App\Models\CouncilApproval;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryMember;
use Modules\Notifications\App\Services\InAppNotificationService;

class MinistryController extends Controller
{
    /**
     * Lista ministérios do membro e disponíveis para participação
     */
    public function index(): View
    {
        $user = Auth::user();
        $myMinistries = $user->activeMinistries()
            ->with(['leader', 'coLeader'])
            ->latest()
            ->get();

        $availableMinistries = Ministry::active()
            ->whereDoesntHave('members', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with(['leader', 'coLeader'])
            ->latest()
            ->get();

        return view('ministries::memberpanel.index', compact('myMinistries', 'availableMinistries'));
    }

    /**
     * Mostra detalhes do ministério
     */
    public function show(Ministry $ministry): View
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();
        $isMember = $ministry->hasMember($user);
        $isLeader = $ministry->isLeader($user);

        $ministry->load(['leader', 'coLeader', 'activeMembers', 'pendingMembers']);

        // Pendentes que ainda não foram aceitos pelo líder (sem CouncilApproval pendente para este ministério)
        $pendingForLeader = collect();
        if ($isLeader && $ministry->pendingMembers->isNotEmpty() && class_exists(CouncilApproval::class)) {
            $pendingCouncilUserIds = CouncilApproval::pending()
                ->where('approval_type', CouncilApproval::TYPE_MINISTRY_MEMBERSHIP)
                ->get()
                ->filter(fn ($a) => ($a->metadata['ministry_id'] ?? null) == $ministry->id)
                ->pluck('metadata')
                ->map(fn ($m) => $m['user_id'] ?? null)
                ->filter()
                ->unique()
                ->values()
                ->all();
            $pendingForLeader = $ministry->pendingMembers->whereNotIn('id', $pendingCouncilUserIds);
        }

        $memberInfo = null;
        if ($isMember) {
            $memberInfo = $ministry->members()
                ->where('user_id', $user->id)
                ->first()
                ->pivot;
        }

        $treasurySummary = null;
        if ($isLeader && class_exists(\Modules\Treasury\App\Services\TreasuryApiService::class)) {
            try {
                $start = now()->startOfMonth()->toDateString();
                $end = now()->endOfMonth()->toDateString();
                $treasurySummary = app(\Modules\Treasury\App\Services\TreasuryApiService::class)
                    ->getMinistrySummary($ministry->id, $start, $end, $user);
            } catch (\Throwable $e) {
                $treasurySummary = null;
            }
        }

        $ministryEvents = collect();
        if (class_exists(\Modules\Events\App\Models\Event::class)) {
            $ministryEvents = \Modules\Events\App\Models\Event::where('ministry_id', $ministry->id)
                ->where('end_date', '>=', now()->subDay())
                ->orderBy('start_date')
                ->limit(20)
                ->get();
        }

        $currentPlan = null;
        $currentMonthReport = null;
        if ($isLeader) {
            $currentPlan = \Modules\Ministries\App\Models\MinistryPlan::where('ministry_id', $ministry->id)
                ->whereIn('status', [\Modules\Ministries\App\Models\MinistryPlan::STATUS_APPROVED, \Modules\Ministries\App\Models\MinistryPlan::STATUS_IN_EXECUTION])
                ->orderBy('period_end', 'desc')
                ->first();
            $currentMonthReport = \Modules\Ministries\App\Models\MinistryReport::where('ministry_id', $ministry->id)
                ->where('report_year', now()->year)
                ->where('report_month', now()->month)
                ->first();
        }

        $recentReports = collect();
        if (class_exists(\Modules\Ministries\App\Models\MinistryReport::class)) {
            $recentReports = \Modules\Ministries\App\Models\MinistryReport::where('ministry_id', $ministry->id)
                ->orderByDesc('report_year')
                ->orderByDesc('report_month')
                ->limit(6)
                ->get();
        }

        return view('ministries::memberpanel.show', compact(
            'ministry',
            'isMember',
            'isLeader',
            'memberInfo',
            'treasurySummary',
            'ministryEvents',
            'currentPlan',
            'currentMonthReport',
            'recentReports',
            'pendingForLeader'
        ));
    }

    /**
     * Solicita participação no ministério
     */
    public function join(Request $request, Ministry $ministry): RedirectResponse
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();

        if (! $ministry->is_active) {
            return back()->with('error', 'Este ministério não está aceitando novos membros no momento.');
        }

        if ($ministry->hasMember($user)) {
            return back()->with('error', 'Você já é membro deste ministério.');
        }

        if (! $ministry->canAddMembers()) {
            return back()->with('error', 'Este ministério atingiu o limite de membros.');
        }

        $status = $ministry->requires_approval ? 'pending' : 'active';

        $ministry->members()->attach($user->id, [
            'role' => 'member',
            'status' => $status,
            'joined_at' => now(),
            'approved_at' => $status === 'active' ? now() : null,
        ]);

        if ($status === 'pending' && class_exists(InAppNotificationService::class)) {
            $ministry->load(['leader', 'coLeader']);
            $message = "{$user->name} solicitou filiação ao ministério \"{$ministry->name}\". Como líder, aceite para submeter ao conselho ou recuse em Ministérios.";
            $actionUrl = route('memberpanel.ministries.show', $ministry);
            foreach (array_filter([$ministry->leader_id, $ministry->co_leader_id]) as $leaderId) {
                if ($leaderId && $leaderId !== $user->id) {
                    $leader = User::find($leaderId);
                    if ($leader) {
                        app(InAppNotificationService::class)->sendToUser($leader, 'Solicitação de filiação', $message, [
                            'type' => 'info',
                            'action_url' => $actionUrl,
                            'action_text' => 'Ver solicitações',
                        ]);
                    }
                }
            }
        }

        $message = $status === 'pending'
            ? 'Solicitação enviada! O líder do ministério receberá um aviso e, ao aceitar, sua filiação será submetida ao conselho para aprovação.'
            : 'Você foi adicionado ao ministério com sucesso!';

        return back()->with('success', $message);
    }

    /**
     * Sai do ministério
     */
    public function leave(Ministry $ministry): RedirectResponse
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();

        if (! $ministry->hasMember($user)) {
            return back()->with('error', 'Você não é membro deste ministério.');
        }

        // Não permite que líderes saiam diretamente
        if ($ministry->isLeader($user)) {
            return back()->with('error', 'Líderes não podem sair do ministério. Entre em contato com o administrador.');
        }

        $ministry->members()->updateExistingPivot($user->id, [
            'status' => 'removed',
            'left_at' => now(),
        ]);

        return redirect()->route('memberpanel.ministries.index')
            ->with('success', 'Você saiu do ministério com sucesso.');
    }

    /**
     * Formulário para solicitar equipamentos (apenas líder/co-líder)
     */
    public function createReservation(Ministry $ministry): View|RedirectResponse
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();
        if (! $ministry->isLeader($user)) {
            return back()->with('error', 'Apenas líderes podem solicitar equipamentos.');
        }

        $assets = \Modules\Assets\App\Models\Asset::orderBy('name')->get(['id', 'name', 'code']);
        $events = [];
        if (class_exists(\Modules\Events\App\Models\Event::class)) {
            $events = \Modules\Events\App\Models\Event::where('ministry_id', $ministry->id)
                ->where('starts_at', '>', now())
                ->orderBy('starts_at')
                ->get(['id', 'title', 'starts_at']);
        }

        return view('ministries::memberpanel.reservations.create', compact('ministry', 'assets', 'events'));
    }

    /**
     * Cria reserva de equipamento (apenas líder/co-líder)
     */
    public function storeReservation(Request $request, Ministry $ministry): RedirectResponse
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();
        if (! $ministry->isLeader($user)) {
            return back()->with('error', 'Apenas líderes podem solicitar equipamentos.');
        }

        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'event_id' => 'nullable|exists:events,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (\Modules\Assets\App\Models\AssetReservation::hasCollision(
            (int) $validated['asset_id'],
            $validated['start_at'],
            $validated['end_at']
        )) {
            return back()
                ->withInput()
                ->with('error', 'Este equipamento já está reservado neste horário. Escolha outro horário ou recurso.');
        }

        \Modules\Assets\App\Models\AssetReservation::create([
            'asset_id' => $validated['asset_id'],
            'ministry_id' => $ministry->id,
            'event_id' => $validated['event_id'] ?? null,
            'requested_by' => $user->id,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'status' => \Modules\Assets\App\Models\AssetReservation::STATUS_REQUESTED,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('memberpanel.ministries.show', $ministry)
            ->with('success', 'Solicitação de reserva enviada. Aguarde aprovação.');
    }

    /**
     * Formulário de relatório mensal (líder/co-líder).
     */
    public function createReport(Ministry $ministry): View|RedirectResponse
    {
        $this->authorize('view', $ministry);
        if (! $ministry->isLeader(Auth::user())) {
            return back()->with('error', 'Apenas líderes podem enviar relatórios.');
        }
        $year = (int) request('year', now()->year);
        $month = (int) request('month', now()->month);
        $existing = \Modules\Ministries\App\Models\MinistryReport::where('ministry_id', $ministry->id)
            ->where('report_year', $year)->where('report_month', $month)->first();
        if ($existing) {
            return redirect()->route('memberpanel.ministries.reports.edit', [$ministry, $existing]);
        }
        return view('ministries::memberpanel.reports.form', compact('ministry', 'year', 'month'));
    }

    /**
     * Salvar novo relatório mensal.
     */
    public function storeReport(Request $request, Ministry $ministry): RedirectResponse
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();
        if (! $ministry->isLeader($user)) {
            return back()->with('error', 'Apenas líderes podem enviar relatórios.');
        }
        $validated = $request->validate([
            'report_year' => 'required|integer|min:2020|max:2030',
            'report_month' => 'required|integer|min:1|max:12',
            'qualitative_summary' => 'nullable|string|max:5000',
            'prayer_requests' => 'nullable|string|max:2000',
            'highlights' => 'nullable|string|max:2000',
            'challenges' => 'nullable|string|max:2000',
        ]);
        $validated['ministry_id'] = $ministry->id;
        $validated['period_start'] = \Carbon\Carbon::createFromDate($validated['report_year'], $validated['report_month'], 1)->startOfMonth();
        $validated['period_end'] = \Carbon\Carbon::createFromDate($validated['report_year'], $validated['report_month'], 1)->endOfMonth();
        $validated['quantitative_data'] = $request->input('quantitative_data', []);
        $validated['status'] = $request->boolean('submit') ? \Modules\Ministries\App\Models\MinistryReport::STATUS_SUBMITTED : \Modules\Ministries\App\Models\MinistryReport::STATUS_DRAFT;
        if ($validated['status'] === \Modules\Ministries\App\Models\MinistryReport::STATUS_SUBMITTED) {
            $validated['submitted_at'] = now();
            $validated['submitted_by'] = $user->id;
        }
        \Modules\Ministries\App\Models\MinistryReport::create($validated);
        $msg = $validated['status'] === 'submitted' ? 'Relatório enviado com sucesso.' : 'Rascunho do relatório salvo.';
        return redirect()->route('memberpanel.ministries.show', $ministry)->with('success', $msg);
    }

    /**
     * Editar relatório mensal.
     */
    public function editReport(Ministry $ministry, \Modules\Ministries\App\Models\MinistryReport $report): View|RedirectResponse
    {
        $this->authorize('view', $ministry);
        if ($report->ministry_id !== $ministry->id || ! $ministry->isLeader(Auth::user())) {
            abort(404);
        }
        if ($report->status === \Modules\Ministries\App\Models\MinistryReport::STATUS_SUBMITTED) {
            return back()->with('error', 'Relatório já enviado; não pode ser editado.');
        }
        $year = $report->report_year;
        $month = $report->report_month;
        return view('ministries::memberpanel.reports.form', compact('ministry', 'report', 'year', 'month'));
    }

    /**
     * Atualizar relatório.
     */
    public function updateReport(Request $request, Ministry $ministry, \Modules\Ministries\App\Models\MinistryReport $report): RedirectResponse
    {
        $this->authorize('view', $ministry);
        $user = Auth::user();
        if ($report->ministry_id !== $ministry->id || ! $ministry->isLeader($user)) {
            abort(404);
        }
        if ($report->status === \Modules\Ministries\App\Models\MinistryReport::STATUS_SUBMITTED) {
            return back()->with('error', 'Relatório já enviado.');
        }
        $validated = $request->validate([
            'qualitative_summary' => 'nullable|string|max:5000',
            'prayer_requests' => 'nullable|string|max:2000',
            'highlights' => 'nullable|string|max:2000',
            'challenges' => 'nullable|string|max:2000',
        ]);
        $validated['quantitative_data'] = $request->input('quantitative_data', []);
        $validated['status'] = $request->boolean('submit') ? \Modules\Ministries\App\Models\MinistryReport::STATUS_SUBMITTED : \Modules\Ministries\App\Models\MinistryReport::STATUS_DRAFT;
        if ($validated['status'] === \Modules\Ministries\App\Models\MinistryReport::STATUS_SUBMITTED) {
            $validated['submitted_at'] = now();
            $validated['submitted_by'] = $user->id;
        }
        $report->update($validated);
        $msg = $validated['status'] === 'submitted' ? 'Relatório enviado com sucesso.' : 'Rascunho atualizado.';
        return redirect()->route('memberpanel.ministries.show', $ministry)->with('success', $msg);
    }

    /**
     * Aceita a solicitação de filiação e submete ao conselho (apenas líder/co-líder).
     */
    public function acceptAndSubmitToCouncil(Ministry $ministry, User $user): RedirectResponse
    {
        $this->authorize('view', $ministry);
        if (! $ministry->isLeader(Auth::user())) {
            return back()->with('error', 'Apenas o líder ou co-líder pode aceitar solicitações.');
        }

        $pivot = $ministry->members()->where('user_id', $user->id)->wherePivot('status', 'pending')->first();
        if (! $pivot) {
            return back()->with('error', 'Solicitação não encontrada ou já processada.');
        }

        if (class_exists(CouncilApproval::class)) {
            CouncilApproval::create([
                'approvable_type' => Ministry::class,
                'approvable_id' => $ministry->id,
                'approval_type' => CouncilApproval::TYPE_MINISTRY_MEMBERSHIP,
                'status' => CouncilApproval::STATUS_PENDING,
                'request_details' => "Solicitação de filiação ao ministério \"{$ministry->name}\" por {$user->name}. Aceita pelo líder para submissão ao conselho.",
                'requested_by' => $user->id,
                'submitted_at' => now(),
                'metadata' => [
                    'user_id' => $user->id,
                    'ministry_id' => $ministry->id,
                    'approved_by_leader_id' => Auth::id(),
                ],
            ]);
        }

        if (class_exists(InAppNotificationService::class)) {
            app(InAppNotificationService::class)->sendToUser($user, 'Solicitação aceita pelo líder', "Sua filiação ao ministério \"{$ministry->name}\" foi aceita pelo líder e enviada ao conselho para aprovação final.", [
                'type' => 'info',
                'action_url' => route('memberpanel.ministries.show', $ministry),
                'action_text' => 'Ver ministério',
            ]);
        }

        return back()->with('success', "Solicitação de {$user->name} aceita e enviada ao conselho para aprovação.");
    }

    /**
     * Recusa a solicitação de filiação (apenas líder/co-líder). Remove o vínculo pendente.
     */
    public function rejectRequest(Ministry $ministry, User $user): RedirectResponse
    {
        $this->authorize('view', $ministry);
        if (! $ministry->isLeader(Auth::user())) {
            return back()->with('error', 'Apenas o líder ou co-líder pode recusar solicitações.');
        }

        $pivot = $ministry->members()->where('user_id', $user->id)->wherePivot('status', 'pending')->first();
        if (! $pivot) {
            return back()->with('error', 'Solicitação não encontrada ou já processada.');
        }

        $ministry->members()->updateExistingPivot($user->id, [
            'status' => 'removed',
            'left_at' => now(),
        ]);

        if (class_exists(InAppNotificationService::class)) {
            app(InAppNotificationService::class)->sendToUser($user, 'Solicitação de filiação recusada', "Sua solicitação de filiação ao ministério \"{$ministry->name}\" foi recusada pela liderança.", [
                'type' => 'warning',
            ]);
        }

        return back()->with('success', "Solicitação de {$user->name} recusada.");
    }
}
