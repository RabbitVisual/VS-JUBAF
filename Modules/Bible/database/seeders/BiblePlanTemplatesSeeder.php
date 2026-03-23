<?php

namespace Modules\Bible\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Bible\App\Models\BiblePlanTemplate;

class BiblePlanTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'nt_psalms',
                'name' => 'Leitor Iniciante (NT + Salmos)',
                'description' => 'Foco na Nova Aliança e Salmos. Ideal para quem está começando.',
                'complexity' => 'iniciante',
                'order_type' => 'nt_psalms',
                'options' => null,
                'is_active' => true,
            ],
            [
                'key' => 'canonical',
                'name' => 'Bíblia Toda em 1 Ano (Canônico)',
                'description' => 'Leitura completa em ordem canônica. Intercalando AT e NT na visão Promessa e Cumprimento.',
                'complexity' => 'standard',
                'order_type' => 'canonical',
                'options' => null,
                'is_active' => true,
            ],
            [
                'key' => 'chronological',
                'name' => 'Cronológico (Batista)',
                'description' => 'Ordem cronológica dos eventos. Abordagem teológica Batista.',
                'complexity' => 'standard',
                'order_type' => 'chronological',
                'options' => null,
                'is_active' => true,
            ],
            [
                'key' => 'doctrinal',
                'name' => 'Doutrinário (Fé Batista)',
                'description' => 'Grandes temas: Sola Scriptura, Centralidade de Cristo, Autonomia do Crente, Piedade Pessoal.',
                'complexity' => 'standard',
                'order_type' => 'doctrinal',
                'options' => [
                    'doctrinal_themes' => [
                        [
                            'theme' => 'Sola Scriptura / Autoridade das Escrituras',
                            'references' => [
                                ['book' => '2Tm', 'chapter' => 3],
                                ['book' => '2Pe', 'chapter' => 1],
                                ['book' => 'Sl', 'chapter' => 119],
                                ['book' => 'Is', 'chapter' => 40],
                            ],
                        ],
                        [
                            'theme' => 'Batismo por imersão',
                            'references' => [
                                ['book' => 'Mt', 'chapter' => 3],
                                ['book' => 'Rm', 'chapter' => 6],
                                ['book' => 'At', 'chapter' => 2],
                                ['book' => 'At', 'chapter' => 8],
                            ],
                        ],
                        [
                            'theme' => 'Ceia como memorial',
                            'references' => [
                                ['book' => 'Mt', 'chapter' => 26],
                                ['book' => '1Co', 'chapter' => 11],
                            ],
                        ],
                        [
                            'theme' => 'Sacerdócio universal dos crentes',
                            'references' => [
                                ['book' => '1Pe', 'chapter' => 2],
                                ['book' => 'Ap', 'chapter' => 1],
                                ['book' => 'Hb', 'chapter' => 4],
                            ],
                        ],
                        [
                            'theme' => 'Centralidade de Cristo',
                            'references' => [
                                ['book' => 'Cl', 'chapter' => 1],
                                ['book' => 'Hb', 'chapter' => 1],
                                ['book' => 'Jo', 'chapter' => 1],
                            ],
                        ],
                        [
                            'theme' => 'Autonomia do crente e da igreja local',
                            'references' => [
                                ['book' => 'Mt', 'chapter' => 18],
                                ['book' => '1Co', 'chapter' => 5],
                                ['book' => 'At', 'chapter' => 15],
                            ],
                        ],
                    ],
                ],
                'is_active' => true,
            ],
            [
                'key' => 'christ_centered',
                'name' => 'Cristocêntrico',
                'description' => 'Evangelhos → Atos → Epístolas/Apocalipse → AT.',
                'complexity' => 'standard',
                'order_type' => 'christ_centered',
                'options' => null,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $t) {
            BiblePlanTemplate::updateOrCreate(
                ['key' => $t['key']],
                $t
            );
        }
    }
}
