<?php

namespace Modules\Treasury\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Treasury\App\Models\FinancialCategory;

class TreasuryCategoriesSeeder extends Seeder
{
    /**
     * CBAV2026: Plano de contas padrão batista.
     */
    public function run(): void
    {
        $categories = [
            // Receitas
            ['type' => 'income', 'slug' => 'tithe', 'name' => 'Dízimos', 'description' => 'Dízimos dos membros', 'order' => 1],
            ['type' => 'income', 'slug' => 'offering', 'name' => 'Ofertas Alçadas', 'description' => 'Ofertas regulares', 'order' => 2],
            ['type' => 'income', 'slug' => 'offering_missions_national', 'name' => 'Ofertas de Missões - Nacional', 'description' => 'Contribuição missionária nacional', 'order' => 3],
            ['type' => 'income', 'slug' => 'offering_missions_state', 'name' => 'Ofertas de Missões - Estadual', 'description' => 'Contribuição missionária estadual', 'order' => 4],
            ['type' => 'income', 'slug' => 'offering_missions_world', 'name' => 'Ofertas de Missões - Mundial', 'description' => 'Contribuição missionária mundial', 'order' => 5],
            ['type' => 'income', 'slug' => 'construction_fund', 'name' => 'Fundo de Construção', 'description' => 'Recursos para obras', 'order' => 6],
            ['type' => 'income', 'slug' => 'donation', 'name' => 'Doações', 'description' => 'Doações gerais', 'order' => 7],
            ['type' => 'income', 'slug' => 'ministry_donation', 'name' => 'Doação para Ministério', 'description' => 'Doação vinculada a ministério', 'order' => 8],
            ['type' => 'income', 'slug' => 'campaign', 'name' => 'Campanha', 'description' => 'Entradas de campanhas', 'order' => 9],
            ['type' => 'income', 'slug' => 'other', 'name' => 'Outros', 'description' => 'Outras receitas', 'order' => 10],
            // Despesas
            ['type' => 'expense', 'slug' => 'preachers', 'name' => 'Preletores', 'description' => 'Honorários e despesas de preletores', 'order' => 1],
            ['type' => 'expense', 'slug' => 'maintenance', 'name' => 'Manutenção', 'description' => 'Manutenção e reparos', 'order' => 2],
            ['type' => 'expense', 'slug' => 'social_action', 'name' => 'Ação Social', 'description' => 'Programas de ação social', 'order' => 3],
            ['type' => 'expense', 'slug' => 'christian_education', 'name' => 'Educação Cristã', 'description' => 'EBD, materiais, formação', 'order' => 4],
            ['type' => 'expense', 'slug' => 'salary_benefits', 'name' => 'Salários e Encargos', 'description' => 'Salários e encargos trabalhistas', 'order' => 5],
            ['type' => 'expense', 'slug' => 'utilities', 'name' => 'Contas e Utilidades', 'description' => 'Água, luz, telefone, internet', 'order' => 6],
            ['type' => 'expense', 'slug' => 'equipment', 'name' => 'Equipamentos', 'description' => 'Aquisição de equipamentos', 'order' => 7],
            ['type' => 'expense', 'slug' => 'event', 'name' => 'Eventos', 'description' => 'Custos de eventos', 'order' => 8],
            ['type' => 'expense', 'slug' => 'denominational_contribution', 'name' => 'Contribuição Denominacional', 'description' => 'Plano Cooperativo / Convenção', 'order' => 9],
            ['type' => 'expense', 'slug' => 'other', 'name' => 'Outros', 'description' => 'Outras despesas', 'order' => 10],
        ];

        foreach ($categories as $data) {
            FinancialCategory::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['is_system' => true])
            );
        }
    }
}
