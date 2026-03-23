<?php

namespace Modules\PaymentGateway\App\Drivers;

use Illuminate\Support\Facades\Http;
use Modules\PaymentGateway\App\Contracts\PaymentGatewayInterface;

class PixMtlsDriver implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get configured HTTP client with mTLS options.
     */
    protected function getClient()
    {
        $options = [];

        // Path to the certificate file (PEM/P12)
        if (! empty($this->config['certificate_path'])) {
            $certPath = $this->config['certificate_path'];

            // Basic mTLS configuration
            // Check Guzzle docs: ['cert' => ['/path/server.pem', 'password']]
            $options['cert'] = $certPath;

            // If there is a separate key file, it would be in settings, but for now assuming one file or handled here.
            // $options['ssl_key'] = $this->config['key_path'];
        }

        return Http::withOptions($options)
            ->baseUrl($this->config['base_url'] ?? 'https://api-pix.example.com');
    }

    public function charge(float $amount, array $data): array
    {
        // This driver is specific for PIX, so charge usually generates a TXID/Location
        return $this->generatePixQrCode($amount, $data);
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        $response = $this->getClient()->put("/pix/{$transactionId}/devolucao", [
            'valor' => number_format($amount, 2, '.', ''),
        ]);

        return $response->json();
    }

    public function getPaymentStatus(string $transactionId): string
    {
        $response = $this->getClient()->get("/pix/{$transactionId}");

        // Map status
        $status = $response->json('status'); // ativa, concluida, etc.
        return $status === 'CONCLUIDA' ? 'completed' : 'pending';
    }

    public function generatePixQrCode(float $amount, array $data): array
    {
        // 1. Authenticate (Get Token) - usually requires mTLS
        // This step is simplified. In production, cache the token.
        // $token = $this->authenticate();

        // 2. Create Charge (Cob)
        $txid = $data['transaction_id'] ?? uniqid();
        $response = $this->getClient()->post("/cob/{$txid}", [
            'calendario' => ['expiracao' => 3600],
            'valor' => ['original' => number_format($amount, 2, '.', '')],
            'chave' => $this->config['pix_key'] ?? '',
        ]);

        // 3. Return payload
        return [
            'qr_code' => $response->json('pixCopiaECola'),
            'transaction_id' => $response->json('txid'),
            'payload' => $response->json(),
        ];
    }

    public function verifyWebhookSignature(\Illuminate\Http\Request $request): bool
    {
        // TODO: Implement Pix mTLS webhook signature verification if applicable
        // Often Pix webhooks are authenticated via mTLS client certs on the receiving end (server config) or signatures.
        return true;
    }
}
