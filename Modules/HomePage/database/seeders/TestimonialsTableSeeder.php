<?php

namespace Modules\HomePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HomePage\App\Models\Testimonial;

class TestimonialsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Idempotente: insere apenas o que falta; nunca apaga o que já existe.
        $testimonials = [
            [
                'name' => 'Maria Silva',
                'photo' => null,
                'testimonial' => 'Há 5 anos, encontrei nesta igreja não apenas uma comunidade, mas uma família. O amor de Cristo se manifesta aqui de forma real e transformadora.',
                'position' => 'Membro Ativo',
                'is_active' => true,
                'order' => 1,
                'created_by' => 1,
            ],
            [
                'name' => 'João Santos',
                'photo' => null,
                'testimonial' => 'Participar dos ministérios desta igreja mudou completamente minha vida. Aqui aprendi o verdadeiro significado de servir ao próximo.',
                'position' => 'Líder de Ministério',
                'is_active' => true,
                'order' => 2,
                'created_by' => 1,
            ],
            [
                'name' => 'Ana Costa',
                'photo' => null,
                'testimonial' => 'Através dos estudos bíblicos e do acompanhamento pastoral, encontrei direção e propósito para minha vida. Sou grata por fazer parte desta comunidade.',
                'position' => 'Membro',
                'is_active' => true,
                'order' => 3,
                'created_by' => 1,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(
                ['name' => $t['name'], 'position' => $t['position']],
                $t
            );
        }
    }
}
