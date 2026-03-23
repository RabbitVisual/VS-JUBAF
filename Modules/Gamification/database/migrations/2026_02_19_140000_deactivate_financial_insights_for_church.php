<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * VertexCBAV é sistema de igreja. Desativa insights de contexto financeiro (Vertex Pro)
 * para que o bot Elias exiba apenas conteúdo edificante/gospel.
 */
return new class extends Migration
{
    /** Trigger events exclusivos do contexto financeiro (nunca usar no painel do membro igreja). */
    private const FINANCIAL_TRIGGERS = [
        'low_balance',
        'budget_reached',
        'savings_milestone',
        'daily_tip',
        'page_goals',
        'page_categories',
        'page_reports',
        'page_budgets',
        'page_income',
        'page_transactions',
        'page_tickets',
    ];

    public function up(): void
    {
        DB::table('insights_bank')
            ->whereIn('trigger_event', self::FINANCIAL_TRIGGERS)
            ->update(['is_active' => false]);

        DB::table('insights_bank')
            ->where('trigger_event', 'page_dashboard')
            ->where(function ($q) {
                $q->where('content', 'like', '%receitas%')
                    ->orWhere('content', 'like', '%despesas e saldo%')
                    ->orWhere('content', 'like', '%gastos que fogem%')
                    ->orWhere('content', 'like', '%indicador financeiro%')
                    ->orWhere('content', 'like', '%Revisar o dashboard semanalmente%');
            })
            ->update(['is_active' => false]);
    }

    public function down(): void
    {
        DB::table('insights_bank')
            ->whereIn('trigger_event', self::FINANCIAL_TRIGGERS)
            ->update(['is_active' => true]);

        DB::table('insights_bank')
            ->where('trigger_event', 'page_dashboard')
            ->where(function ($q) {
                $q->where('content', 'like', '%receitas%')
                    ->orWhere('content', 'like', '%despesas e saldo%')
                    ->orWhere('content', 'like', '%gastos que fogem%')
                    ->orWhere('content', 'like', '%indicador financeiro%')
                    ->orWhere('content', 'like', '%Revisar o dashboard semanalmente%');
            })
            ->update(['is_active' => true]);
    }
};
