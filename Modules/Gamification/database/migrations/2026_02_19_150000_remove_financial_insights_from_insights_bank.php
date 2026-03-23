<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * VertexCBAV é sistema de igreja. Remove definitivamente insights de contexto financeiro (Vertex Pro)
 * da base de dados; não devem existir nem no projeto nem na DB.
 */
return new class extends Migration
{
    private const FINANCIAL_TRIGGERS = [
        'low_balance', 'budget_reached', 'savings_milestone', 'daily_tip',
        'page_goals', 'page_categories', 'page_reports', 'page_budgets',
        'page_income', 'page_transactions', 'page_tickets',
    ];

    public function up(): void
    {
        DB::table('insights_bank')->whereIn('trigger_event', self::FINANCIAL_TRIGGERS)->delete();

        DB::table('insights_bank')
            ->where('trigger_event', 'page_dashboard')
            ->where(function ($q) {
                $q->where('content', 'like', '%receitas%')
                    ->orWhere('content', 'like', '%despesas e saldo%')
                    ->orWhere('content', 'like', '%gastos que fogem%')
                    ->orWhere('content', 'like', '%indicador financeiro%')
                    ->orWhere('content', 'like', '%Revisar o dashboard semanalmente%');
            })
            ->delete();
    }

    public function down(): void
    {
        // Não restaura; os seeders financeiros foram removidos do projeto.
    }
};
