<?php

namespace Modules\HomePage\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HomePage\App\Models\GalleryImage;

class GalleryImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Idempotente: insere apenas o que falta; nunca apaga o que já existe.
        $images = [
            [
                'title' => 'Culto Dominical',
                'description' => 'Momento de adoração e comunhão semanal',
                'image_path' => 'gallery/sample1.jpg',
                'image_url' => null,
                'category' => 'cultos',
                'is_active' => true,
                'order' => 1,
                'created_by' => 1,
            ],
            [
                'title' => 'Batismo',
                'description' => 'Celebração de novas vidas em Cristo',
                'image_path' => 'gallery/sample2.jpg',
                'image_url' => null,
                'category' => 'eventos',
                'is_active' => true,
                'order' => 2,
                'created_by' => 1,
            ],
            [
                'title' => 'Ministério Infantil',
                'description' => 'Crianças aprendendo sobre o amor de Jesus',
                'image_path' => 'gallery/sample3.jpg',
                'image_url' => null,
                'category' => 'ministerios',
                'is_active' => true,
                'order' => 3,
                'created_by' => 1,
            ],
            [
                'title' => 'Grupo de Jovens',
                'description' => 'Comunhão e crescimento espiritual',
                'image_path' => 'gallery/sample4.jpg',
                'image_url' => null,
                'category' => 'ministerios',
                'is_active' => true,
                'order' => 4,
                'created_by' => 1,
            ],
            [
                'title' => 'Conferência',
                'description' => 'Momento de aprendizado e edificação',
                'image_path' => 'gallery/sample5.jpg',
                'image_url' => null,
                'category' => 'eventos',
                'is_active' => true,
                'order' => 5,
                'created_by' => 1,
            ],
            [
                'title' => 'Comunhão',
                'description' => 'Laços de amizade e amor fraternal',
                'image_path' => 'gallery/sample6.jpg',
                'image_url' => null,
                'category' => 'comunidade',
                'is_active' => true,
                'order' => 6,
                'created_by' => 1,
            ],
            [
                'title' => 'Louvor',
                'description' => 'Adoração através da música',
                'image_path' => 'gallery/sample7.jpg',
                'image_url' => null,
                'category' => 'cultos',
                'is_active' => true,
                'order' => 7,
                'created_by' => 1,
            ],
            [
                'title' => 'Família',
                'description' => 'Fé, amor e unidade familiar',
                'image_path' => 'gallery/sample8.jpg',
                'image_url' => null,
                'category' => 'comunidade',
                'is_active' => true,
                'order' => 8,
                'created_by' => 1,
            ],
        ];

        foreach ($images as $img) {
            GalleryImage::firstOrCreate(
                ['title' => $img['title'], 'category' => $img['category'], 'created_by' => $img['created_by']],
                $img
            );
        }
    }
}
