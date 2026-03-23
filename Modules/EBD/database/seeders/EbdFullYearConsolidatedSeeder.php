<?php

namespace Modules\EBD\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDLessonMaterial;
use Modules\EBD\App\Models\EBDLessonQuestion;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EBDTeacher;
use Modules\EBD\App\Models\EbdBadge;
use Modules\EBD\App\Models\EbdLessonMedia;
use Modules\EBD\App\Models\EBDEvaluation;

/**
 * Seed consolidado: ano letivo completo 22/02/2026 a 31/12/2026.
 * Lições, materiais, vídeos, questões e avaliações para todas as classes (adultos, jovens, adolescentes, crianças).
 * Idempotente: pode ser executado várias vezes sem duplicar.
 */
class EbdFullYearConsolidatedSeeder extends Seeder
{
    protected array $bibleBooks = [
        'Gênesis', 'Êxodo', 'Romanos', 'João', '1 Coríntios', 'Salmos', 'Isaías', 'Mateus',
        'Atos', 'Hebreus', 'Efésios', 'Filipenses', 'Tiago', '1 Pedro', 'Apocalipse', 'Josué',
        '2 Timóteo', 'Lucas', 'Colossenses', 'Gálatas', 'Provérbios', 'Daniel', 'Ezequiel',
    ];

    /** Títulos por trimestre (1º: 1-13, 2º: 14-26, 3º: 27-39, 4º: 40-44) */
    protected array $trimestreThemes = [
        1 => 'O Deus que age',
        2 => 'Igreja: Corpo de Cristo',
        3 => 'Vida Cristã',
        4 => 'Profecias e Esperança',
    ];

    protected array $lessonTitlesByTrimester = [
        1 => [
            'A soberania de Deus', 'Deus criador', 'O Deus que chama', 'Deus e a aliança', 'Deus e a obediência',
            'Deus e a provisão', 'Deus e a justiça', 'Deus e a misericórdia', 'Deus e a santidade', 'Deus e a graça',
            'Deus e a fé', 'Deus e a esperança', 'Deus e o amor',
        ],
        2 => [
            'A natureza da Igreja', 'A cabeça da Igreja', 'Os membros do Corpo', 'Unidade na diversidade', 'Dons espirituais',
            'Edificando o Corpo', 'A missão da Igreja', 'A comunhão da Igreja', 'A adoração na Igreja', 'O serviço na Igreja',
            'A Igreja e o mundo', 'A Igreja e a família', 'A Igreja e a esperança',
        ],
        3 => [
            'Vida de oração', 'Vida na Palavra', 'Vida em santidade', 'Vida em amor', 'Vida em serviço',
            'Vida em gratidão', 'Vida em humildade', 'Vida em fé', 'Vida em esperança', 'Vida em comunhão',
            'Vida em missão', 'Vida em família', 'Vida em sabedoria',
        ],
        4 => [
            'O tempo do fim', 'A volta de Cristo', 'O reino eterno', 'A nova criação', 'Maranata',
        ],
    ];

    public function run(): void
    {
        $this->command->info('EbdFullYearConsolidatedSeeder: iniciando (ano 22/02/2026 a 31/12/2026).');

        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                ['name' => 'Administrador', 'password' => bcrypt('password')]
            );
        }

        $classes = EBDClass::orderBy('order')->get();
        if ($classes->count() < 4) {
            $this->ensureClasses();
            $classes = EBDClass::orderBy('order')->get();
        }

        $this->ensureTeachersAndStudents($classes, $admin);

        $sundays = $this->getSundaysFrom2026();
        $this->command->info('Total de domingos no período: '.count($sundays));

        $lessonCount = 0;
        $materialCount = 0;
        $mediaCount = 0;
        $questionCount = 0;
        $evaluationCount = 0;

        foreach ($classes as $class) {
            $creatorId = $this->getCreatorForClass($class) ?? $admin->id;
            $lessonIndex = 0;

            foreach ($sundays as $date) {
                $lessonIndex++;
                $lessonData = $this->getLessonDataForIndex($lessonIndex, $date, $class);
                $lesson = EBDLesson::firstOrCreate(
                    [
                        'class_id' => $class->id,
                        'lesson_date' => $date->format('Y-m-d'),
                    ],
                    array_merge($lessonData, [
                        'created_by' => $creatorId,
                        'lesson_time' => $date->copy()->setTime(9, 0),
                    ])
                );

                if ($lesson->wasRecentlyCreated) {
                    $lessonCount++;
                }

                $materialCount += $this->seedMaterialsForLesson($lesson);
                $mediaCount += $this->seedMediaForLesson($lesson);
                $questionCount += $this->seedQuestionsForLesson($lesson);
                $evaluationCount += $this->seedEvaluationsForLesson($lesson);
            }
        }

        $this->seedEbdBadges();

        $this->command->info('EbdFullYearConsolidatedSeeder: concluído.');
        $this->command->info("Lições criadas/garantidas: {$lessonCount} novas.");
        $this->command->info("Materiais: {$materialCount}, Mídia: {$mediaCount}, Questões: {$questionCount}, Avaliações: {$evaluationCount}.");
    }

    protected function ensureClasses(): void
    {
        $data = [
            ['name' => 'Adultos - Sala Principal', 'age_group' => 'adult', 'description' => 'Classe para adultos.', 'room' => 'Sala Principal', 'order' => 1],
            ['name' => 'Jovens', 'age_group' => 'youth', 'description' => 'Classe para jovens de 18 a 30 anos.', 'room' => 'Sala 2', 'order' => 2],
            ['name' => 'Adolescentes', 'age_group' => 'teen', 'description' => 'Classe para adolescentes de 13 a 17 anos.', 'room' => 'Sala 3', 'order' => 3],
            ['name' => 'Crianças', 'age_group' => 'children', 'description' => 'Classe para crianças de 5 a 12 anos.', 'room' => 'Sala Infantil', 'order' => 4],
        ];
        foreach ($data as $d) {
            EBDClass::firstOrCreate(
                ['name' => $d['name']],
                array_merge($d, ['schedule_time' => '09:00', 'max_students' => 40, 'is_active' => true])
            );
        }
    }

    protected function ensureTeachersAndStudents($classes, User $admin): void
    {
        $teacherEmails = ['professor1@example.com', 'professor2@example.com', 'professor3@example.com', 'professor4@example.com'];
        $teachers = [];
        for ($i = 0; $i < 4; $i++) {
            $teachers[] = User::firstOrCreate(
                ['email' => $teacherEmails[$i]],
                ['name' => 'Professor '.($i + 1), 'password' => bcrypt('password')]
            );
        }

        foreach ($classes as $index => $class) {
            $teacher = $teachers[$index] ?? $teachers[0];
            EBDTeacher::firstOrCreate(
                ['user_id' => $teacher->id, 'class_id' => $class->id],
                ['role' => 'teacher', 'start_date' => now()->subMonths(6), 'is_active' => true]
            );
        }

        $allUsers = User::whereNotIn('email', array_merge($teacherEmails, ['admin@example.com']))->limit(20)->get();
        if ($allUsers->isEmpty()) {
            for ($i = 1; $i <= 12; $i++) {
                $u = User::firstOrCreate(
                    ['email' => "aluno{$i}@example.com"],
                    ['name' => "Aluno {$i}", 'password' => bcrypt('password')]
                );
                $allUsers->push($u);
            }
        }

        $perClass = (int) max(2, ceil($allUsers->count() / 4));
        $offset = 0;
        foreach ($classes as $class) {
            $slice = $allUsers->slice($offset, $perClass);
            foreach ($slice as $user) {
                EBDStudent::firstOrCreate(
                    ['user_id' => $user->id, 'class_id' => $class->id],
                    ['enrollment_date' => now()->subMonths(rand(1, 6)), 'is_active' => true]
                );
            }
            $offset += $perClass;
        }
    }

    /** @return Carbon[] */
    protected function getSundaysFrom2026(): array
    {
        $start = Carbon::parse('2026-02-22');
        $end = Carbon::parse('2026-12-31');
        if ($start->dayOfWeek !== Carbon::SUNDAY) {
            $start->next(Carbon::SUNDAY);
        }
        $sundays = [];
        while ($start->lte($end)) {
            $sundays[] = $start->copy();
            $start->addWeek();
        }
        return $sundays;
    }

    protected function getCreatorForClass(EBDClass $class): ?int
    {
        $teacher = EBDTeacher::where('class_id', $class->id)->where('is_active', true)->first();
        return $teacher?->user_id;
    }

    protected function getLessonDataForIndex(int $lessonIndex, Carbon $date, EBDClass $class): array
    {
        $trimester = $lessonIndex <= 13 ? 1 : ($lessonIndex <= 26 ? 2 : ($lessonIndex <= 39 ? 3 : 4));
        $subIndex = $lessonIndex <= 13 ? $lessonIndex : ($lessonIndex <= 26 ? $lessonIndex - 13 : ($lessonIndex <= 39 ? $lessonIndex - 26 : $lessonIndex - 39));
        $theme = $this->trimestreThemes[$trimester];
        $titles = $this->lessonTitlesByTrimester[$trimester] ?? $this->lessonTitlesByTrimester[1];
        $title = ($titles[$subIndex - 1] ?? "Lição {$lessonIndex}").' – '.$theme;

        $bookIndex = ($lessonIndex - 1) % count($this->bibleBooks);
        $bibleBook = $this->bibleBooks[$bookIndex];
        $chapter = ($lessonIndex % 22) + 1;
        $verses = '1-10';

        $isPast = $date->lt(Carbon::today());
        $status = $isPast ? 'completed' : 'scheduled';

        $desc = "Lição {$lessonIndex} do trimestre: {$theme}. Estudo bíblico para a classe de {$class->name}.";
        if ($class->age_group === 'children') {
            $desc = "Lição {$lessonIndex} – {$theme}. Conteúdo adaptado para crianças.";
        }

        return [
            'title' => $title,
            'description' => $desc,
            'lesson_date' => $date,
            'lesson_time' => $date->copy()->setTime(9, 0),
            'bible_book' => $bibleBook,
            'bible_chapter' => $chapter,
            'bible_verses' => $verses,
            'bible_version' => 'nvi',
            'objective' => "Compreender os ensinamentos desta lição sobre {$theme} e aplicá-los no dia a dia.",
            'introduction' => 'Nesta lição estudaremos a Palavra de Deus e seu ensino para nossa vida.',
            'development' => 'Desenvolvimento do conteúdo com base em '.$bibleBook.' '.$chapter.'. Leitura e reflexão dos versículos.',
            'conclusion' => 'Resumo dos pontos principais e reforço do objetivo da lição.',
            'application' => 'Como aplicar estes ensinamentos na vida prática, na família e na igreja.',
            'status' => $status,
        ];
    }

    protected function seedMaterialsForLesson(EBDLesson $lesson): int
    {
        $count = 0;
        $video = EBDLessonMaterial::firstOrCreate(
            ['lesson_id' => $lesson->id, 'title' => 'Vídeo da lição'],
            [
                'type' => 'video',
                'url' => 'https://exemplo.igreja.br/ebd/lessons/'.$lesson->id.'/video',
                'description' => 'Vídeo da aula desta lição.',
                'order' => 1,
                'is_required' => true,
                'is_public' => true,
            ]
        );
        if ($video->wasRecentlyCreated) {
            $count++;
        }
        $pdf = EBDLessonMaterial::firstOrCreate(
            ['lesson_id' => $lesson->id, 'title' => 'Material em PDF'],
            [
                'type' => 'pdf',
                'url' => 'https://exemplo.igreja.br/ebd/lessons/'.$lesson->id.'/material.pdf',
                'description' => 'Material de apoio em PDF.',
                'order' => 2,
                'is_required' => false,
                'is_public' => true,
            ]
        );
        if ($pdf->wasRecentlyCreated) {
            $count++;
        }
        return $count;
    }

    protected function seedMediaForLesson(EBDLesson $lesson): int
    {
        $exists = EbdLessonMedia::where('lesson_id', $lesson->id)->exists();
        if ($exists) {
            return 0;
        }
        EbdLessonMedia::create([
            'lesson_id' => $lesson->id,
            'type' => 'local_video',
            'path' => 'lessons/class-'.$lesson->class_id.'/lesson-'.$lesson->id.'-video.mp4',
            'title' => 'Vídeo: '.$lesson->title,
            'duration_seconds' => rand(600, 1800),
        ]);
        return 1;
    }

    protected function seedQuestionsForLesson(EBDLesson $lesson): int
    {
        $templates = $this->getQuestionTemplates();
        $count = 0;
        foreach ($templates as $order => $q) {
            $question = EBDLessonQuestion::firstOrCreate(
                ['lesson_id' => $lesson->id, 'order' => $order + 1],
                [
                    'question' => $q['question'],
                    'type' => $q['type'],
                    'options' => $q['options'] ?? null,
                    'correct_answer' => $q['correct_answer'],
                    'points' => $q['points'] ?? 1,
                    'is_required' => true,
                ]
            );
            if ($question->wasRecentlyCreated) {
                $count++;
            }
        }
        return $count;
    }

    protected function getQuestionTemplates(): array
    {
        return [
            [
                'question' => 'De acordo com a lição, qual é o principal ensino sobre o tema estudado?',
                'type' => 'multiple_choice',
                'options' => ['O amor de Deus', 'A obediência à Palavra', 'A fé que atua', 'Todas as anteriores'],
                'correct_answer' => 'Todas as anteriores',
                'points' => 2,
            ],
            [
                'question' => 'O conteúdo desta lição se baseia em textos bíblicos. Verdadeiro ou falso?',
                'type' => 'true_false',
                'correct_answer' => 'true',
                'points' => 1,
            ],
            [
                'question' => 'Qual livro bíblico foi destacado nesta lição? (resposta curta)',
                'type' => 'short_answer',
                'correct_answer' => 'Conforme a lição',
                'points' => 1,
            ],
            [
                'question' => 'A aplicação prática da lição envolve a vida em família e na igreja. Verdadeiro ou falso?',
                'type' => 'true_false',
                'correct_answer' => 'true',
                'points' => 1,
            ],
            [
                'question' => 'O que devemos fazer com o que aprendemos?',
                'type' => 'multiple_choice',
                'options' => ['Guardar só para nós', 'Praticar no dia a dia', 'Esquecer depois da aula', 'Nenhuma das anteriores'],
                'correct_answer' => 'Praticar no dia a dia',
                'points' => 2,
            ],
            [
                'question' => 'A lição nos incentiva a ler a Bíblia. Verdadeiro ou falso?',
                'type' => 'true_false',
                'correct_answer' => 'true',
                'points' => 1,
            ],
            [
                'question' => 'Em uma frase, qual o objetivo desta lição? (resposta curta)',
                'type' => 'short_answer',
                'correct_answer' => 'Compreender e aplicar os ensinamentos',
                'points' => 2,
            ],
            [
                'question' => 'A conclusão da lição reforça o objetivo estudado. Verdadeiro ou falso?',
                'type' => 'true_false',
                'correct_answer' => 'true',
                'points' => 1,
            ],
        ];
    }

    protected function seedEvaluationsForLesson(EBDLesson $lesson): int
    {
        $students = EBDStudent::where('class_id', $lesson->class_id)->where('is_active', true)->get();
        $count = 0;
        foreach ($students as $student) {
            $eval = EBDEvaluation::firstOrCreate(
                ['lesson_id' => $lesson->id, 'student_id' => $student->id],
                ['status' => 'pending']
            );
            if ($eval->wasRecentlyCreated) {
                $count++;
            }
        }
        return $count;
    }

    protected function seedEbdBadges(): void
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('ebd_badges')) {
            return;
        }
        $badges = [
            ['slug' => 'frequencia-1-trimestre', 'name' => 'Frequência 1º Trimestre', 'description' => 'Participou das lições do primeiro trimestre.'],
            ['slug' => 'frequencia-2-trimestre', 'name' => 'Frequência 2º Trimestre', 'description' => 'Participou das lições do segundo trimestre.'],
            ['slug' => 'concluiu-10-licoes', 'name' => 'Concluiu 10 lições', 'description' => 'Completou 10 lições da EBD.'],
            ['slug' => 'concluiu-25-licoes', 'name' => 'Concluiu 25 lições', 'description' => 'Completou 25 lições da EBD.'],
            ['slug' => 'aluno-destaque-ebd', 'name' => 'Aluno Destaque EBD', 'description' => 'Destaque em participação e conclusão de lições.'],
        ];
        foreach ($badges as $b) {
            EbdBadge::firstOrCreate(
                ['slug' => $b['slug']],
                array_merge($b, ['is_hidden' => false, 'icon_path' => 'medal', 'icon' => 'medal'])
            );
        }
    }
}
