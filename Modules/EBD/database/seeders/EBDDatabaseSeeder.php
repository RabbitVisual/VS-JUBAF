<?php

namespace Modules\EBD\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EBDTeacher;

class EBDDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // Create sample classes (use firstOrCreate to avoid duplicates)
            $adultClass = EBDClass::firstOrCreate(
                ['name' => 'Adultos - Sala Principal'],
                [
                    'age_group' => 'adult',
                    'description' => 'Classe para adultos, estudo aprofundado da Palavra de Deus.',
                    'room' => 'Sala Principal',
                    'schedule_time' => '09:00',
                    'max_students' => 50,
                    'is_active' => true,
                    'order' => 1,
                ]
            );
            $this->command->info('Classe Adultos criada: '.$adultClass->id);

            $youthClass = EBDClass::firstOrCreate(
                ['name' => 'Jovens'],
                [
                    'age_group' => 'youth',
                    'description' => 'Classe para jovens de 18 a 30 anos.',
                    'room' => 'Sala 2',
                    'schedule_time' => '09:00',
                    'max_students' => 30,
                    'is_active' => true,
                    'order' => 2,
                ]
            );
            $this->command->info('Classe Jovens criada: '.$youthClass->id);

            $teenClass = EBDClass::firstOrCreate(
                ['name' => 'Adolescentes'],
                [
                    'age_group' => 'teen',
                    'description' => 'Classe para adolescentes de 13 a 17 anos.',
                    'room' => 'Sala 3',
                    'schedule_time' => '09:00',
                    'max_students' => 25,
                    'is_active' => true,
                    'order' => 3,
                ]
            );
            $this->command->info('Classe Adolescentes criada: '.$teenClass->id);

            $childrenClass = EBDClass::firstOrCreate(
                ['name' => 'Crianças'],
                [
                    'age_group' => 'children',
                    'description' => 'Classe para crianças de 5 a 12 anos.',
                    'room' => 'Sala Infantil',
                    'schedule_time' => '09:00',
                    'max_students' => 40,
                    'is_active' => true,
                    'order' => 4,
                ]
            );
            $this->command->info('Classe Crianças criada: '.$childrenClass->id);

            // Get or create sample users
            $admin = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Administrador',
                    'password' => bcrypt('password'),
                ]
            );

            $teacher1 = User::firstOrCreate(
                ['email' => 'professor1@example.com'],
                [
                    'name' => 'Professor João Silva',
                    'password' => bcrypt('password'),
                ]
            );

            $teacher2 = User::firstOrCreate(
                ['email' => 'professor2@example.com'],
                [
                    'name' => 'Professora Maria Santos',
                    'password' => bcrypt('password'),
                ]
            );

            // Assign teachers to classes (use firstOrCreate to avoid duplicates)
            EBDTeacher::firstOrCreate(
                ['user_id' => $teacher1->id, 'class_id' => $adultClass->id],
                [
                    'role' => 'teacher',
                    'start_date' => now()->subMonths(6),
                    'is_active' => true,
                ]
            );

            EBDTeacher::firstOrCreate(
                ['user_id' => $teacher2->id, 'class_id' => $youthClass->id],
                [
                    'role' => 'teacher',
                    'start_date' => now()->subMonths(3),
                    'is_active' => true,
                ]
            );

            // Create sample students (using existing users or creating new ones)
            $students = User::where('email', '!=', 'admin@example.com')
                ->where('email', '!=', 'professor1@example.com')
                ->where('email', '!=', 'professor2@example.com')
                ->limit(10)
                ->get();

            if ($students->isEmpty()) {
                // Create sample students
                for ($i = 1; $i <= 10; $i++) {
                    $student = User::firstOrCreate(
                        ['email' => "aluno{$i}@example.com"],
                        [
                            'name' => "Aluno {$i}",
                            'password' => bcrypt('password'),
                        ]
                    );

                    // Enroll in classes
                    if ($i <= 5) {
                        EBDStudent::firstOrCreate(
                            ['user_id' => $student->id, 'class_id' => $adultClass->id],
                            [
                                'enrollment_date' => now()->subMonths(rand(1, 6)),
                                'is_active' => true,
                            ]
                        );
                    } else {
                        EBDStudent::firstOrCreate(
                            ['user_id' => $student->id, 'class_id' => $youthClass->id],
                            [
                                'enrollment_date' => now()->subMonths(rand(1, 3)),
                                'is_active' => true,
                            ]
                        );
                    }
                }
            } else {
                // Enroll existing users
                foreach ($students->take(5) as $index => $student) {
                    EBDStudent::firstOrCreate(
                        ['user_id' => $student->id, 'class_id' => $adultClass->id],
                        [
                            'enrollment_date' => now()->subMonths(rand(1, 6)),
                            'is_active' => true,
                        ]
                    );
                }

                foreach ($students->skip(5)->take(5) as $student) {
                    EBDStudent::firstOrCreate(
                        ['user_id' => $student->id, 'class_id' => $youthClass->id],
                        [
                            'enrollment_date' => now()->subMonths(rand(1, 3)),
                            'is_active' => true,
                        ]
                    );
                }
            }

            // Create sample lessons for adult class
            $nextSunday = Carbon::now()->next(Carbon::SUNDAY);

            for ($week = 0; $week < 4; $week++) {
                $lessonDate = $nextSunday->copy()->addWeeks($week);
                $title = '1 Coríntios - Capítulo '.($week + 1);

                EBDLesson::firstOrCreate(
                    ['class_id' => $adultClass->id, 'lesson_date' => $lessonDate->format('Y-m-d'), 'title' => $title],
                    [
                        'description' => 'Estudo do capítulo '.($week + 1).' da primeira carta aos Coríntios.',
                        'lesson_date' => $lessonDate,
                        'lesson_time' => $lessonDate->copy()->setTime(9, 0),
                        'bible_book' => '1 Coríntios',
                        'bible_chapter' => $week + 1,
                        'bible_verses' => '1-31',
                        'bible_version' => 'nvi',
                        'objective' => 'Compreender os principais ensinamentos do capítulo '.($week + 1).' de 1 Coríntios.',
                        'introduction' => 'Nesta lição, estudaremos o capítulo '.($week + 1).' da primeira carta do apóstolo Paulo aos Coríntios.',
                        'development' => 'Desenvolvimento completo da lição com explicação dos versículos principais.',
                        'conclusion' => 'Resumo dos pontos principais aprendidos nesta lição.',
                        'application' => 'Como aplicar estes ensinamentos na vida prática diária.',
                        'status' => $week === 0 ? 'scheduled' : ($week < 2 ? 'in_progress' : 'completed'),
                        'created_by' => $admin->id,
                    ]
                );
            }

            // Create sample lessons for youth class
            for ($week = 0; $week < 2; $week++) {
                $lessonDate = $nextSunday->copy()->addWeeks($week);
                $title = 'Romanos - Capítulo '.($week + 1);

                EBDLesson::firstOrCreate(
                    ['class_id' => $youthClass->id, 'lesson_date' => $lessonDate->format('Y-m-d'), 'title' => $title],
                    [
                        'description' => 'Estudo do capítulo '.($week + 1).' da carta aos Romanos.',
                        'lesson_date' => $lessonDate,
                        'lesson_time' => $lessonDate->copy()->setTime(9, 0),
                        'bible_book' => 'Romanos',
                        'bible_chapter' => $week + 1,
                        'bible_verses' => '1-32',
                        'bible_version' => 'nvi',
                        'objective' => 'Compreender os principais ensinamentos do capítulo '.($week + 1).' de Romanos.',
                        'introduction' => 'Nesta lição, estudaremos o capítulo '.($week + 1).' da carta do apóstolo Paulo aos Romanos.',
                        'development' => 'Desenvolvimento completo da lição com explicação dos versículos principais.',
                        'conclusion' => 'Resumo dos pontos principais aprendidos nesta lição.',
                        'application' => 'Como aplicar estes ensinamentos na vida prática diária.',
                        'status' => $week === 0 ? 'scheduled' : 'in_progress',
                        'created_by' => $admin->id,
                    ]
                );
            }

            // Seed Games and Quiz Questions
            $this->call(GameSeeder::class);
            $this->call(GameDataSeeder::class);

            // Gamification: XP rules and achievements (500+)
            if (\Illuminate\Support\Facades\Schema::hasTable('ebd_xp_rules')) {
                $this->call(EbdXpRulesSeeder::class);
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('ebd_achievements')) {
                $this->call(EbdAchievementsSeeder::class);
            }

            // Ano letivo completo 22/02/2026 a 31/12/2026: lições, materiais, vídeos, questões, avaliações
            $this->call(EbdFullYearConsolidatedSeeder::class);

            $this->command->info('EBD seeder executado com sucesso!');
            $this->command->info('Total de Classes: '.EBDClass::count());
            $this->command->info('Total de Professores: '.EBDTeacher::count());
            $this->command->info('Total de Alunos: '.EBDStudent::count());
            $this->command->info('Total de Lições: '.EBDLesson::count());
        } catch (\Exception $e) {
            $this->command->error('Erro ao executar o seeder: '.$e->getMessage());
            $this->command->error('Arquivo: '.$e->getFile().':'.$e->getLine());
            throw $e;
        }
    }
}
