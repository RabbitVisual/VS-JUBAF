<?php

/**
 * Autor: Reinan Rodrigues
 * Empresa: Vertex Solutions LTDA © 2026
 * Email: r.rodriguesjs@gmail.com
 *
 * VertexCBAV (igreja): apenas seeders gospel e gamificação de igreja.
 * Seeders financeiros (Vertex Pro) foram removidos do projeto.
 */

namespace Modules\Gamification\Database\Seeders;

use Illuminate\Database\Seeder;

class GamificationDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            GospelInsightsSeeder::class,
            GospelCoachingRulesSeeder::class,
            PageGospelInsightsSeeder::class,
        ]);
    }
}
