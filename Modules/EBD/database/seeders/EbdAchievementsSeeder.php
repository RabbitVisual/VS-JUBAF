<?php

namespace Modules\EBD\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBD\App\Models\EbdAchievement;

class EbdAchievementsSeeder extends Seeder
{
    /**
     * Conquistas 500+: marcos de XP, jogos, aulas, sequência.
     */
    public function run(): void
    {
        $order = 0;

        // 1–400: Marcos de XP (100 em 100 até 40.000)
        for ($xp = 100; $xp <= 40_000; $xp += 100) {
            $tier = $xp <= 5_000 ? 'bronze' : ($xp <= 15_000 ? 'silver' : ($xp <= 25_000 ? 'gold' : 'platinum'));
            EbdAchievement::updateOrCreate(
                ['slug' => 'xp-' . $xp],
                [
                    'name' => "Marco de {$xp} XP",
                    'description' => "Acumule {$xp} pontos de experiência.",
                    'icon_id' => null,
                    'icon_fa_name' => 'trophy',
                    'tier' => $tier,
                    'difficulty' => $xp <= 1000 ? 'easy' : ($xp <= 10000 ? 'medium' : 'hard'),
                    'trigger_type' => 'xp_milestone',
                    'trigger_value' => ['xp' => $xp],
                    'xp_bonus' => min(50, (int) ($xp / 100)),
                    'order' => ++$order,
                    'is_active' => true,
                    'is_hidden' => false,
                ]
            );
        }

        $gameSlugs = [
            'heroi-da-fe', 'trio-biblico', 'mestre-do-conhecimento', 'arca-da-memoria', 'quem-disse',
            'linha-do-tempo', 'espada-afiada', 'forca-da-fe', 'versemaster', 'caca-palavras',
            'complete-o-versiculo', 'desafio-dos-livros', 'palavras-cruzadas', 'parabolas', 'navegador-biblico',
        ];

        // Primeira vitória em cada jogo
        foreach ($gameSlugs as $slug) {
            $name = str_replace('-', ' ', ucwords($slug, '-'));
            EbdAchievement::updateOrCreate(
                ['slug' => 'first-' . $slug],
                [
                    'name' => "Primeira partida: {$name}",
                    'description' => "Jogue uma partida de {$name}.",
                    'icon_id' => null,
                    'icon_fa_name' => 'star',
                    'tier' => 'bronze',
                    'difficulty' => 'easy',
                    'trigger_type' => 'game_first_win',
                    'trigger_value' => ['game_slug' => $slug],
                    'xp_bonus' => 10,
                    'order' => ++$order,
                    'is_active' => true,
                    'is_hidden' => false,
                ]
            );
        }

        // Partidas por jogo: 5, 10, 25, 50, 100
        foreach ($gameSlugs as $slug) {
            $name = str_replace('-', ' ', ucwords($slug, '-'));
            foreach ([5 => '5 partidas', 10 => '10 partidas', 25 => '25 partidas', 50 => '50 partidas', 100 => '100 partidas'] as $min => $label) {
                EbdAchievement::updateOrCreate(
                    ['slug' => 'plays-' . $slug . '-' . $min],
                    [
                        'name' => "{$name}: {$label}",
                        'description' => "Jogue {$label} de {$name}.",
                        'icon_id' => null,
                        'icon_fa_name' => 'gamepad',
                        'tier' => $min <= 10 ? 'bronze' : ($min <= 50 ? 'silver' : 'gold'),
                        'difficulty' => $min <= 10 ? 'easy' : ($min <= 50 ? 'medium' : 'hard'),
                        'trigger_type' => 'game_plays',
                        'trigger_value' => ['min' => $min, 'game_slug' => $slug],
                        'xp_bonus' => min(100, $min),
                        'order' => ++$order,
                        'is_active' => true,
                        'is_hidden' => false,
                    ]
                );
            }
        }

        // Aulas concluídas: 1, 5, 10, 25, 50, 100, 200
        foreach ([1, 5, 10, 25, 50, 100, 200] as $min) {
            EbdAchievement::updateOrCreate(
                ['slug' => 'lessons-' . $min],
                [
                    'name' => "{$min} aula(s) concluída(s)",
                    'description' => "Conclua {$min} aula(s) na EBD.",
                    'icon_id' => null,
                    'icon_fa_name' => 'book-open',
                    'tier' => $min <= 10 ? 'bronze' : ($min <= 50 ? 'silver' : 'gold'),
                    'difficulty' => $min <= 5 ? 'easy' : ($min <= 25 ? 'medium' : 'hard'),
                    'trigger_type' => 'lesson_complete',
                    'trigger_value' => ['min' => $min],
                    'xp_bonus' => min(100, $min * 2),
                    'order' => ++$order,
                    'is_active' => true,
                    'is_hidden' => false,
                ]
            );
        }

        // Sequência de dias jogando: 3, 7, 14, 30
        foreach ([3 => '3 dias', 7 => '7 dias', 14 => '14 dias', 30 => '30 dias'] as $days => $label) {
            EbdAchievement::updateOrCreate(
                ['slug' => 'streak-' . $days],
                [
                    'name' => "Sequência: {$label}",
                    'description' => "Jogue jogos da EBD em {$label} consecutivos.",
                    'icon_id' => null,
                    'icon_fa_name' => 'fire',
                    'tier' => $days <= 7 ? 'bronze' : ($days <= 14 ? 'silver' : 'gold'),
                    'difficulty' => $days <= 3 ? 'easy' : ($days <= 14 ? 'medium' : 'hard'),
                    'trigger_type' => 'streak_days',
                    'trigger_value' => ['days' => $days],
                    'xp_bonus' => $days * 5,
                    'order' => ++$order,
                    'is_active' => true,
                    'is_hidden' => false,
                ]
            );
        }
    }
}
