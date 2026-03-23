<?php

namespace Database\Seeders;

use Modules\Gamification\App\Models\Badge;
use Modules\Gamification\App\Models\GamificationLevel;
use Illuminate\Database\Seeder;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Levels (icons: Font Awesome short names)
        $levels = [
            [
                'name' => 'Visitante',
                'description' => 'Início da jornada na comunidade.',
                'icon' => 'user',
                'color' => 'gray',
                'points_min' => 0,
                'points_max' => 99,
                'order' => 1,
            ],
            [
                'name' => 'Participante',
                'description' => 'Começando a se envolver com a igreja.',
                'icon' => 'star',
                'color' => 'yellow',
                'points_min' => 100,
                'points_max' => 299,
                'order' => 2,
            ],
            [
                'name' => 'Membro Ativo',
                'description' => 'Envolvimento constante nas atividades.',
                'icon' => 'medal',
                'color' => 'green',
                'points_min' => 300,
                'points_max' => 699,
                'order' => 3,
            ],
            [
                'name' => 'Discípulo',
                'description' => 'Dedicação ao serviço e crescimento.',
                'icon' => 'hand-holding-heart',
                'color' => 'blue',
                'points_min' => 700,
                'points_max' => 1499,
                'order' => 4,
            ],
            [
                'name' => 'Líder',
                'description' => 'Liderança e exemplo para os irmãos.',
                'icon' => 'trophy',
                'color' => 'purple',
                'points_min' => 1500,
                'points_max' => 2999,
                'order' => 5,
            ],
            [
                'name' => 'Embaixador',
                'description' => 'Pilar fundamental da comunidade.',
                'icon' => 'crown',
                'color' => 'rose',
                'points_min' => 3000,
                'points_max' => null,
                'order' => 6,
            ],
        ];

        foreach ($levels as $level) {
            GamificationLevel::updateOrCreate(
                ['name' => $level['name']],
                $level + ['is_active' => true]
            );
        }

        // 2. Badges (icons: Font Awesome short names)
        $badges = [
            // Core
            [
                'name' => 'Boas Vindas',
                'description' => 'Cadastro realizado no sistema.',
                'icon' => 'door-open',
                'color' => 'gray',
                'criteria_type' => 'manual',
                'criteria_value' => null,
                'order' => 1,
            ],
            [
                'name' => 'Batizado',
                'description' => 'Membro batizado na congregação.',
                'icon' => 'water',
                'color' => 'blue',
                'criteria_type' => 'is_baptized',
                'criteria_value' => true,
                'order' => 2,
            ],
            [
                'name' => 'Perfil Completo',
                'description' => 'Cadastro 100% preenchido.',
                'icon' => 'id-card',
                'color' => 'green',
                'criteria_type' => 'profile_complete',
                'criteria_value' => ['percentage' => 100],
                'order' => 3,
            ],

            // Tempo de Casa
            [
                'name' => '3 Meses de Casa',
                'description' => 'Membro há 3 meses na congregação.',
                'icon' => 'heart',
                'color' => 'pink',
                'criteria_type' => 'time_congregating',
                'criteria_value' => ['months' => 3],
                'order' => 8,
            ],
            [
                'name' => '1 Ano de Casa',
                'description' => 'Membro há 1 ano.',
                'icon' => 'cake-candles',
                'color' => 'yellow',
                'criteria_type' => 'time_congregating',
                'criteria_value' => ['months' => 12],
                'order' => 10,
            ],
            [
                'name' => '2 Anos de Casa',
                'description' => 'Membro há 2 anos.',
                'icon' => 'champagne-glasses',
                'color' => 'orange',
                'criteria_type' => 'time_congregating',
                'criteria_value' => ['months' => 24],
                'order' => 11,
            ],
            [
                'name' => 'Jubileu (5 Anos)',
                'description' => '5 anos de fidelidade.',
                'icon' => 'award',
                'color' => 'purple',
                'criteria_type' => 'time_congregating',
                'criteria_value' => ['months' => 60],
                'order' => 12,
            ],

            // Bíblia / Estudos
            [
                'name' => 'Leitor da Palavra',
                'description' => '5 versículos marcados como favoritos.',
                'icon' => 'book-bible',
                'color' => 'indigo',
                'criteria_type' => 'bible_favorites',
                'criteria_value' => ['count' => 5],
                'order' => 20,
            ],
            [
                'name' => 'Intercessor',
                'description' => 'Guerreiro de oração.',
                'icon' => 'hands-praying',
                'color' => 'rose',
                'criteria_type' => 'manual',
                'criteria_value' => null,
                'order' => 21,
            ],
            [
                'name' => 'Aluno EBD',
                'description' => 'Inscrito na Escola Bíblica.',
                'icon' => 'school',
                'color' => 'blue',
                'criteria_type' => 'manual',
                'criteria_value' => null,
                'order' => 22,
            ],

            // Eventos
            [
                'name' => 'Presença Confirmada',
                'description' => 'Primeiro evento participado.',
                'icon' => 'ticket',
                'color' => 'cyan',
                'criteria_type' => 'events_attended',
                'criteria_value' => ['count' => 1],
                'order' => 30,
            ],
            [
                'name' => 'Membro Participativo',
                'description' => 'Marcou presença em 5 eventos.',
                'icon' => 'calendar-check',
                'color' => 'green',
                'criteria_type' => 'events_attended',
                'criteria_value' => ['count' => 5],
                'order' => 31,
            ],

            // Ministérios
            [
                'name' => 'Voluntário',
                'description' => 'Ingressou em um ministério.',
                'icon' => 'handshake-angle',
                'color' => 'orange',
                'criteria_type' => 'ministries_joined',
                'criteria_value' => ['count' => 1],
                'order' => 40,
            ],
            [
                'name' => 'Liderança',
                'description' => 'Líder de ministério.',
                'icon' => 'users-rays',
                'color' => 'red',
                'criteria_type' => 'manual',
                'criteria_value' => null,
                'order' => 41,
            ],

            // Financeiro
            [
                'name' => 'Dizimista',
                'description' => 'Contribuiu para a obra.',
                'icon' => 'hand-holding-dollar',
                'color' => 'emerald',
                'criteria_type' => 'contributions_made',
                'criteria_value' => ['count' => 1],
                'order' => 50,
            ],
            [
                'name' => 'Coluna Financeira',
                'description' => 'Contribuições consistentes (10+).',
                'icon' => 'landmark',
                'color' => 'amber',
                'criteria_type' => 'contributions_made',
                'criteria_value' => ['count' => 10],
                'order' => 51,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::updateOrCreate(
                ['name' => $badge['name']],
                $badge + ['is_active' => true, 'points_required' => 0]
            );
        }
    }
}
