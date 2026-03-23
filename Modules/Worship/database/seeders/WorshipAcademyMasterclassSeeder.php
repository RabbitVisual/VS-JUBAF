<?php

namespace Modules\Worship\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\WorshipTeamRole;

class WorshipAcademyMasterclassSeeder extends Seeder
{
    /**
     * Creates a fully fledged, production-ready Worship Academy Course
     * to demonstrate the end-to-end capabilities of the LMS.
     */
    public function run(): void
    {
        $admin = User::first() ?? User::factory()->create();

        // Ensure a target role exists
        $role = WorshipTeamRole::firstOrCreate(
            ['name' => 'Líder de Louvor'],
            ['color' => '#8b5cf6', 'description' => 'Ministros, líderes de banda e arranjadores.']
        );

        // Ensure a generic instrument exists to attach
        $instrument = DB::table('worship_instruments')->where('slug', 'geral')->first();
        if (! $instrument) {
            $instId = DB::table('worship_instruments')->insertGetId([
                'name' => 'Prática de Banda (Geral)',
                'slug' => 'geral',
                'icon' => 'music',
                'category_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $instId = $instrument->id;
        }

        // 1. Create the Masterclass Course
        $course = AcademyCourse::firstOrCreate(
            ['slug' => 'masterclass-adoracao-vertex'],
            [
                'title' => 'Masterclass: Liderança e Excelência na Adoração',
                'instrument_id' => $instId,
                'worship_team_role_id' => $role->id,
                'level' => 'Avançado',
                'difficulty_level' => 'Intermediário a Avançado',
                'description' => '<p>Bem-vindo à <strong>Masterclass de Excelência na Adoração</strong>. Este curso intensivo foi desenhado para elevar o padrão técnico, espiritual e de liderança da sua equipe de louvor. Através de módulos práticos e teológicos, você aprenderá sobre dinâmica de banda, estruturação de ensaios, saúde vocal e como utilizar a tecnologia (condução, multitracks e VS) a favor do culto.</p><ul><li>Acesso a materiais de apoio exclusivos em PDF.</li><li>Visão Multi-Câmera para imersão técnica.</li><li>Desenvolvimento de repertório dinâmico.</li></ul>',
                'cover_image' => 'https://images.unsplash.com/photo-1501612780327-45045538702b?q=80&w=2070&auto=format&fit=crop',
                'instructor_id' => $admin->id,
                'status' => 'published',
            ]
        );

        // 2. Define the Curriculum
        $curriculum = [
            'Módulo 1: Visão e Coração da Adoração' => [
                [
                    'title' => 'O Papel do Levita na Igreja Local',
                    'type' => 'video',
                    'duration' => 15,
                    'video_url' => 'https://www.youtube.com/watch?v=1bPEq4c1lP4', // Hillsong Creative generic
                    'content' => 'Nesta aula, abordamos a teologia do serviço e como alinhar as expectativas musicais com a visão pastoral real do seu ministério.',
                    'pdf_path' => null,
                ],
                [
                    'title' => 'Técnica versus Espiritualidade',
                    'type' => 'video',
                    'duration' => 20,
                    'video_url' => 'https://www.youtube.com/watch?v=M5z2FfDBAEY',
                    'content' => 'Como equilibrar horas de estúdio e ensaio com profunda intimidade com Deus no quarto secreto.',
                    'pdf_path' => 'storage/materials/apostila-modulo1.pdf',
                ],
            ],
            'Módulo 2: A Arte da Música e Dinâmica de Banda' => [
                [
                    'title' => 'Construindo um Arranjo Completo',
                    'type' => 'video',
                    'duration' => 35,
                    'video_url' => 'https://www.youtube.com/watch?v=yJg-Y5byzP1',
                    'multicam_video_url' => 'https://www.youtube.com/watch?v=multicam_demo', // Shows the multicam feature
                    'content' => 'Entenda o papel da cozinha (Baixo e Bateria) versus elementos harmônicos (Teclas e Guitarras). Não toque o tempo todo!',
                    'pdf_path' => null,
                ],
                [
                    'title' => 'Espaçamento e Volume',
                    'type' => 'video',
                    'duration' => 18,
                    'video_url' => 'https://www.youtube.com/watch?v=qT4_L2k7vXQ',
                    'content' => 'Tocar menos é tocar mais. Aprenda a trabalhar com pads, texturas e crescentes.',
                    'pdf_path' => null,
                ],
            ],
            'Módulo 3: Ferramentas e Tecnologia' => [
                [
                    'title' => 'Usando Metrônomo e Multitracks (VS)',
                    'type' => 'video',
                    'duration' => 28,
                    'video_url' => 'https://www.youtube.com/watch?v=kY8_6tJmO6w',
                    'content' => 'Como a sua banda pode transicionar para o uso de cliques e guias (Cues) no palco usando Ableton Live ou Playback.',
                    'sheet_music_pdf' => 'storage/materials/guia-clique.pdf',
                ],
                [
                    'title' => 'Passagem de Som Eficiente',
                    'type' => 'video',
                    'duration' => 12,
                    'video_url' => 'https://www.youtube.com/watch?v=fV7iHlWl1pU',
                    'content' => 'Checlist de passagem de som. Comunicação limpa entre altar e a House of Mix (House of Worship).',
                    'pdf_path' => null,
                ],
            ],
        ];

        // 3. Insert Modules and Lessons
        $orderMod = 1;
        foreach ($curriculum as $modName => $lessons) {
            $module = $course->modules()->firstOrCreate(
                ['title' => $modName],
                ['order' => $orderMod]
            );
            $orderMod++;

            $orderLes = 1;
            foreach ($lessons as $less) {
                // Ensure unique slug
                $slug = Str::slug($course->slug.'-'.$less['title']).'-'.rand(100, 999);

                $lesson = $module->lessons()->firstOrCreate(
                    ['title' => $less['title']],
                    [
                        'slug' => $slug,
                        'type' => $less['type'],
                        'order' => $orderLes,
                        'duration_minutes' => $less['duration'],
                        'content' => $less['content'],
                        'video_url' => $less['video_url'] ?? null,
                        'multicam_video_url' => $less['multicam_video_url'] ?? null,
                        'pdf_path' => $less['pdf_path'] ?? null,
                        'sheet_music_pdf' => $less['sheet_music_pdf'] ?? null,
                    ]
                );
                $orderLes++;
            }
        }

        // 4. Output Success
        $this->command->info('✅ WorshipAcademyMasterclassSeeder executado com sucesso! Curso "Masterclass de Excelência" adicionado.');
    }
}
