<?php

namespace Modules\Treasury\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Treasury\App\Models\FinancialCategory;

class TreasuryCategoriesSeeder extends Seeder
{
    /**
     * JUBAF: Plano de contas padrão para Associação de Jovens.
     */
    public function run(): void
    {
        $categories = [
            // Receitas
            ['type' => 'income', 'slug' => 'taxa_filiacao', 'name' => 'Taxa de Filiação (Igrejas)', 'description' => 'Anuidade das igrejas filiadas', 'order' => 1],
            ['type' => 'income', 'slug' => 'inscricoes_eventos', 'name' => 'Inscrições de Eventos', 'description' => 'Receitas com inscrições (Congressos, Acampamentos)', 'order' => 2],
            ['type' => 'income', 'slug' => 'patrocinios_doacoes', 'name' => 'Patrocínios/Doações', 'description' => 'Patrocínios ou doações voluntárias', 'order' => 3],
            ['type' => 'income', 'slug' => 'cantina_lojinha', 'name' => 'Cantina/Lojinha', 'description' => 'Vendas em eventos e cantina', 'order' => 4],
            ['type' => 'income', 'slug' => 'other', 'name' => 'Outros', 'description' => 'Outras receitas', 'order' => 5],
            
            // Despesas
            ['type' => 'expense', 'slug' => 'custos_eventos', 'name' => 'Custos com Eventos', 'description' => 'Estrutura, preletores, alimentação em eventos', 'order' => 1],
            ['type' => 'expense', 'slug' => 'despesas_administrativas', 'name' => 'Despesas Administrativas', 'description' => 'Impostos, taxas, contabilidade, material de escritório', 'order' => 2],
            ['type' => 'expense', 'slug' => 'marketing_comunicacao', 'name' => 'Marketing/Comunicação', 'description' => 'Site, redes sociais, tráfego pago, mídias', 'order' => 3],
            ['type' => 'expense', 'slug' => 'ajuda_custo_missoes', 'name' => 'Ajuda de Custo/Missões', 'description' => 'Apoio a projetos missionários ou voluntários', 'order' => 4],
            ['type' => 'expense', 'slug' => 'other', 'name' => 'Outros', 'description' => 'Outras despesas', 'order' => 5],
        ];

        foreach ($categories as $data) {
            FinancialCategory::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_system' => true])
            );
        }
    }
}
