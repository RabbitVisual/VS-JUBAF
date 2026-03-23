<?php

namespace Modules\PaymentGateway\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\PaymentGateway\App\Models\PaymentGateway;

class UpdateMercadoPagoCredentialsSeeder extends Seeder
{
    /**
     * Atualiza o gateway Mercado Pago sem credenciais hardcoded.
     * Em ambiente local, credenciais podem ser preenchidas via .env (opcional):
     *   MP_PUBLIC_KEY_TEST, MP_ACCESS_TOKEN_TEST, MP_WEBHOOK_SECRET
     * Em produção, configurar 100% pelo Admin (Gateways).
     */
    public function run(): void
    {
        $gateway = PaymentGateway::where('name', 'mercado_pago')->first();

        if (!$gateway) {
            $this->command->error('Mercado Pago gateway not found.');
            return;
        }

        $credentials = [];
        if (app()->environment('local', 'development', 'dev')) {
            $publicKey = env('MP_PUBLIC_KEY_TEST');
            $accessToken = env('MP_ACCESS_TOKEN_TEST');
            $webhookSecret = env('MP_WEBHOOK_SECRET');
            if ($publicKey) {
                $credentials['public_key'] = $publicKey;
            }
            if ($accessToken) {
                $credentials['access_token'] = $accessToken;
            }
            if ($webhookSecret) {
                $credentials['webhook_secret'] = $webhookSecret;
            }
        }

        if (!empty($credentials)) {
            $gateway->setEncryptedCredentials(array_merge($gateway->getDecryptedCredentials(), $credentials));
        }

        $gateway->is_active = $gateway->is_active ?? false;
        $gateway->is_test_mode = $gateway->is_test_mode ?? true;
        $gateway->save();

        $this->command->info('Mercado Pago gateway updated (credentials only from env in dev or Admin in production).');
    }
}
