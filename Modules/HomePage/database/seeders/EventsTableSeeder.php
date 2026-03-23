<?php

namespace Modules\HomePage\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\HomePage\App\Models\Event;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Idempotente: insere apenas o que falta; nunca apaga o que já existe.
     * Só popula quando a tabela events tem o schema do HomePage (title, order, etc.); se for do módulo Events (slug), pula.
     */
    public function run(): void
    {
        // Tabela do módulo Events tem slug; a do HomePage (root migration) tem order/image. Evita conflito.
        if (Schema::hasColumn('events', 'slug')) {
            return;
        }

        $events = [
            [
                'title' => 'Culto Dominical',
                'description' => 'Celebração semanal de adoração com pregação da Palavra, louvor congregacional e comunhão entre irmãos.',
                'start_date' => Carbon::now()->next('Sunday')->setTime(10, 30, 0),
                'location' => 'Sede Principal',
                'image' => null,
                'is_active' => true,
                'order' => 1,
                'created_by' => 1,
            ],
            [
                'title' => 'Reunião de Oração',
                'description' => 'Momento dedicado à intercessão, estudo bíblico e fortalecimento espiritual da comunidade.',
                'start_date' => Carbon::now()->next('Wednesday')->setTime(19, 0, 0),
                'location' => 'Salão Principal',
                'image' => null,
                'is_active' => true,
                'order' => 2,
                'created_by' => 1,
            ],
            [
                'title' => 'Culto de Jovens',
                'description' => 'Encontro especial para jovens com música contemporânea, palavra direcionada e muita comunhão.',
                'start_date' => Carbon::now()->next('Saturday')->setTime(19, 0, 0),
                'location' => 'Salão de Jovens',
                'image' => null,
                'is_active' => true,
                'order' => 3,
                'created_by' => 1,
            ],
            [
                'title' => 'Batismo',
                'description' => 'Celebração especial dos novos membros que professarão publicamente sua fé em Cristo Jesus.',
                'start_date' => Carbon::now()->addDays(15)->setTime(10, 30, 0),
                'location' => 'Igreja',
                'image' => null,
                'is_active' => true,
                'order' => 4,
                'created_by' => 1,
            ],
            [
                'title' => 'Conferência de Família',
                'description' => 'Evento especial sobre família cristã com palestras, workshops e momentos de comunhão.',
                'start_date' => Carbon::now()->addDays(30)->setTime(19, 0, 0),
                'end_date' => Carbon::now()->addDays(30)->setTime(22, 0, 0),
                'location' => 'Auditório',
                'image' => null,
                'is_active' => true,
                'order' => 5,
                'created_by' => 1,
            ],
        ];

        foreach ($events as $event) {
            Event::firstOrCreate(
                ['title' => $event['title'], 'created_by' => $event['created_by']],
                $event
            );
        }
    }
}
