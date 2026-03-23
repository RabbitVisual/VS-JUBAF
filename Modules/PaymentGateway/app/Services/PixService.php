<?php

namespace Modules\PaymentGateway\App\Services;

use Modules\PaymentGateway\App\Models\Payment;

class PixService implements GatewayInterface
{
    protected $pixKey;

    protected $pixKeyType;

    protected $merchantName;

    protected $merchantCity;

    public function __construct()
    {
        $gateway = \Modules\PaymentGateway\App\Models\PaymentGateway::where('name', 'pix')->first();

        if ($gateway && $gateway->isConfigured()) {
            $credentials = $gateway->getDecryptedCredentials();
            $this->pixKey = $credentials['pix_key'] ?? null;
            $this->pixKeyType = $credentials['pix_key_type'] ?? 'email';
            $this->merchantName = $credentials['merchant_name'] ?? config('app.name');
            $this->merchantCity = $credentials['merchant_city'] ?? 'SAO PAULO';
        }
    }

    public function process(Payment $payment): array
    {
        if (! $this->pixKey) {
            return [
                'success' => false,
                'error' => 'Chave PIX não configurada',
            ];
        }

        // Gera código PIX (EMV)
        $pixCode = $this->generatePixCode($payment);

        // Gera QR Code (pode usar biblioteca externa ou API)
        $qrCodeBase64 = $this->generateQRCode($pixCode);

        $payment->update([
            'gateway_transaction_id' => 'PIX-'.$payment->transaction_id,
            'gateway_response' => [
                'pix_code' => $pixCode,
                'qr_code_base64' => $qrCodeBase64,
                'expires_at' => now()->addHours(24)->toIso8601String(),
            ],
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'pix_code' => $pixCode,
            'qr_code_base64' => $qrCodeBase64,
            'expires_at' => now()->addHours(24)->toIso8601String(),
        ];
    }

    public function checkStatus(Payment $payment): array
    {
        // Para PIX, o status precisa ser verificado manualmente ou via webhook
        // Por enquanto, retorna o status atual
        return [
            'success' => true,
            'status' => $payment->status,
            'payment' => $payment->toArray(),
        ];
    }

    public function cancel(Payment $payment): array
    {
        if ($payment->status === 'completed') {
            return [
                'success' => false,
                'error' => 'Não é possível cancelar um pagamento PIX já confirmado',
            ];
        }

        $payment->update([
            'status' => 'cancelled',
        ]);

        return ['success' => true];
    }

    public function refund(Payment $payment, ?float $amount = null): array
    {
        // PIX não suporta reembolso automático
        // Deve ser feito manualmente
        return [
            'success' => false,
            'error' => 'Reembolso PIX deve ser processado manualmente',
        ];
    }

    /**
     * Gera código PIX (EMV)
     */
    protected function generatePixCode(Payment $payment): string
    {
        $payload = [
            '00' => '01', // Payload Format Indicator
            '26' => [
                '00' => 'BR.GOV.BCB.PIX', // GUI
                '01' => $this->pixKey, // Chave PIX
            ],
            '52' => '0000', // Merchant Category Code
            '53' => '986', // Transaction Currency (BRL)
            '54' => number_format($payment->amount, 2, '.', ''), // Transaction Amount
            '58' => 'BR', // Country Code
            '59' => $this->merchantName, // Merchant Name
            '60' => $this->merchantCity, // Merchant City
            '62' => [
                '05' => $payment->transaction_id, // Transaction ID
            ],
        ];

        // Converte para string EMV
        return $this->buildEMVString($payload);
    }

    /**
     * Constrói string EMV
     */
    protected function buildEMVString(array $data, string $parent = ''): string
    {
        $result = '';

        foreach ($data as $key => $value) {
            $fullKey = $parent ? "{$parent}.{$key}" : $key;

            if (is_array($value)) {
                $result .= $this->buildEMVString($value, $fullKey);
            } else {
                $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
                $result .= $key.$length.$value;
            }
        }

        return $result;
    }

    /**
     * Gera URL local do QR Code (sem APIs externas).
     */
    protected function generateQRCode(string $pixCode): string
    {
        $d = strtr(base64_encode($pixCode), ['+' => '-', '/' => '_']);

        return route('checkout.qr', ['d' => $d, 'size' => 300]);
    }
}
