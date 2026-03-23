<?php

namespace Modules\PaymentGateway\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PaymentGateway\App\Models\PaymentGateway;

class PaymentGatewayDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'stripe',
                'display_name' => 'Stripe',
                'description' => 'Gateway de pagamento internacional com suporte a PIX, Cartões de Crédito e Débito.',
                'icon' => 'credit-card',
                'is_active' => true,
                'is_test_mode' => true,
                'credentials' => [],
                'settings' => [],
                'sort_order' => 1,
            ],
            [
                'name' => 'mercado_pago',
                'display_name' => 'Mercado Pago',
                'description' => 'Gateway brasileiro com suporte a PIX, Cartões de Crédito e Débito.',
                'icon' => 'credit-card',
                'is_active' => true,
                'is_test_mode' => true,
                'credentials' => [],
                'settings' => [],
                'sort_order' => 2,
            ],
        ];

        foreach ($gateways as $gatewayData) {
            PaymentGateway::updateOrCreate(
                ['name' => $gatewayData['name']],
                $gatewayData
            );
        }
    }
}
