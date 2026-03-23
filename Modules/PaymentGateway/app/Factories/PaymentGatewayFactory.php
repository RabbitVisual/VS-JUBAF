<?php

namespace Modules\PaymentGateway\App\Factories;

use Modules\PaymentGateway\App\Contracts\PaymentGatewayInterface;
use Modules\PaymentGateway\App\Drivers\StripeDriver;
use Modules\PaymentGateway\App\Drivers\MercadoPagoDriver;
use Modules\PaymentGateway\App\Drivers\PixMtlsDriver;

/**
 * Factory central de drivers de pagamento. Config vem do modelo PaymentGateway (Admin).
 *
 * Para adicionar um novo gateway:
 * 1. Implementar PaymentGatewayInterface em um Driver (ex.: app/Drivers/NovoDriver.php).
 * 2. Registrar aqui no match(): 'nome_driver' => new NovoDriver($config).
 * 3. Adicionar campos de credenciais em resources/views/admin/gateways/edit.blade.php.
 * 4. Adicionar branch em GatewayWebhookController::handle() para o driver.
 * 5. (Opcional) Exibir URL do webhook na edição: url()->route('api.gateway.webhook', ['driver' => 'nome_driver']).
 */
class PaymentGatewayFactory
{
    /**
     * Create a payment gateway driver instance.
     *
     * @param string $driver
     * @return PaymentGatewayInterface
     * @throws \Exception
     */
    public static function make(string $driver): PaymentGatewayInterface
    {
        // 1. Fetch Configuration from new PaymentGateway model
        $gateway = \Modules\PaymentGateway\App\Models\PaymentGateway::where('name', $driver)->first();

        if (! $gateway) {
             throw new \Exception("Payment Gateway [{$driver}] is not configured.");
        }

        if (! $gateway->is_active) {
             throw new \Exception("Payment Gateway [{$driver}] is not active.");
        }

        // 2. Prepare Config (Credentials + Settings)
        $credentials = $gateway->getDecryptedCredentials();
        $settings = $gateway->settings ?? [];

        // Merge credentials into config (Driver expects ['access_token' => '...', 'public_key' => '...'])
        $config = array_merge($settings, $credentials);

        // Append certificate path if exists (Legacy/Pix MTLS support)
        // Note: PaymentGateway model might not have 'certificate_path' column, check migration if needed.
        // Assuming it might be in settings for now.
        if (isset($settings['certificate_path'])) {
            $config['certificate_path'] = storage_path('app/private/certs/' . $settings['certificate_path']);
        }

        return match ($driver) {
            'stripe' => new StripeDriver($config),
            'mercado_pago' => new MercadoPagoDriver($config),
            'pix_mtls' => new PixMtlsDriver($config),
            default => throw new \Exception("Driver [{$driver}] not supported."),
        };
    }
}
