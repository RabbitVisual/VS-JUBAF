<?php

declare(strict_types=1);

namespace Modules\Gamification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Gamification\Models\CoachingRule;
use Modules\Gamification\Models\Medal;

class CoachingRulesSeeder extends Seeder
{
    public function run(): void
    {
        $medalKeys = [
            'rule_first_steps', 'rule_reserve_1', 'rule_reserve_3', 'rule_reserve_solid',
            'rule_consistency_bonus', 'rule_consistency_10', 'rule_503020_essential', 'rule_503020_balanced',
            'rule_platinum_pro', 'rule_reserve_12', 'rule_consistency_30', 'rule_savings_20',
        ];
        $medals = [];
        foreach ($medalKeys as $key) {
            $medals[$key] = Medal::where('trigger_key', $key)->first();
        }

        $rules = [
            [
                'trigger_key' => 'rule_first_steps',
                'condition_type' => 'consecutive_days',
                'condition_params' => ['operator' => '>=', 'value' => 3, 'min_transaction_count' => 5],
                'level' => 'success',
                'medal_id' => $medals['rule_first_steps']?->id,
                'priority' => 70,
                'message_override' => 'Primeiros passos! 3 dias seguidos lançando transações.',
            ],
            [
                'trigger_key' => 'rule_reserve_1',
                'condition_type' => 'reserve_months',
                'condition_params' => ['operator' => '>=', 'value' => 1, 'min_monthly_expenses' => 300],
                'level' => 'success',
                'medal_id' => $medals['rule_reserve_1']?->id,
                'priority' => 92,
                'message_override' => 'Reserva inicial conquistada! Seu saldo cobre 1 mês de despesas.',
            ],
            [
                'trigger_key' => 'rule_503020_essential',
                'condition_type' => 'pillar_threshold',
                'condition_params' => ['pillar' => 'essential', 'operator' => '>', 'value' => 50, 'min_transaction_count' => 5],
                'level' => 'danger',
                'medal_id' => $medals['rule_503020_essential']?->id,
                'priority' => 100,
                'message_override' => 'Seus gastos essenciais ultrapassam 50% da renda. Revise sua distribuição 50/30/20.',
            ],
            [
                'trigger_key' => 'rule_reserve_3',
                'condition_type' => 'reserve_months',
                'condition_params' => ['operator' => '>=', 'value' => 3, 'min_monthly_expenses' => 500],
                'level' => 'success',
                'medal_id' => $medals['rule_reserve_3']?->id,
                'priority' => 95,
                'message_override' => 'Reserva emergencial conquistada! Seu saldo cobre 3 meses de despesas.',
            ],
            [
                'trigger_key' => 'rule_reserve_solid',
                'condition_type' => 'reserve_months',
                'condition_params' => ['operator' => '>=', 'value' => 6, 'min_monthly_expenses' => 800],
                'level' => 'success',
                'medal_id' => $medals['rule_reserve_solid']?->id,
                'priority' => 90,
                'message_override' => 'Sua reserva está sólida! Você tem o equivalente a 6 ou mais meses de despesas guardados.',
            ],
            [
                'trigger_key' => 'rule_consistency_bonus',
                'condition_type' => 'consecutive_days',
                'condition_params' => ['operator' => '>=', 'value' => 5, 'min_transaction_count' => 15],
                'level' => 'success',
                'medal_id' => $medals['rule_consistency_bonus']?->id,
                'priority' => 80,
                'message_override' => 'Bônus de consistência! 5 dias seguidos lançando transações.',
            ],
            [
                'trigger_key' => 'rule_consistency_10',
                'condition_type' => 'consecutive_days',
                'condition_params' => ['operator' => '>=', 'value' => 10, 'min_transaction_count' => 25],
                'level' => 'success',
                'medal_id' => $medals['rule_consistency_10']?->id,
                'priority' => 78,
                'message_override' => 'Mestre da consistência! 10 dias seguidos lançando transações.',
            ],
            [
                'trigger_key' => 'rule_503020_balanced',
                'condition_type' => 'pillar_threshold',
                'condition_params' => [
                    'pillar' => 'essential',
                    'operator' => '<=',
                    'value' => 50,
                    'min_baseline_income' => 500,
                    'min_transaction_count' => 20,
                ],
                'level' => 'success',
                'medal_id' => $medals['rule_503020_balanced']?->id,
                'priority' => 85,
                'message_override' => 'Equilíbrio 50/30/20! Seus gastos essenciais estão dentro da meta.',
            ],
            [
                'trigger_key' => 'rule_platinum_pro',
                'condition_type' => 'pro_subscription',
                'condition_params' => [],
                'level' => 'success',
                'medal_id' => $medals['rule_platinum_pro']?->id,
                'priority' => 110,
                'message_override' => 'Você é Vertex PRO! Acesso completo ao plano premium.',
            ],
            [
                'trigger_key' => 'rule_reserve_12',
                'condition_type' => 'reserve_months',
                'condition_params' => ['operator' => '>=', 'value' => 12, 'min_monthly_expenses' => 1000],
                'level' => 'success',
                'medal_id' => $medals['rule_reserve_12']?->id,
                'priority' => 88,
                'message_override' => 'Super reserva! 12 meses de despesas guardados. Você é referência em gestão financeira.',
            ],
            [
                'trigger_key' => 'rule_consistency_30',
                'condition_type' => 'consecutive_days',
                'condition_params' => ['operator' => '>=', 'value' => 30, 'min_transaction_count' => 50],
                'level' => 'success',
                'medal_id' => $medals['rule_consistency_30']?->id,
                'priority' => 76,
                'message_override' => 'Lenda da consistência! 30 dias seguidos lançando transações.',
            ],
            [
                'trigger_key' => 'rule_savings_20',
                'condition_type' => 'savings_threshold',
                'condition_params' => ['operator' => '>=', 'value' => 20, 'min_transaction_count' => 30],
                'level' => 'success',
                'medal_id' => $medals['rule_savings_20']?->id,
                'priority' => 82,
                'message_override' => 'Poupança exemplar! Você destina 20% ou mais da renda a poupança e investimentos.',
            ],
        ];

        foreach ($rules as $data) {
            CoachingRule::updateOrCreate(
                ['trigger_key' => $data['trigger_key']],
                array_merge($data, ['is_active' => true])
            );
        }
    }
}
