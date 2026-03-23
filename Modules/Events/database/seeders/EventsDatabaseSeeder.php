<?php

namespace Modules\Events\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventPriceRule;

class EventsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(EventTypesSeeder::class);

        // Get first user or create a default one
        $user = User::first();

        if (! $user) {
            $this->command->warn('Nenhum usuário encontrado. Crie um usuário primeiro.');

            return;
        }

        // Create example event: Retiro de Jovens
        $event = Event::firstOrCreate(
            ['slug' => 'retiro-jovens-2025'],
            [
                'title' => 'Retiro de Jovens 2025',
                'description' => 'Um fim de semana inesquecível de comunhão, adoração e aprendizado. Venha conosco para este retiro especial onde vamos fortalecer nossa fé e criar memórias duradouras.',
                'banner_path' => null,
                'start_date' => Carbon::now()->addMonths(2)->startOfWeek(Carbon::FRIDAY)->setTime(18, 0),
                'end_date' => Carbon::now()->addMonths(2)->startOfWeek(Carbon::FRIDAY)->addDays(2)->setTime(18, 0),
                'location' => 'Acampamento Monte Carmelo, Serra da Mantiqueira - SP',
                'location_data' => [
                    'address' => 'Rodovia SP-45, Km 125',
                    'city' => 'Camanducaia',
                    'state' => 'MG',
                    'zipcode' => '37650-000',
                ],
                'capacity' => 150,
                'status' => Event::STATUS_PUBLISHED,
                'visibility' => Event::VISIBILITY_BOTH,
                'form_fields' => [
                    [
                        'type' => 'text',
                        'label' => 'Qual sua congregação?',
                        'name' => 'congregation',
                        'required' => false,
                    ],
                    [
                        'type' => 'select',
                        'label' => 'Tamanho da Camiseta',
                        'name' => 'shirt_size',
                        'options' => ['PP', 'P', 'M', 'G', 'GG', 'XG'],
                        'required' => true,
                    ],
                    [
                        'type' => 'textarea',
                        'label' => 'Alergias ou restrições alimentares',
                        'name' => 'allergies',
                        'required' => false,
                    ],
                ],
                'created_by' => $user->id,
            ]
        );

        // Create price rules
        $priceRules = [
            [
                'label' => 'Crianças (0-10 anos)',
                'min_age' => 0,
                'max_age' => 10,
                'price' => 0.00,
                'order' => 1,
            ],
            [
                'label' => 'Adolescentes (11-17 anos)',
                'min_age' => 11,
                'max_age' => 17,
                'price' => 150.00,
                'order' => 2,
            ],
            [
                'label' => 'Adultos (18+ anos)',
                'min_age' => 18,
                'max_age' => null,
                'price' => 300.00,
                'order' => 3,
            ],
        ];

        foreach ($priceRules as $ruleData) {
            EventPriceRule::firstOrCreate(
                [
                    'event_id' => $event->id,
                    'label' => $ruleData['label'],
                ],
                $ruleData
            );
        }

        $this->command->info('Evento exemplo criado com sucesso!');
        $this->command->info('Slug: '.$event->slug);
        $this->command->info('Título: '.$event->title);
        $this->command->info('Regras de preço criadas: '.count($priceRules));
    }
}
