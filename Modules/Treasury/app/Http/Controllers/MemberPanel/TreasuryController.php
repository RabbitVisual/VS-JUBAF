<?php

namespace Modules\Treasury\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Treasury\App\Http\Controllers\Admin\CampaignController as TreasuryCampaignController;
use Modules\Treasury\App\Http\Controllers\Admin\DashboardController as TreasuryDashboardController;
use Modules\Treasury\App\Http\Controllers\Admin\FinancialEntryController as TreasuryFinancialEntryController;
use Modules\Treasury\App\Http\Controllers\Admin\FinancialGoalController as TreasuryFinancialGoalController;
use Modules\Treasury\App\Http\Controllers\Admin\ReportController as TreasuryReportController;
use Modules\Treasury\App\Http\Controllers\Admin\TreasuryPermissionController as TreasuryTreasuryPermissionController;
use Modules\Treasury\App\Models\Campaign;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\FinancialGoal;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class TreasuryController extends Controller
{
    protected $treasuryDashboardController;

    protected $treasuryFinancialEntryController;

    protected $treasuryCampaignController;

    protected $treasuryFinancialGoalController;

    protected $treasuryTreasuryPermissionController;

    protected $treasuryReportController;

    protected TreasuryApiService $treasuryApiService;

    public function __construct(TreasuryApiService $treasuryApiService)
    {
        $this->treasuryApiService = $treasuryApiService;
        $this->treasuryDashboardController = app(TreasuryDashboardController::class);
        $this->treasuryFinancialEntryController = app(TreasuryFinancialEntryController::class);
        $this->treasuryCampaignController = app(TreasuryCampaignController::class);
        $this->treasuryFinancialGoalController = app(TreasuryFinancialGoalController::class);
        $this->treasuryTreasuryPermissionController = app(TreasuryTreasuryPermissionController::class);
        $this->treasuryReportController = app(TreasuryReportController::class);
    }

    /**
     * Proxy para dashboard do Treasury
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $permission = TreasuryPermission::where('user_id', $user->id)->first();

        if (! $permission) {
            abort(403, 'Você não tem permissão para acessar a Tesouraria.');
        }

        // Dashboard é permitido a qualquer membro com permissão de tesouraria (mesmo só "ver dashboard").

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyIncome = FinancialEntry::income()
            ->month($currentYear, $currentMonth)
            ->sum('amount');

        $monthlyExpense = FinancialEntry::expense()
            ->month($currentYear, $currentMonth)
            ->sum('amount');

        $monthlyBalance = $monthlyIncome - $monthlyExpense;

        $yearlyIncome = FinancialEntry::income()
            ->year($currentYear)
            ->sum('amount');

        $yearlyExpense = FinancialEntry::expense()
            ->year($currentYear)
            ->sum('amount');

        $yearlyBalance = $yearlyIncome - $yearlyExpense;

        $incomeByCategory = FinancialEntry::income()
            ->month($currentYear, $currentMonth)
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $expenseByCategory = FinancialEntry::expense()
            ->month($currentYear, $currentMonth)
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $recentEntries = FinancialEntry::with(['user', 'campaign', 'ministry'])
            ->orderBy('entry_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeCampaigns = Campaign::active()
            ->orderBy('end_date', 'asc')
            ->get();

        $activeGoals = FinancialGoal::active()
            ->orderBy('end_date', 'asc')
            ->get();

        $monthlyIncomeChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyIncomeChart[] = [
                'month' => $date->format('M/Y'),
                'income' => FinancialEntry::income()
                    ->month($date->year, $date->month)
                    ->sum('amount'),
                'expense' => FinancialEntry::expense()
                    ->month($date->year, $date->month)
                    ->sum('amount'),
            ];
        }

        return view('treasury::memberpanel.dashboard', compact(
            'permission',
            'monthlyIncome',
            'monthlyExpense',
            'monthlyBalance',
            'yearlyIncome',
            'yearlyExpense',
            'yearlyBalance',
            'incomeByCategory',
            'expenseByCategory',
            'recentEntries',
            'activeCampaigns',
            'activeGoals',
            'monthlyIncomeChart'
        ));
    }

    /**
     * Portal de Transparência – dados agregados para prestação de contas aos membros.
     */
    public function transparency(Request $request)
    {
        $user = auth()->user();
        $permission = TreasuryPermission::where('user_id', $user->id)->first();

        if (! $permission) {
            abort(403, 'Você não tem permissão para acessar a Tesouraria.');
        }

        $year = $request->get('ano', (string) now()->year);
        $summary = $this->treasuryApiService->getTransparencySummary($year);

        return view('treasury::memberpanel.transparency', $summary);
    }

    /**
     * Proxy para entries do Treasury
     */
    public function entriesIndex(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar entradas financeiras.');
        }

        $response = $this->treasuryFinancialEntryController->index($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.entries.index', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para criar entrada financeira
     */
    public function entriesCreate(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canCreateEntries()) {
            abort(403, 'Você não tem permissão para criar entradas financeiras.');
        }

        $response = $this->treasuryFinancialEntryController->create();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.entries.create', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para salvar entrada financeira
     */
    public function entriesStore(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canCreateEntries()) {
            abort(403, 'Você não tem permissão para criar entradas financeiras.');
        }

        return $this->treasuryFinancialEntryController->store($request);
    }

    /**
     * Proxy para editar entrada financeira
     */
    public function entriesEdit(Request $request, FinancialEntry $entry)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canEditEntries()) {
            abort(403, 'Você não tem permissão para editar entradas financeiras.');
        }

        $response = $this->treasuryFinancialEntryController->edit($entry);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.entries.edit', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para atualizar entrada financeira
     */
    public function entriesUpdate(Request $request, FinancialEntry $entry)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canEditEntries()) {
            abort(403, 'Você não tem permissão para editar entradas financeiras.');
        }

        return $this->treasuryFinancialEntryController->update($request, $entry);
    }

    /**
     * Proxy para deletar entrada financeira
     */
    public function entriesDestroy(Request $request, FinancialEntry $entry)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canDeleteEntries()) {
            abort(403, 'Você não tem permissão para deletar entradas financeiras.');
        }

        return $this->treasuryFinancialEntryController->destroy($entry);
    }

    /**
     * Proxy para campaigns do Treasury
     */
    public function campaignsIndex(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar campanhas.');
        }

        $response = $this->treasuryCampaignController->index();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.campaigns.index', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para criar campanha
     */
    public function campaignsCreate(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageCampaigns()) {
            abort(403, 'Você não tem permissão para gerenciar campanhas.');
        }

        $response = $this->treasuryCampaignController->create();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.campaigns.create', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para visualizar campanha
     */
    public function campaignsShow(Request $request, Campaign $campaign)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar campanhas.');
        }

        $response = $this->treasuryCampaignController->show($campaign);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.campaigns.show', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para salvar campanha
     */
    public function campaignsStore(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageCampaigns()) {
            abort(403, 'Você não tem permissão para gerenciar campanhas.');
        }

        return $this->treasuryCampaignController->store($request);
    }

    /**
     * Proxy para editar campanha
     */
    public function campaignsEdit(Request $request, Campaign $campaign)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageCampaigns()) {
            abort(403, 'Você não tem permissão para gerenciar campanhas.');
        }

        $response = $this->treasuryCampaignController->edit($campaign);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.campaigns.edit', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para atualizar campanha
     */
    public function campaignsUpdate(Request $request, Campaign $campaign)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageCampaigns()) {
            abort(403, 'Você não tem permissão para gerenciar campanhas.');
        }

        return $this->treasuryCampaignController->update($request, $campaign);
    }

    /**
     * Proxy para deletar campanha
     */
    public function campaignsDestroy(Request $request, Campaign $campaign)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageCampaigns()) {
            abort(403, 'Você não tem permissão para gerenciar campanhas.');
        }

        return $this->treasuryCampaignController->destroy($campaign);
    }

    /**
     * Proxy para goals do Treasury
     */
    public function goalsIndex(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar metas financeiras.');
        }

        $response = $this->treasuryFinancialGoalController->index();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.goals.index', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para criar meta
     */
    public function goalsCreate(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageGoals()) {
            abort(403, 'Você não tem permissão para gerenciar metas.');
        }

        $response = $this->treasuryFinancialGoalController->create();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.goals.create', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para visualizar meta
     */
    public function goalsShow(Request $request, FinancialGoal $goal)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar metas.');
        }

        $response = $this->treasuryFinancialGoalController->show($goal);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.goals.show', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para salvar meta
     */
    public function goalsStore(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageGoals()) {
            abort(403, 'Você não tem permissão para gerenciar metas.');
        }

        return $this->treasuryFinancialGoalController->store($request);
    }

    /**
     * Proxy para editar meta
     */
    public function goalsEdit(Request $request, FinancialGoal $goal)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageGoals()) {
            abort(403, 'Você não tem permissão para gerenciar metas.');
        }

        $response = $this->treasuryFinancialGoalController->edit($goal);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.goals.edit', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para atualizar meta
     */
    public function goalsUpdate(Request $request, FinancialGoal $goal)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageGoals()) {
            abort(403, 'Você não tem permissão para gerenciar metas.');
        }

        return $this->treasuryFinancialGoalController->update($request, $goal);
    }

    /**
     * Proxy para deletar meta
     */
    public function goalsDestroy(Request $request, FinancialGoal $goal)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canManageGoals()) {
            abort(403, 'Você não tem permissão para gerenciar metas.');
        }

        return $this->treasuryFinancialGoalController->destroy($goal);
    }

    /**
     * Proxy para reports do Treasury
     */
    public function reportsIndex(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canViewReports()) {
            abort(403, 'Você não tem permissão para visualizar relatórios.');
        }

        $response = $this->treasuryReportController->index($request);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.reports.index', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para exportar relatório para PDF
     */
    public function reportsExportPdf(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        return $this->treasuryReportController->exportPdf($request);
    }

    /**
     * Proxy para exportar relatório para Excel
     */
    public function reportsExportExcel(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        return $this->treasuryReportController->exportExcel($request);
    }

    /**
     * Proxy para exportar relatório (genérico)
     */
    public function reportsExport(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->canExportData()) {
            abort(403, 'Você não tem permissão para exportar dados.');
        }

        return $this->treasuryReportController->export($request);
    }

    /**
     * Proxy para permissions do Treasury (apenas admin)
     */
    public function permissionsIndex(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->isAdmin()) {
            abort(403, 'Você não tem permissão para gerenciar permissões.');
        }

        $response = $this->treasuryTreasuryPermissionController->index();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.permissions.index', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para criar permissão
     */
    public function permissionsCreate(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->isAdmin()) {
            abort(403, 'Você não tem permissão para gerenciar permissões.');
        }

        $response = $this->treasuryTreasuryPermissionController->create();

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.permissions.create', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para salvar permissão
     */
    public function permissionsStore(Request $request)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->isAdmin()) {
            abort(403, 'Você não tem permissão para gerenciar permissões.');
        }

        return $this->treasuryTreasuryPermissionController->store($request);
    }

    /**
     * Proxy para editar permissão
     */
    public function permissionsEdit(Request $request, \Modules\Treasury\App\Models\TreasuryPermission $treasuryPermission)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->isAdmin()) {
            abort(403, 'Você não tem permissão para gerenciar permissões.');
        }

        $response = $this->treasuryTreasuryPermissionController->edit($treasuryPermission);

        if ($response instanceof \Illuminate\View\View) {
            return view('treasury::memberpanel.permissions.edit', $response->getData());
        }

        return $response;
    }

    /**
     * Proxy para atualizar permissão
     */
    public function permissionsUpdate(Request $request, \Modules\Treasury\App\Models\TreasuryPermission $treasuryPermission)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->isAdmin()) {
            abort(403, 'Você não tem permissão para gerenciar permissões.');
        }

        return $this->treasuryTreasuryPermissionController->update($request, $treasuryPermission);
    }

    /**
     * Proxy para deletar permissão
     */
    public function permissionsDestroy(Request $request, \Modules\Treasury\App\Models\TreasuryPermission $treasuryPermission)
    {
        $permission = TreasuryPermission::where('user_id', auth()->id())->firstOrFail();

        if (! $permission->isAdmin()) {
            abort(403, 'Você não tem permissão para gerenciar permissões.');
        }

        return $this->treasuryTreasuryPermissionController->destroy($treasuryPermission);
    }

    /**
     * Proxy genérico para métodos POST/PUT/DELETE
     */
    public function proxy(Request $request, $controller, $method, ...$params)
    {
        $user = auth()->user();
        $permission = TreasuryPermission::where('user_id', $user->id)->first();

        if (! $permission) {
            abort(403, 'Você não tem permissão para acessar a Tesouraria.');
        }

        // Mapeamento de métodos e permissões requeridas para segurança
        $methodPermissions = [
            'entries' => [
                'store' => 'canCreateEntries',
                'update' => 'canEditEntries',
                'destroy' => 'canDeleteEntries',
                'importPayment' => 'canCreateEntries',
            ],
            'campaigns' => [
                'store' => 'canManageCampaigns',
                'update' => 'canManageCampaigns',
                'destroy' => 'canManageCampaigns',
            ],
            'goals' => [
                'store' => 'canManageGoals',
                'update' => 'canManageGoals',
                'destroy' => 'canManageGoals',
            ],
            'permissions' => [
                'store' => 'isAdmin',
                'update' => 'isAdmin',
                'destroy' => 'isAdmin',
            ],
            'reports' => [
                'exportExcel' => 'canExportData',
                'exportPdf' => 'canExportData',
                'export' => 'canExportData',
            ],
        ];

        if (! isset($methodPermissions[$controller]) || ! isset($methodPermissions[$controller][$method])) {
            \Log::warning('Tentativa de acesso a método proxy não mapeado ou não autorizado', [
                'user_id' => $user->id,
                'controller' => $controller,
                'method' => $method,
            ]);
            abort(403, 'Ação não permitida via proxy de segurança.');
        }

        $requiredPermission = $methodPermissions[$controller][$method];

        if ($requiredPermission === 'isAdmin') {
            if (! $permission->isAdmin()) {
                abort(403, 'Apenas administradores podem realizar esta ação.');
            }
        } else {
            if (method_exists($permission, $requiredPermission) && ! $permission->$requiredPermission()) {
                abort(403, 'Você não tem permissão para realizar esta ação.');
            }
        }

        $controllerMap = [
            'entries' => $this->treasuryFinancialEntryController,
            'campaigns' => $this->treasuryCampaignController,
            'goals' => $this->treasuryFinancialGoalController,
            'permissions' => $this->treasuryTreasuryPermissionController,
            'reports' => $this->treasuryReportController,
        ];

        $targetController = $controllerMap[$controller];

        if (! method_exists($targetController, $method)) {
            abort(404);
        }

        return app()->call([$targetController, $method], array_merge($params, [$request]));
    }
}
