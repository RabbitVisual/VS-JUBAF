<?php

namespace Modules\PaymentGateway\App\Drivers;

use Modules\PaymentGateway\App\Contracts\PaymentGatewayInterface;

class MercadoPagoDriver implements PaymentGatewayInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        // MercadoPago\SDK::setAccessToken($config['access_token'] ?? '');
    }

    public function charge(float $amount, array $data): array
    {
        $accessToken = $this->config['access_token'] ?? null;

        if (!$accessToken) {
             throw new \Exception('Mercado Pago Access Token not configured.');
        }

        $notificationUrl = route('api.gateway.webhook', ['driver' => 'mercado_pago']);

        $paymentData = [
            'transaction_amount' => (float) $amount,
            'description' => $data['description'] ?? 'Doação',
            'payment_method_id' => $data['metadata']['payment_method_id'] ?? 'pix', // Default to Pix if not sent (e.g. manual call)
            'payer' => [
                'email' => $data['payer']['email'] ?? 'email@test.com',
                'first_name' => $data['payer']['name'] ?? 'Anonimo',
                // 'identification' => ... // Add if document is available
            ],
            // 'installments' => ... (add logic for cards)
        ];

        // Mercado Pago rejects local/private URLs as notification_url
        if (!preg_match('/(localhost|127\.0\.0\.1|192\.168\.|10\.|172\.(1[6-9]|2[0-9]|3[0-1])\.)/', $notificationUrl)) {
            $paymentData['notification_url'] = $notificationUrl;
        }

        // Add details for Card
        if (isset($data['token']) && $paymentData['payment_method_id'] !== 'pix' && $paymentData['payment_method_id'] !== 'bolbradesco') {
             $paymentData['token'] = $data['token'];
             $paymentData['installments'] = (int) ($data['metadata']['installments'] ?? 1);
             if(isset($data['metadata']['issuer_id'])) {
                 $paymentData['issuer_id'] = (int) $data['metadata']['issuer_id'];
             }
        }

        // Add Identification if present
        if (!empty($data['payer']['document'])) {
            $paymentData['payer']['identification'] = [
                'type' => 'CPF',
                'number' => preg_replace('/\D/', '', $data['payer']['document']),
            ];
        }

        // external_reference: webhook usa para localizar nosso Payment (transaction_id)
        $paymentData['external_reference'] = $data['transaction_id'] ?? null;

        \Illuminate\Support\Facades\Log::info('MercadoPago Payload:', $paymentData);

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->withHeaders(['X-Idempotency-Key' => (string) \Illuminate\Support\Str::uuid()])
            ->post('https://api.mercadopago.com/v1/payments', $paymentData);

        if ($response->failed()) {
            throw new \Exception('Mercado Pago API Error: ' . $response->body());
        }

        $result = $response->json();
        $status = $result['status'] ?? 'pending';
        if ($status === 'approved') {
            $status = 'completed';
        }
        if ($status === 'rejected') {
            $status = 'failed';
        }

        return [
            'status' => $status,
            'transaction_id' => (string) $result['id'],
            'payload' => $result,
            'qr_code' => $result['point_of_interaction']['transaction_data']['qr_code'] ?? null,
            'qr_code_base64' => $result['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
        ];
    }

    public function generatePixQrCode(float $amount, array $data): array
    {
        $data['metadata']['payment_method_id'] = 'pix';
        return $this->charge($amount, $data);
    }

    /**
     * Verifica assinatura do webhook conforme documentação Mercado Pago (x-signature + x-request-id).
     * Se webhook_secret não estiver configurado, aceita (retrocompatibilidade). GET sem assinatura (IPN legado) é aceito.
     */
    public function verifyWebhookSignature(\Illuminate\Http\Request $request): bool
    {
        $secret = $this->config['webhook_secret'] ?? null;
        if (empty($secret)) {
            return true;
        }

        $xSignature = $request->header('x-signature');
        if (empty($xSignature)) {
            // IPN legado (GET com topic/id) não envia assinatura
            return true;
        }

        $parts = explode(',', $xSignature);
        $ts = null;
        $hash = null;
        foreach ($parts as $part) {
            $keyValue = explode('=', trim($part), 2);
            if (count($keyValue) === 2) {
                $key = trim($keyValue[0]);
                $value = trim($keyValue[1]);
                if ($key === 'ts') {
                    $ts = $value;
                } elseif ($key === 'v1') {
                    $hash = $value;
                }
            }
        }

        if ($hash === null) {
            \Illuminate\Support\Facades\Log::warning('Mercado Pago webhook: x-signature sem v1');
            return false;
        }

        $dataId = $request->query('data.id') ?? $request->input('data.id');
        if (is_array($dataId) || is_object($dataId)) {
            $dataId = $request->input('data.id');
        }
        $dataId = (string) ($dataId ?? '');
        if (ctype_alnum($dataId)) {
            $dataId = strtolower($dataId);
        }

        $xRequestId = (string) ($request->header('x-request-id') ?? '');
        $ts = (string) ($ts ?? '');

        $manifest = "id:{$dataId};request-id:{$xRequestId};ts:{$ts};";
        $expected = hash_hmac('sha256', $manifest, $secret);

        if (!hash_equals($expected, $hash)) {
            \Illuminate\Support\Facades\Log::warning('Mercado Pago webhook: assinatura inválida');
            return false;
        }

        return true;
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        return [
            'status' => 'refunded',
            'transaction_id' => $transactionId,
        ];
    }

    /**
     * Consulta status do pagamento na API do Mercado Pago (auditável, sem depender só de webhook).
     */
    public function getPaymentStatus(string $transactionId): string
    {
        $accessToken = $this->config['access_token'] ?? null;
        if (! $accessToken) {
            return 'pending';
        }

        $response = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get("https://api.mercadopago.com/v1/payments/{$transactionId}");

        if (! $response->successful()) {
            \Illuminate\Support\Facades\Log::warning('Mercado Pago getPaymentStatus failed', ['id' => $transactionId, 'body' => $response->body()]);
            return 'pending';
        }

        $result = $response->json();
        $status = $result['status'] ?? 'pending';

        return match ($status) {
            'approved' => 'completed',
            'rejected', 'cancelled' => 'failed',
            'refunded', 'charged_back' => 'refunded',
            default => 'pending',
        };
    }
}
