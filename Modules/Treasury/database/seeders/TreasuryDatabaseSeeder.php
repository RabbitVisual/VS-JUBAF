<?php

namespace Modules\Treasury\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Treasury\App\Models\Campaign;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\FinancialGoal;
use Modules\Treasury\App\Models\TreasuryPermission;

class TreasuryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seed do módulo Treasury...');

        // 1. Criar permissões para usuários
        $this->seedPermissions();

        // 2. Criar campanhas de exemplo
        $campaigns = $this->seedCampaigns();

        // 3. Criar metas financeiras
        $this->seedFinancialGoals($campaigns);

        // 4. Criar entradas financeiras de exemplo
        $this->seedFinancialEntries($campaigns);

        $this->command->info('✅ Seed do módulo Treasury concluído!');
    }

    /**
     * Seed de permissões
     */
    private function seedPermissions(): void
    {
        $this->command->info('  📋 Criando permissões...');

        $users = User::limit(5)->get();

        foreach ($users as $index => $user) {
            $permissionLevel = $index === 0 ? 'admin' : ($index === 1 ? 'editor' : 'viewer');

            TreasuryPermission::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'permission_level' => $permissionLevel,
                    'can_view_reports' => true,
                    'can_create_entries' => $permissionLevel !== 'viewer',
                    'can_edit_entries' => $permissionLevel !== 'viewer',
                    'can_delete_entries' => $permissionLevel === 'admin',
                    'can_manage_campaigns' => $permissionLevel === 'admin',
                    'can_manage_goals' => $permissionLevel === 'admin',
                    'can_export_data' => $permissionLevel !== 'viewer',
                ]
            );
        }

        $this->command->info("    ✅ {$users->count()} permissões criadas");
    }

    /**
     * Seed de campanhas
     */
    private function seedCampaigns(): array
    {
        $this->command->info('  🎯 Criando campanhas...');

        $campaigns = [
            [
                'name' => 'Reforma do Templo',
                'slug' => 'reforma-do-templo',
                'description' => 'Campanha para arrecadar recursos para a reforma completa do templo, incluindo melhorias na estrutura, iluminação e acústica.',
                'target_amount' => 500000.00,
                'current_amount' => 0,
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
                'is_active' => true,
            ],
            [
                'name' => 'Novo Sistema de Som',
                'slug' => 'novo-sistema-de-som',
                'description' => 'Arrecadação para aquisição de equipamentos de áudio profissionais para melhorar a qualidade do som durante os cultos.',
                'target_amount' => 80000.00,
                'current_amount' => 0,
                'start_date' => now()->subMonth(),
                'end_date' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'name' => 'Projeto Missionário África',
                'slug' => 'projeto-missionario-africa',
                'description' => 'Suporte financeiro para o projeto missionário na África, incluindo construção de igrejas e treinamento de líderes locais.',
                'target_amount' => 200000.00,
                'current_amount' => 0,
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(12),
                'is_active' => true,
            ],
            [
                'name' => 'Ajuda Social - Comunidade',
                'slug' => 'ajuda-social-comunidade',
                'description' => 'Campanha permanente para ajudar famílias carentes da comunidade com alimentos, roupas e assistência básica.',
                'target_amount' => null,
                'current_amount' => 0,
                'start_date' => now()->subYear(),
                'end_date' => null,
                'is_active' => true,
            ],
        ];

        $createdCampaigns = [];
        foreach ($campaigns as $campaignData) {
            $campaign = Campaign::updateOrCreate(
                ['slug' => $campaignData['slug']],
                $campaignData
            );
            $createdCampaigns[] = $campaign;
        }

        $this->command->info('    ✅ '.count($createdCampaigns).' campanhas criadas');

        return $createdCampaigns;
    }

    /**
     * Seed de metas financeiras
     */
    private function seedFinancialGoals(array $campaigns): void
    {
        $this->command->info('  🎯 Criando metas financeiras...');

        $goals = [
            [
                'name' => 'Meta Mensal de Dízimos',
                'description' => 'Meta de arrecadação mensal de dízimos para o mês atual',
                'type' => 'monthly',
                'target_amount' => 50000.00,
                'current_amount' => 0,
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'category' => 'tithe',
                'campaign_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Meta Anual de Ofertas',
                'description' => 'Meta de arrecadação anual de ofertas especiais',
                'type' => 'yearly',
                'target_amount' => 300000.00,
                'current_amount' => 0,
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'category' => 'offering',
                'campaign_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Meta da Campanha - Reforma do Templo',
                'description' => 'Meta específica para a campanha de reforma do templo',
                'type' => 'campaign',
                'target_amount' => 500000.00,
                'current_amount' => 0,
                'start_date' => $campaigns[0]->start_date ?? now()->subMonths(2),
                'end_date' => $campaigns[0]->end_date ?? now()->addMonths(10),
                'category' => 'campaign',
                'campaign_id' => $campaigns[0]->id ?? null,
                'is_active' => true,
            ],
        ];

        foreach ($goals as $goalData) {
            FinancialGoal::updateOrCreate(
                [
                    'name' => $goalData['name'],
                    'start_date' => $goalData['start_date'],
                    'end_date' => $goalData['end_date'],
                ],
                $goalData
            );
        }

        $this->command->info('    ✅ '.count($goals).' metas criadas');
    }

    /**
     * Seed de entradas financeiras
     */
    private function seedFinancialEntries(array $campaigns): void
    {
        // Evita criar um grande volume de lançamentos duplicados em re-execuções
        if (FinancialEntry::count() > 0) {
            $this->command->info('  💰 Entradas financeiras já existentes, pulando criação de lançamentos de exemplo.');

            // Garante que os totais das campanhas sejam atualizados mesmo quando não criamos novas entradas
            foreach ($campaigns as $campaign) {
                $campaign->updateCurrentAmount();
            }

            return;
        }

        $this->command->info('  💰 Criando entradas financeiras...');

        $users = User::limit(10)->get();
        if ($users->isEmpty()) {
            $this->command->warn('    ⚠️  Nenhum usuário encontrado. Pulando criação de entradas.');

            return;
        }

        $categories = [
            'income' => ['tithe', 'offering', 'donation', 'ministry_donation', 'campaign'],
            'expense' => ['maintenance', 'utilities', 'salary', 'equipment', 'event', 'other'],
        ];

        $paymentMethods = ['cash', 'transfer', 'pix', 'credit_card', 'debit_card', 'check'];

        // Criar entradas dos últimos 6 meses
        $entriesCreated = 0;
        for ($month = 5; $month >= 0; $month--) {
            $date = now()->subMonths($month);
            $daysInMonth = $date->daysInMonth;

            // Receitas (mais frequentes)
            for ($i = 0; $i < rand(15, 30); $i++) {
                $category = $categories['income'][array_rand($categories['income'])];
                $amount = $category === 'tithe' ? rand(10000, 50000) : rand(500, 5000);
                $amount = $amount / 100; // Converter centavos para reais

                FinancialEntry::create([
                    'type' => 'income',
                    'category' => $category,
                    'title' => $this->getEntryTitle('income', $category),
                    'description' => $this->getEntryDescription('income', $category),
                    'amount' => $amount,
                    'entry_date' => $date->copy()->day(rand(1, $daysInMonth)),
                    'user_id' => $users->random()->id,
                    'campaign_id' => $category === 'campaign' && ! empty($campaigns) ? $campaigns[array_rand($campaigns)]->id : null,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'reference_number' => 'COMP-'.strtoupper(Str::random(8)),
                ]);
                $entriesCreated++;
            }

            // Despesas (menos frequentes)
            for ($i = 0; $i < rand(5, 15); $i++) {
                $category = $categories['expense'][array_rand($categories['expense'])];
                $amount = $category === 'salary' ? rand(20000, 80000) : rand(500, 10000);
                $amount = $amount / 100;

                FinancialEntry::create([
                    'type' => 'expense',
                    'category' => $category,
                    'title' => $this->getEntryTitle('expense', $category),
                    'description' => $this->getEntryDescription('expense', $category),
                    'amount' => $amount,
                    'entry_date' => $date->copy()->day(rand(1, $daysInMonth)),
                    'user_id' => $users->random()->id,
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'reference_number' => 'DESP-'.strtoupper(Str::random(8)),
                ]);
                $entriesCreated++;
            }
        }

        // Atualizar valores das campanhas
        foreach ($campaigns as $campaign) {
            $campaign->updateCurrentAmount();
        }

        $this->command->info("    ✅ {$entriesCreated} entradas financeiras criadas");
    }

    /**
     * Gera título para entrada financeira
     */
    private function getEntryTitle(string $type, string $category): string
    {
        $titles = [
            'income' => [
                'tithe' => ['Dízimo', 'Dízimo Mensal', 'Dízimo de Oferta'],
                'offering' => ['Oferta', 'Oferta Especial', 'Oferta de Gratidão'],
                'donation' => ['Doação', 'Doação Especial', 'Contribuição'],
                'ministry_donation' => ['Doação para Ministério', 'Contribuição Ministerial'],
                'campaign' => ['Doação para Campanha', 'Contribuição Campanha'],
            ],
            'expense' => [
                'maintenance' => ['Manutenção', 'Reparo', 'Conserto'],
                'utilities' => ['Conta de Luz', 'Conta de Água', 'Internet', 'Telefone'],
                'salary' => ['Salário', 'Remuneração', 'Pagamento'],
                'equipment' => ['Equipamento', 'Compra de Equipamento'],
                'event' => ['Evento', 'Organização de Evento'],
                'other' => ['Outras Despesas', 'Despesa Diversa'],
            ],
        ];

        $options = $titles[$type][$category] ?? ['Entrada Financeira'];

        return $options[array_rand($options)];
    }

    /**
     * Gera descrição para entrada financeira
     */
    private function getEntryDescription(string $type, string $category): ?string
    {
        $descriptions = [
            'income' => [
                'tithe' => 'Dízimo recebido de membro da igreja',
                'offering' => 'Oferta especial recebida',
                'donation' => 'Doação recebida',
                'ministry_donation' => 'Doação específica para ministério',
                'campaign' => 'Contribuição para campanha',
            ],
            'expense' => [
                'maintenance' => 'Manutenção e reparos',
                'utilities' => 'Pagamento de contas',
                'salary' => 'Pagamento de salários',
                'equipment' => 'Aquisição de equipamentos',
                'event' => 'Custos de eventos',
                'other' => 'Outras despesas operacionais',
            ],
        ];

        return $descriptions[$type][$category] ?? null;
    }
}
