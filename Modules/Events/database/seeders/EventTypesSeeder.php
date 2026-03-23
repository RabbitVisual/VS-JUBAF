<?php

namespace Modules\Events\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Events\App\Models\EventType;

class EventTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Culto',                    'slug' => 'culto',             'icon' => 'church',           'color' => '#4F46E5', 'order' => 1],
            ['name' => 'Retiro Espiritual',        'slug' => 'retiro',            'icon' => 'mountain-sun',     'color' => '#059669', 'order' => 2],
            ['name' => 'Congresso / Conferência',  'slug' => 'congresso',         'icon' => 'microphone',       'color' => '#2563EB', 'order' => 3],
            ['name' => 'Acampamento',              'slug' => 'acampamento',       'icon' => 'campground',       'color' => '#16A34A', 'order' => 4],
            ['name' => 'Batismo',                  'slug' => 'batismo',           'icon' => 'water',            'color' => '#0EA5E9', 'order' => 5],
            ['name' => 'Encontro de Jovens',       'slug' => 'encontro-jovens',   'icon' => 'users',            'color' => '#7C3AED', 'order' => 6],
            ['name' => 'Encontro de Casais',       'slug' => 'encontro-casais',   'icon' => 'heart',            'color' => '#EC4899', 'order' => 7],
            ['name' => 'Encontro de Mulheres',     'slug' => 'encontro-mulheres', 'icon' => 'venus',            'color' => '#DB2777', 'order' => 8],
            ['name' => 'Encontro de Homens',       'slug' => 'encontro-homens',   'icon' => 'mars',             'color' => '#1D4ED8', 'order' => 9],
            ['name' => 'Treinamento / Capacitação','slug' => 'treinamento',       'icon' => 'chalkboard-user',  'color' => '#D97706', 'order' => 10],
            ['name' => 'Missões',                  'slug' => 'missoes',           'icon' => 'globe',            'color' => '#0D9488', 'order' => 11],
            ['name' => 'Festa / Confraternização', 'slug' => 'festa',             'icon' => 'party-bell',       'color' => '#F59E0B', 'order' => 12],
            ['name' => 'Culto de Ação de Graças',  'slug' => 'acao-de-gracas',    'icon' => 'hands-praying',    'color' => '#EA580C', 'order' => 13],
            ['name' => 'Evangelismo',              'slug' => 'evangelismo',       'icon' => 'bullhorn',         'color' => '#DC2626', 'order' => 14],
            ['name' => 'Escola Bíblica',           'slug' => 'escola-biblica',    'icon' => 'book-bible',       'color' => '#65A30D', 'order' => 15],
            ['name' => 'Seminário / Palestra',     'slug' => 'seminario',         'icon' => 'presentation',     'color' => '#475569', 'order' => 16],
            ['name' => 'Workshop / Oficina',       'slug' => 'workshop',          'icon' => 'screwdriver-wrench','color' => '#6D28D9', 'order' => 17],
            ['name' => 'Ação Social',              'slug' => 'acao-social',       'icon' => 'hands-holding-heart','color' => '#B45309', 'order' => 18],
            ['name' => 'Outro',                    'slug' => 'outro',             'icon' => 'calendar',         'color' => '#6B7280', 'order' => 99],
        ];

        foreach ($types as $type) {
            EventType::updateOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}
