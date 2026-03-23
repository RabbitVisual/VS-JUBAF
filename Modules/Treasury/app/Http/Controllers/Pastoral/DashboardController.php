<?php

namespace Modules\Treasury\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class DashboardController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    public function index()
    {
        $stats = $this->api->getDashboardStats(auth()->user());

        return view('treasury::pastoralpanel.dashboard', [
            'permission' => $stats['permission'],
            'monthlyIncome' => $stats['monthly_income'],
            'monthlyExpense' => $stats['monthly_expense'],
            'monthlyBalance' => $stats['monthly_balance'],
            'yearlyIncome' => $stats['yearly_income'],
            'yearlyExpense' => $stats['yearly_expense'],
            'yearlyBalance' => $stats['yearly_balance'],
            'incomeByCategory' => $stats['income_by_category'],
            'expenseByCategory' => $stats['expense_by_category'],
            'recentEntries' => $stats['recent_entries'],
            'activeCampaigns' => $stats['active_campaigns'],
            'monthlyIncomeChart' => $stats['monthly_income_chart'],
            'planoCooperativo' => $stats['plano_cooperativo'] ?? null,
        ]);
    }
}
