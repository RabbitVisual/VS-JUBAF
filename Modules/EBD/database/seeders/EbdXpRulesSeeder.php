<?php

namespace Modules\EBD\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBD\App\Models\EbdXpRule;

class EbdXpRulesSeeder extends Seeder
{
    /**
     * Regras de XP: jogos (10% do score, cap 50, min 1), aula (fixed 100), streak bônus (50% extra).
     */
    public function run(): void
    {
        $rules = [
            [
                'source_type' => 'game',
                'source_slug' => null,
                'formula' => 'score_percent',
                'value' => 10,
                'cap' => 50,
                'min' => 1,
                'is_active' => true,
            ],
            [
                'source_type' => 'lesson',
                'source_slug' => null,
                'formula' => 'fixed',
                'value' => 100,
                'cap' => null,
                'min' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $r) {
            EbdXpRule::updateOrCreate(
                ['source_type' => $r['source_type'], 'source_slug' => $r['source_slug']],
                $r
            );
        }

        $gameSlugs = [
            'heroi-da-fe', 'trio-biblico', 'mestre-do-conhecimento', 'arca-da-memoria', 'quem-disse',
            'linha-do-tempo', 'espada-afiada', 'forca-da-fe', 'versemaster', 'caca-palavras',
            'complete-o-versiculo', 'desafio-dos-livros', 'palavras-cruzadas', 'parabolas', 'navegador-biblico',
        ];
        foreach ($gameSlugs as $slug) {
            EbdXpRule::updateOrCreate(
                ['source_type' => 'game', 'source_slug' => $slug],
                [
                    'formula' => 'score_percent',
                    'value' => 10,
                    'cap' => 50,
                    'min' => 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
