<?php

namespace Modules\Intercessor\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Intercessor\App\Models\PrayerCategory;
use App\Models\Settings;

class IntercessorDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (PrayerCategory::count() === 0) {
            $defaults = [
                ['name' => 'Saúde', 'description' => 'Enfermidades, tratamentos, exames.'],
                ['name' => 'Família', 'description' => 'Casamento, filhos, relacionamentos familiares.'],
                ['name' => 'Finanças', 'description' => 'Emprego, sustento, provisão financeira.'],
                ['name' => 'Ministério', 'description' => 'Chamado, serviço cristão, liderança.'],
                ['name' => 'Disciplina na Palavra', 'description' => 'Pedidos por disciplina e deleite na leitura bíblica (integração com planos de leitura).'],
                ['name' => 'Outros', 'description' => 'Outros pedidos diversos.'],
            ];

            foreach ($defaults as $category) {
                PrayerCategory::create([
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ]);
            }
        }

        // Default settings for local/demo environments
        Settings::set('intercessor_module_enabled', true, 'boolean', 'intercessor');
        Settings::set('intercessor_require_moderation', true, 'boolean', 'intercessor');
        Settings::set('intercessor_allow_comments', true, 'boolean', 'intercessor');
        Settings::set('intercessor_allow_private', true, 'boolean', 'intercessor');
        Settings::set('intercessor_allow_anonymous', true, 'boolean', 'intercessor');
        Settings::set('intercessor_allow_requests', true, 'boolean', 'intercessor');
        Settings::set('intercessor_notification_days', 7, 'integer', 'intercessor');
        Settings::set('intercessor_max_open_requests', 5, 'integer', 'intercessor');
        Settings::set('intercessor_show_intercessor_names', 'author_only', 'string', 'intercessor');
        Settings::set('intercessor_room_label', 'Sala de Oração', 'string', 'intercessor');
    }
}
