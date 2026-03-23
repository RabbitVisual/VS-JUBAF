<?php

namespace Modules\Gamification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Gamification\Models\CoachingRule;
use Modules\Gamification\Models\Insight;
use Modules\Gamification\Models\Medal;

class GospelCoachingRulesSeeder extends Seeder
{
    public function run(): void
    {
        $insightFirstSteps = Insight::where('trigger_event', 'first_steps_gospel')->first();
        $insightProfile = Insight::where('trigger_event', 'profile_complete')->first();
        $insightBible = Insight::where('trigger_event', 'bible_first_favorite')->first();

        $medalFirst = Medal::firstOrCreate(
            ['trigger_key' => 'gospel_first_steps'],
            [
                'title' => 'Primeiros Passos',
                'description' => 'Perfil iniciado e primeiro versículo favorito.',
                'icon_name' => 'book-bible',
                'color' => 'emerald',
                'rarity' => 'bronze',
                'difficulty' => 'easy',
                'is_active' => true,
                'is_pro_only' => false,
            ]
        );
        $medalProfile = Medal::firstOrCreate(
            ['trigger_key' => 'gospel_profile_complete'],
            [
                'title' => 'Perfil Completo',
                'description' => 'Você completou seu perfil no painel.',
                'icon_name' => 'user-check',
                'color' => 'blue',
                'rarity' => 'silver',
                'difficulty' => 'medium',
                'is_active' => true,
                'is_pro_only' => false,
            ]
        );

        $rules = [
            [
                'trigger_key' => 'gospel_first_steps',
                'condition_type' => 'first_steps',
                'condition_params' => [],
                'insight_id' => $insightFirstSteps?->id,
                'level' => 'info',
                'medal_id' => $medalFirst->id,
                'priority' => 50,
                'is_active' => true,
                'message_override' => 'Que bom ter você aqui! Complete seu perfil e adicione um versículo favorito na Bíblia para começar sua jornada.',
            ],
            [
                'trigger_key' => 'gospel_profile_complete',
                'condition_type' => 'profile_complete',
                'condition_params' => ['percentage' => 80],
                'insight_id' => $insightProfile?->id,
                'level' => 'success',
                'medal_id' => $medalProfile->id,
                'priority' => 70,
                'is_active' => true,
                'message_override' => 'Seu perfil está completo! Isso ajuda a igreja a te conhecer melhor.',
            ],
            [
                'trigger_key' => 'gospel_bible_first_favorite',
                'condition_type' => 'bible_favorites',
                'condition_params' => ['count' => 1],
                'insight_id' => $insightBible?->id,
                'level' => 'success',
                'medal_id' => null,
                'priority' => 60,
                'is_active' => true,
                'message_override' => 'Você adicionou seu primeiro versículo favorito! A Palavra guardada no coração fortalece a fé.',
            ],
        ];

        foreach ($rules as $r) {
            CoachingRule::updateOrCreate(
                ['trigger_key' => $r['trigger_key']],
                $r
            );
        }
    }
}
