<?php

namespace Modules\Treasury\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Treasury\App\Models\FinancialFund;

class TreasuryFundsSeeder extends Seeder
{
    /**
     * CBAV2026: Fundos padrão (centros de custo).
     */
    public function run(): void
    {
        $funds = [
            ['name' => 'Caixa Geral', 'slug' => 'caixa-geral', 'description' => 'Caixa principal da igreja', 'is_restricted' => false],
            ['name' => 'Fundo de Missões', 'slug' => 'fundo-missoes', 'description' => 'Verba para missões', 'is_restricted' => true],
            ['name' => 'Fundo de Construção', 'slug' => 'fundo-construcao', 'description' => 'Recursos para obras', 'is_restricted' => true],
        ];

        foreach ($funds as $data) {
            FinancialFund::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
