<?php

namespace Modules\PastoralPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Treasury\App\Services\TreasuryApiService;

class TransparenciaController extends Controller
{
    /**
     * Saúde financeira e balancetes em modo somente leitura (layout pastoral).
     */
    public function index()
    {
        if (! class_exists(TreasuryApiService::class)) {
            return view('pastoralpanel::transparencia.index', [
                'monthlyIncome' => 0,
                'monthlyExpense' => 0,
                'monthlyBalance' => 0,
                'yearlyIncome' => 0,
                'yearlyExpense' => 0,
                'yearlyBalance' => 0,
                'incomeByCategory' => collect(),
                'expenseByCategory' => collect(),
                'recentEntries' => collect(),
                'activeCampaigns' => collect(),
                'monthlyIncomeChart' => [],
                'planoCooperativo' => null,
                'readOnly' => true,
            ]);
        }

        try {
            $stats = app(TreasuryApiService::class)->getDashboardStats(auth()->user());
        } catch (\Throwable $e) {
            return view('pastoralpanel::transparencia.index', [
                'monthlyIncome' => 0,
                'monthlyExpense' => 0,
                'monthlyBalance' => 0,
                'yearlyIncome' => 0,
                'yearlyExpense' => 0,
                'yearlyBalance' => 0,
                'incomeByCategory' => collect(),
                'expenseByCategory' => collect(),
                'recentEntries' => collect(),
                'activeCampaigns' => collect(),
                'monthlyIncomeChart' => [],
                'planoCooperativo' => null,
                'readOnly' => true,
            ]);
        }

        return view('pastoralpanel::transparencia.index', [
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
            'readOnly' => true,
        ]);
    }
}
