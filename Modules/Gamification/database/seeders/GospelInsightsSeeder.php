<?php

namespace Modules\Gamification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Gamification\Models\Insight;

class GospelInsightsSeeder extends Seeder
{
    public function run(): void
    {
        $insights = [
            ['trigger_event' => 'first_steps_gospel', 'content' => 'Que bom ter você aqui! Complete seu perfil e adicione um versículo favorito na Bíblia para começar sua jornada.', 'level' => 'info', 'is_active' => true, 'is_pro_only' => false],
            ['trigger_event' => 'profile_complete', 'content' => 'Seu perfil está completo! Isso ajuda a igreja a te conhecer melhor e você ganha mais pontos de engajamento.', 'level' => 'success', 'is_active' => true, 'is_pro_only' => false],
            ['trigger_event' => 'bible_first_favorite', 'content' => 'Você adicionou seu primeiro versículo favorito! A Palavra guardada no coração fortalece a fé.', 'level' => 'success', 'is_active' => true, 'is_pro_only' => false],
            ['trigger_event' => 'bible_favorites', 'content' => 'Continue guardando versículos favoritos. A Bíblia é nossa bússola para o dia a dia.', 'level' => 'info', 'is_active' => true, 'is_pro_only' => false],
            ['trigger_event' => 'events_registered', 'content' => 'Você se inscreveu em um evento! A participação fortalece a comunhão.', 'level' => 'success', 'is_active' => true, 'is_pro_only' => false],
            ['trigger_event' => 'ministries_joined', 'content' => 'Fazer parte de um ministério é servir com os dons que Deus nos deu. Parabéns por se envolver!', 'level' => 'success', 'is_active' => true, 'is_pro_only' => false],
            ['trigger_event' => 'pastor_dashboard', 'content' => 'Pastor, acompanhe aqui os aniversariantes da semana e os pedidos de oração. O rebanho conta com seu cuidado.', 'level' => 'info', 'is_active' => true, 'is_pro_only' => false],
        ];

        foreach ($insights as $row) {
            Insight::firstOrCreate(
                [
                    'trigger_event' => $row['trigger_event'],
                    'content' => $row['content'],
                ],
                ['level' => $row['level'], 'is_active' => $row['is_active'], 'is_pro_only' => $row['is_pro_only'] ?? false]
            );
        }
    }
}
