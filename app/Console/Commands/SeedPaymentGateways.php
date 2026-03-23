<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\PaymentGateway\App\Models\PaymentGateway;

class SeedPaymentGateways extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment-gateways:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed payment gateways (Stripe, Mercado Pago, PIX, etc)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding payment gateways...');

        $gateways = [
            [
                'name' => 'stripe',
                'display_name' => 'Stripe',
                'description' => 'Gateway de pagamento internacional com suporte a cartões de crédito e débito',
                'icon' => 'credit-card',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => [],
                'settings' => [],
                'sort_order' => 1,
            ],
            [
                'name' => 'mercado_pago',
                'display_name' => 'Mercado Pago',
                'description' => 'Gateway brasileiro com suporte a PIX, cartões e boleto',
                'icon' => 'credit-card',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => [],
                'settings' => [],
                'sort_order' => 2,
            ],
            [
                'name' => 'pix',
                'display_name' => 'PIX',
                'description' => 'Pagamento instantâneo via PIX',
                'icon' => 'credit-card',
                'is_active' => false,
                'is_test_mode' => false,
                'credentials' => [],
                'settings' => [],
                'sort_order' => 3,
            ],
            [
                'name' => 'credit_card',
                'display_name' => 'Cartão de Crédito',
                'description' => 'Pagamento via cartão de crédito (usa Stripe ou Mercado Pago como backend)',
                'icon' => 'credit-card',
                'is_active' => false,
                'is_test_mode' => true,
                'credentials' => [],
                'settings' => [],
                'sort_order' => 4,
            ],
        ];

        foreach ($gateways as $gatewayData) {
            PaymentGateway::updateOrCreate(
                ['name' => $gatewayData['name']],
                $gatewayData
            );
        }

        $count = PaymentGateway::count();
        $this->info("Payment gateways seeded successfully! Total: {$count}");

        return Command::SUCCESS;
    }
}
