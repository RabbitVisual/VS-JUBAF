<?php

namespace Modules\Treasury\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Treasury\App\Models\Campaign;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\FinancialGoal;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Models\TreasuryMonthlyClosing;
use Modules\Treasury\App\Services\TreasuryApiService;

/**
 * API v1 central de Treasury. Respostas no padrão { data }.
 * Controle fiscal, entradas, campanhas, metas, relatórios e permissões.
 */
class TreasuryController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    /**
     * GET /api/v1/treasury/dashboard
     */
    public function dashboard(): JsonResponse
    {
        $stats = $this->api->getDashboardStats(auth()->user());
        $data = [
            'monthly_income' => $stats['monthly_income'],
            'monthly_expense' => $stats['monthly_expense'],
            'monthly_balance' => $stats['monthly_balance'],
            'yearly_income' => $stats['yearly_income'],
            'yearly_expense' => $stats['yearly_expense'],
            'yearly_balance' => $stats['yearly_balance'],
            'income_by_category' => $stats['income_by_category'],
            'expense_by_category' => $stats['expense_by_category'],
            'recent_entries' => $stats['recent_entries'],
            'active_campaigns' => $stats['active_campaigns'],
            'active_goals' => $stats['active_goals'],
            'monthly_income_chart' => $stats['monthly_income_chart'],
        ];
        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/treasury/entries
     */
    public function entries(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'category', 'start_date', 'end_date', 'campaign_id', 'ministry_id']);
        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);
        $paginator = $this->api->listEntries($filters, $perPage);
        return response()->json(['data' => $paginator->items(), 'meta' => ['current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(), 'per_page' => $paginator->perPage(), 'total' => $paginator->total()]]);
    }

    /**
     * GET /api/v1/treasury/entries/{id}
     */
    public function entry(int $id): JsonResponse
    {
        $entry = $this->api->getEntry($id);
        return response()->json(['data' => $entry]);
    }

    /**
     * POST /api/v1/treasury/entries
     */
    public function storeEntry(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'payment_id' => 'nullable|exists:payments,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'goal_id' => 'nullable|exists:financial_goals,id',
            'ministry_id' => 'nullable|exists:ministries,id',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);
        $entry = $this->api->createEntry($validated, auth()->user());
        return response()->json(['data' => $entry], 201);
    }

    /**
     * PUT /api/v1/treasury/entries/{id}
     */
    public function updateEntry(Request $request, int $id): JsonResponse
    {
        $entry = FinancialEntry::findOrFail($id);
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'payment_id' => 'nullable|exists:payments,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'goal_id' => 'nullable|exists:financial_goals,id',
            'ministry_id' => 'nullable|exists:ministries,id',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);
        $entry = $this->api->updateEntry($entry, $validated, auth()->user());
        return response()->json(['data' => $entry]);
    }

    /**
     * DELETE /api/v1/treasury/entries/{id}
     */
    public function destroyEntry(int $id): JsonResponse
    {
        $entry = FinancialEntry::findOrFail($id);
        $this->api->deleteEntry($entry, auth()->user());
        return response()->json(['data' => ['message' => 'Entrada removida.']]);
    }

    /**
     * POST /api/v1/treasury/entries/import-payment/{paymentId}
     */
    public function importPayment(int $paymentId): JsonResponse
    {
        $payment = Payment::findOrFail($paymentId);
        $entry = $this->api->importPayment($payment, auth()->user());
        return response()->json(['data' => $entry], 201);
    }

    /**
     * GET /api/v1/treasury/campaigns
     */
    public function campaigns(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);
        $paginator = $this->api->listCampaigns($perPage);
        return response()->json(['data' => $paginator->items(), 'meta' => ['current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]]);
    }

    /**
     * GET /api/v1/treasury/campaigns/{id}
     */
    public function campaign(int $id): JsonResponse
    {
        $campaign = $this->api->getCampaign($id);
        return response()->json(['data' => $campaign]);
    }

    /**
     * POST /api/v1/treasury/campaigns
     */
    public function storeCampaign(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:campaigns,slug',
            'description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        $campaign = $this->api->createCampaign($validated, auth()->user());
        return response()->json(['data' => $campaign], 201);
    }

    /**
     * PUT /api/v1/treasury/campaigns/{id}
     */
    public function updateCampaign(Request $request, int $id): JsonResponse
    {
        $campaign = Campaign::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:campaigns,slug,' . $id,
            'description' => 'nullable|string',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);
        $campaign = $this->api->updateCampaign($campaign, $validated, auth()->user());
        return response()->json(['data' => $campaign]);
    }

    /**
     * DELETE /api/v1/treasury/campaigns/{id}
     */
    public function destroyCampaign(int $id): JsonResponse
    {
        $campaign = Campaign::findOrFail($id);
        $this->api->deleteCampaign($campaign, auth()->user());
        return response()->json(['data' => ['message' => 'Campanha removida.']]);
    }

    /**
     * GET /api/v1/treasury/goals
     */
    public function goals(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);
        $paginator = $this->api->listGoals($perPage);
        return response()->json(['data' => $paginator->items(), 'meta' => ['current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]]);
    }

    /**
     * GET /api/v1/treasury/goals/{id}
     */
    public function goal(int $id): JsonResponse
    {
        $goal = $this->api->getGoal($id);
        return response()->json(['data' => $goal]);
    }

    /**
     * GET /api/v1/treasury/closings
     * Lista fechamentos mensais (opcionalmente filtrando por ano).
     */
    public function closings(Request $request): JsonResponse
    {
        $user = auth()->user();
        $permission = TreasuryPermission::forUserOrAdmin($user);
        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar fechamentos.');
        }

        $year = (int) ($request->query('year', now()->year));

        $closings = TreasuryMonthlyClosing::where('year', $year)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return response()->json(['data' => $closings]);
    }

    /**
     * POST /api/v1/treasury/closings/{id}/approve-for-assembly
     * Marca um fechamento mensal como pronto para assembleia.
     */
    public function approveClosingForAssembly(Request $request, int $id): JsonResponse
    {
        $closing = TreasuryMonthlyClosing::findOrFail($id);
        $notes = $request->input('notes');
        $closing = $this->api->markClosingReadyForAssembly($closing, auth()->user(), $notes);

        return response()->json(['data' => $closing]);
    }

    /**
     * POST /api/v1/treasury/goals
     */
    public function storeGoal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:monthly,yearly,campaign,custom',
            'target_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_active' => 'boolean',
        ]);
        $goal = $this->api->createGoal($validated, auth()->user());
        return response()->json(['data' => $goal], 201);
    }

    /**
     * PUT /api/v1/treasury/goals/{id}
     */
    public function updateGoal(Request $request, int $id): JsonResponse
    {
        $goal = FinancialGoal::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'type' => 'required|in:monthly,yearly,campaign,custom',
            'target_amount' => 'required|numeric|min:0.01',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'is_active' => 'boolean',
        ]);
        $goal = $this->api->updateGoal($goal, $validated, auth()->user());
        return response()->json(['data' => $goal]);
    }

    /**
     * DELETE /api/v1/treasury/goals/{id}
     */
    public function destroyGoal(int $id): JsonResponse
    {
        $goal = FinancialGoal::findOrFail($id);
        $this->api->deleteGoal($goal, auth()->user());
        return response()->json(['data' => ['message' => 'Meta removida.']]);
    }

    /**
     * GET /api/v1/treasury/reports
     */
    public function reports(Request $request): JsonResponse
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->query('end_date', now()->endOfMonth()->toDateString());
        $data = $this->api->getReportAggregates($startDate, $endDate, auth()->user());
        return response()->json(['data' => $data]);
    }

    /**
     * GET /api/v1/treasury/permissions
     */
    public function permissions(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);
        $paginator = $this->api->listPermissions($perPage);
        return response()->json(['data' => $paginator->items(), 'meta' => ['current_page' => $paginator->currentPage(), 'last_page' => $paginator->lastPage(), 'total' => $paginator->total()]]);
    }

    /**
     * GET /api/v1/treasury/entry-form-options (campaigns, goals, ministries, payments for import)
     */
    public function entryFormOptions(): JsonResponse
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();
        if (! $permission->canCreateEntries()) {
            abort(403, 'Sem permissão.');
        }
        $options = $this->api->getEntryFormOptions();
        return response()->json(['data' => $options]);
    }
}
