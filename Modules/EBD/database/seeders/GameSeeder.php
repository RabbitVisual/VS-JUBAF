<?php

namespace Modules\EBD\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\EBD\App\Models\Game;
use Modules\EBD\App\Models\GameQuestion;
use Modules\EBD\App\Models\GameAnswer;

class GameSeeder extends Seeder
{
    public function run()
    {
        // 1. Mestre do Conhecimento (Quiz)
        $quiz = Game::firstOrCreate(
            ['slug' => 'mestre-do-conhecimento'],
            [
                'name' => 'Mestre do Conhecimento',
                'description' => 'Teste seus conhecimentos bíblicos contra o relógio!',
                'icon' => 'brain-circuit',
                'is_active' => true,
                // 'category' => 'quiz' // Assuming column exists or handled by logic
            ]
        );

        $questions = [
            [
                'q' => 'Quem foi engolido por um grande peixe?',
                'a' => [
                    ['text' => 'Jonas', 'correct' => true],
                    ['text' => 'Pedro', 'correct' => false],
                    ['text' => 'Paulo', 'correct' => false],
                    ['text' => 'Moisés', 'correct' => false],
                ]
            ],
            [
                'q' => 'Qual é o último livro da Bíblia?',
                'a' => [
                    ['text' => 'Apocalipse', 'correct' => true],
                    ['text' => 'Gênesis', 'correct' => false],
                    ['text' => 'Mateus', 'correct' => false],
                    ['text' => 'Malaquias', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quem abriu o Mar Vermelho?',
                'a' => [
                    ['text' => 'Moisés', 'correct' => true],
                    ['text' => 'Josué', 'correct' => false],
                    ['text' => 'Arão', 'correct' => false],
                    ['text' => 'Elias', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quem matou Golias?',
                'a' => [
                    ['text' => 'Davi', 'correct' => true],
                    ['text' => 'Saul', 'correct' => false],
                    ['text' => 'Samuel', 'correct' => false],
                    ['text' => 'Salomão', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quantos mandamentos Deus deu a Moisés?',
                'a' => [
                    ['text' => '10', 'correct' => true],
                    ['text' => '7', 'correct' => false],
                    ['text' => '12', 'correct' => false],
                    ['text' => '5', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quem traiu Jesus?',
                'a' => [
                    ['text' => 'Judas Iscariotes', 'correct' => true],
                    ['text' => 'Pedro', 'correct' => false],
                    ['text' => 'Tomé', 'correct' => false],
                    ['text' => 'Pilatos', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quem construiu a Arca?',
                'a' => [
                    ['text' => 'Noé', 'correct' => true],
                    ['text' => 'Abraão', 'correct' => false],
                    ['text' => 'Adão', 'correct' => false],
                    ['text' => 'José', 'correct' => false],
                ]
            ],
             [
                'q' => 'Onde Jesus nasceu?',
                'a' => [
                    ['text' => 'Belém', 'correct' => true],
                    ['text' => 'Nazaré', 'correct' => false],
                    ['text' => 'Jerusalém', 'correct' => false],
                    ['text' => 'Egito', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quem foi o primeiro homem?',
                'a' => [
                    ['text' => 'Adão', 'correct' => true],
                    ['text' => 'Caim', 'correct' => false],
                    ['text' => 'Abel', 'correct' => false],
                    ['text' => 'Sete', 'correct' => false],
                ]
            ],
             [
                'q' => 'Quem foi lançado na cova dos leões?',
                'a' => [
                    ['text' => 'Daniel', 'correct' => true],
                    ['text' => 'Davi', 'correct' => false],
                    ['text' => 'José', 'correct' => false],
                    ['text' => 'Sansão', 'correct' => false],
                ]
            ],
        ];

        foreach ($questions as $q) {
            // Check if question exists to avoid dupes in seed
            $question = GameQuestion::firstOrCreate([
                'game_id' => $quiz->id,
                'question_text' => $q['q']
            ], [
                'points' => 10,
                'time_limit' => 30,
            ]);

            if ($question->wasRecentlyCreated) {
                foreach ($q['a'] as $ans) {
                    GameAnswer::create([
                        'question_id' => $question->id,
                        'answer_text' => $ans['text'],
                        'is_correct' => $ans['correct'],
                    ]);
                }
            }
        }

        // 2. Arca da Memória
        Game::firstOrCreate(
            ['slug' => 'arca-da-memoria'],
            [
                'name' => 'Arca da Memória',
                'description' => 'Encontre os pares bíblicos.',
                'icon' => 'cards',
                'is_active' => true,
                // 'category' => 'memory'
            ]
        );

        // 3. Quem Disse?
        Game::firstOrCreate(
            ['slug' => 'quem-disse'],
            [
                'name' => 'Quem Disse?',
                'description' => 'Identifique o autor da frase bíblica.',
                'icon' => 'comment-quote',
                'is_active' => true,
                // 'category' => 'quiz'
            ]
        );

        // 4. Linha do Tempo
        Game::firstOrCreate(
            ['slug' => 'linha-do-tempo'],
            [
                'name' => 'Linha do Tempo',
                'description' => 'Ordene os eventos cronologicamente.',
                'icon' => 'hourglass-start',
                'is_active' => true,
                // 'category' => 'puzzle'
            ]
        );

        // 5. Espada Afiada
        Game::firstOrCreate(
            ['slug' => 'espada-afiada'],
            [
                'name' => 'Espada Afiada',
                'description' => 'Teste seus reflexos com os livros da Bíblia.',
                'icon' => 'sword',
                'is_active' => true,
                // 'category' => 'reflex'
            ]
        );

        // 6. Forca da Fé
        Game::firstOrCreate(
            ['slug' => 'forca-da-fe'],
            [
                'name' => 'Forca da Fé',
                'description' => 'Adivinhe a palavra antes que seja tarde.',
                'icon' => 'keyboard',
                'is_active' => true,
                // 'category' => 'word'
            ]
        );

        // 7. VerseMaster
        Game::firstOrCreate(
            ['slug' => 'versemaster'],
            [
                'name' => 'VerseMaster',
                'description' => 'Decore versículos.',
                'icon' => 'scroll',
                'is_active' => true,
                // 'category' => 'memory'
            ]
        );
    }
}
