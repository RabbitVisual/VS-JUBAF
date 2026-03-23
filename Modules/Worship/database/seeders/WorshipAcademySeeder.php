<?php

namespace Modules\Worship\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyModule;
use Modules\Worship\App\Models\AcademyLesson;
use Modules\Worship\App\Models\AcademyEnrollment;
use Modules\Worship\App\Models\AcademyProgress;
use App\Models\User;
use Illuminate\Support\Str;

class WorshipAcademySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first(); // Assuming admin exists, ID 1
        $instrument = \Illuminate\Support\Facades\DB::table('worship_instruments')->first();

        if (!$instrument) {
            // Create fallback instrument if none
             $instId = \Illuminate\Support\Facades\DB::table('worship_instruments')->insertGetId([
                'name' => 'Violão',
                'slug' => 'violao',
                'icon' => 'guitar',
                'type' => 'Cordas',
                'created_at' => now(),
                'updated_at' => now(),
             ]);
        } else {
            $instId = $instrument->id;
        }

        // 1. Create a Course
        $course = AcademyCourse::firstOrCreate(
            ['slug' => 'fundamentos-worship'],
            [
                'title' => 'Fundamentos do Worship',
                'instrument_id' => $instId,
                'level' => 'Iniciante',
                'description' => 'Um curso completo para iniciar sua jornada no ministério de louvor.',
                'cover_image' => 'https://images.unsplash.com/photo-1511379938547-c1f69419868d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1740&q=80',
                'instructor_id' => $admin->id ?? 1,
                'status' => 'published',
            ]
        );

        // 2. Create Modules & Lessons
        $modules = [
            'Módulo 1: O Coração do Adorador' => [
                ['title' => 'O que é Adoração?', 'type' => 'video', 'duration' => 15],
                ['title' => 'Santidade e Serviço', 'type' => 'video', 'duration' => 20],
                ['title' => 'Leitura Devocional', 'type' => 'material', 'duration' => 5],
            ],
            'Módulo 2: Prática Musical' => [
                ['title' => 'Dinâmica de Banda', 'type' => 'video', 'duration' => 25],
                ['title' => 'Timbre e Setup', 'type' => 'video', 'duration' => 30],
                ['title' => 'Cifras e Harmonia', 'type' => 'chordpro', 'duration' => 10],
            ]
        ];

        foreach ($modules as $modName => $lessons) {
            $module = $course->modules()->firstOrCreate(
                ['title' => $modName],
                ['order' => 0]
            );

            foreach ($lessons as $index => $less) {
                $l = $module->lessons()->firstOrCreate(
                    ['title' => $less['title']],
                    [
                        'slug' => Str::slug($less['title']) . '-' . uniqid(),
                        'type' => $less['type'],
                        'order' => $index,
                        'duration_minutes' => $less['duration'],
                        'content' => $less['type'] == 'material' ? 'Conteúdo de leitura aqui.' : null,
                        'video_url' => $less['type'] == 'video' ? 'https://www.youtube.com/watch?v=Example' : null,
                    ]
                );
            }
        }

        // 3. Enroll Admin and some fake progress
        AcademyEnrollment::firstOrCreate(
            ['user_id' => $admin->id, 'course_id' => $course->id],
            ['progress_percent' => 50]
        );

        $firstLesson = $course->lessons()->first();
        if ($firstLesson) {
             AcademyProgress::firstOrCreate(
                ['user_id' => $admin->id, 'lesson_id' => $firstLesson->id],
                [
                    'completed_at' => now(),
                    'score' => 100,
                ]
             );
        }
    }
}
