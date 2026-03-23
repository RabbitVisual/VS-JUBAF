<?php

namespace Modules\PaymentGateway\App\Services;

use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use Modules\PaymentGateway\App\Models\Payment;

class MercadoPagoService implements GatewayInterface
{
    protected $publicKey;

    protected $accessToken;

    protected $isTestMode;

    public function __construct()
    {
        $gateway = \Modules\PaymentGateway\App\Models\PaymentGateway::where('name', 'mercado_pago')->first();

        if ($gateway && $gateway->isConfigured()) {
            $credentials = $gateway->getDecryptedCredentials();
            $this->isTestMode = $gateway->is_test_mode;
            $this->publicKey = $credentials['public_key'] ?? null;
            $this->accessToken = $credentials['access_token'] ?? null;

            if ($this->accessToken) {
                MercadoPagoConfig::setAccessToken($this->accessToken);
            }
        }
    }

    public function process(Payment $payment): array
    {
        try {
            // PIX Payment
            if ($payment->payment_method === 'pix') {
                $client = new PaymentClient;

                // Split name
                $fullName = $payment->payer_name ?? 'Cliente Vertex';
                $parts = explode(' ', $fullName);
                $firstName = $parts[0];
                $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'Sobrenome';

                $docNumber = preg_replace('/[^0-9]/', '', $payment->payer_document ?? Auth::user()->cpf ?? '19119119100');
                $docType = strlen($docNumber) === 14 ? 'CNPJ' : 'CPF';

                $request = [
                    'transaction_amount' => (float) $payment->amount,
                    'description' => $payment->description ?? "Pagamento #{$payment->transaction_id}",
                    'payment_method_id' => 'pix',
                    'external_reference' => $payment->transaction_id,
                    'payer' => [
                        'email' => $payment->payer_email ?? 'email@test.com',
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'identification' => [
                            'type' => $docType,
                            'number' => $docNumber,
                        ],
                    ],
                ];

                $mpPayment = $client->create($request);

                $pointOfInteraction = $mpPayment->point_of_interaction;
                $transactionData = $pointOfInteraction->transaction_data;

                // Prepare response data with shortcuts for View
                $responseArray = (array) $mpPayment;
                $responseArray['pix_code'] = $transactionData->qr_code ?? null;
                $responseArray['pix_qr_code_base64'] = $transactionData->qr_code_base64 ?? null;
                $responseArray['ticket_url'] = $transactionData->ticket_url ?? null;

                $payment->update([
                    'gateway_transaction_id' => $mpPayment->id,
                    'gateway_response' => $responseArray,
                ]);

                $status = match ($mpPayment->status) {
                    'approved' => 'completed',
                    'pending', 'in_process' => 'processing',
                    'rejected', 'cancelled' => 'failed',
                    default => 'pending',
                };

                $payment->update([
                    'status' => $status,
                    'paid_at' => $status === 'completed' ? now() : null,
                ]);

                return [
                    'success' => true,
                    'payment_id' => $mpPayment->id,
                    'status' => $status,
                    'pix_code' => $transactionData->qr_code ?? null,
                    'pix_qr_code_base64' => $transactionData->qr_code_base64 ?? null,
                    'ticket_url' => $transactionData->ticket_url ?? null,
                ];

            } else {
                // Preference (Checkout Pro) for Cards
                $client = new PreferenceClient;

                $request = [
                    'items' => [
                        [
                            'title' => $payment->description ?? "Pagamento #{$payment->transaction_id}",
                            'quantity' => 1,
                            'currency_id' => 'BRL',
                            'unit_price' => (float) $payment->amount,
                        ],
                    ],
                    'external_reference' => $payment->transaction_id,
                    'back_urls' => [
                        'success' => route('checkout.show', $payment->transaction_id),
                        'failure' => route('checkout.show', $payment->transaction_id),
                        'pending' => route('checkout.show', $payment->transaction_id),
                    ],
                ];

                // Only add auto_return if NOT on localhost, as MP validates reachability/format strictness
                $host = request()->getHost();
                if ($host !== '127.0.0.1' && $host !== 'localhost') {
                    $request['auto_return'] = 'approved';
                }

                $preference = $client->create($request);

                $payment->update([
                    'gateway_transaction_id' => $preference->id,
                    'gateway_response' => (array) $preference,
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $this->isTestMode ? $preference->sandbox_init_point : $preference->init_point,
                ];
            }
        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    /**
     * Processa pagamento vindo do Payment Brick (Frontend)
     */
    public function processBrick(Payment $payment, array $brickData): array
    {
        try {
            $client = new PaymentClient;

            // Basic request structure
            $request = [
                'transaction_amount' => (float) $brickData['transaction_amount'],
                'token' => $brickData['token'] ?? null,
                'description' => $payment->description ?? 'Doação Vertex',
                'installments' => (int) ($brickData['installments'] ?? 1),
                'payment_method_id' => $brickData['payment_method_id'],
                'issuer_id' => $brickData['issuer_id'] ?? null,
                'payer' => [
                    'email' => $brickData['payer']['email'],
                    'identification' => [
                        'type' => $brickData['payer']['identification']['type'] ?? 'CPF',
                        'number' => $brickData['payer']['identification']['number'] ?? null,
                    ],
                ],
                'external_reference' => (string) $payment->id,
            ];

            // Remove nulls logic could be here if SDK doesn't handle it, but new SDK is robust.

            $mpPayment = $client->create($request);

            $status = match ($mpPayment->status) {
                'approved' => 'completed',
                'pending', 'in_process' => 'processing',
                'rejected', 'cancelled' => 'failed',
                default => 'pending',
            };

            // Prepare Gateway Response Data
            $responseData = [
                'id' => $mpPayment->id,
                'status' => $mpPayment->status,
                'status_detail' => $mpPayment->status_detail,
                'payment_method_id' => $mpPayment->payment_method_id,
                'transaction_details' => (array) ($mpPayment->transaction_details ?? []),
            ];

            // Add PIX specific data if applicable (though Brick handles PIX display usually)
            if ($brickData['payment_method_id'] === 'pix') {
                $poi = $mpPayment->point_of_interaction;
                if ($poi) {
                    $responseData['pix_code'] = $poi->transaction_data->qr_code ?? null;
                    $responseData['pix_qr_code_base64'] = $poi->transaction_data->qr_code_base64 ?? null;
                    $responseData['ticket_url'] = $poi->transaction_data->ticket_url ?? null;
                }
            }

            $payment->update([
                'gateway_transaction_id' => $mpPayment->id,
                'gateway_response' => $responseData,
                'payment_method' => $brickData['payment_method_id'],
                'status' => $status,
                'paid_at' => $status === 'completed' ? now() : null,
            ]);

            return [
                'success' => true,
                'status' => $status,
                'payment_id' => $mpPayment->id,
                'data' => $responseData,
            ];

        } catch (\Exception $e) {
            return $this->handleError($e);
        }
    }

    private function handleError(\Exception $e): array
    {
        $rawErrorMessage = $e->getMessage();
        $errorMessage = $this->translateError($rawErrorMessage);

        if ($e instanceof MPApiException) {
            $response = $e->getApiResponse();
            if ($response) {
                $content = $response->getContent();
                \Log::error('MercadoPago API Error: '.json_encode($content));
                if (isset($content['message'])) {
                    $errorMessage = $this->translateError($content['message']);
                }
            }
        }

        \Log::error('MercadoPago Exception: '.$rawErrorMessage);

        return [
            'success' => false,
            'error' => $errorMessage,
        ];
    }

    private function translateError(string $message): string
    {
        $translations = [
            'payer.email must be a valid email' => 'O e-mail informado não é válido.',
            'payer.identification.number must be a valid' => 'O CPF/CNPJ informado é inválido.',
            'Invalid user identification number' => 'Número de documento (CPF/CNPJ) inválido.',
            'invalid_email' => 'E-mail inválido.',
            'bad_request' => 'Dados inválidos.',
            'auto_return invalid' => 'Erro na configuração de retorno automático (URL inválida).',
            'back_url.success must be defined' => 'URL de sucesso não definida corretamente.',
            'back_url.failure must be defined' => 'URL de falha não definida corretamente.',
            'back_url.pending must be defined' => 'URL de pendência não definida corretamente.',
            'item.title.length' => 'O título do item é muito longo.',
            'collector.id' => 'Erro no ID do recebedor.',
            'security_code_length' => 'Código de segurança do cartão inválido.',
            'card_number_length' => 'Número do cartão inválido.',
            'expiration_month_length' => 'Mês de expiração inválido.',
            'expiration_year_length' => 'Ano de expiração inválido.',
        ];

        foreach ($translations as $en => $pt) {
            if (stripos($message, $en) !== false) {
                return $pt;
            }
        }

        return $message;
    }

    public function checkStatus(Payment $payment): array
    {
        try {
            if (! $payment->gateway_transaction_id) {
                return ['success' => false, 'error' => 'ID da transação não encontrado'];
            }

            // If it's a preference ID (starts with generic pref id chars usually), we can't check status of preference easily to determine payment status.
            // We usually check payment status by searching for payment with external_reference.
            // But if we saved payment_id (like for PIX), we can check.

            // Assuming for now simple ID check if PIX, or we need to implement search.
            // For this implementation, let's assume direct Payment ID check first.

            $client = new PaymentClient;
            try {
                $mpPayment = $client->get($payment->gateway_transaction_id);
            } catch (\Exception $e) {
                // Maybe it was a preference ID. In that case, we can't get status from pref ID directly as a payment.
                // We should search for payment by external_reference
                // $searchClient = new \MercadoPago\Client\Payment\PaymentClient();
                // $search = $searchClient->search(["external_reference" => $payment->transaction_id]);
                // For now return error or implement search if crucial.
                return ['success' => false, 'error' => 'Não foi possível verificar status com ID salvo.'];
            }

            $status = match ($mpPayment->status) {
                'approved' => 'completed',
                'pending', 'in_process' => 'processing',
                'rejected', 'cancelled' => 'failed',
                default => 'pending',
            };

            $payment->update([
                'status' => $status,
                'gateway_response' => (array) $mpPayment,
                'paid_at' => $status === 'completed' ? now() : null,
            ]);

            return [
                'success' => true,
                'status' => $status,
                'payment' => (array) $mpPayment,
            ];
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancel(Payment $payment): array
    {
        try {
            if (! $payment->gateway_transaction_id) {
                return ['success' => false, 'error' => 'ID da transação não encontrado'];
            }

            $client = new PaymentClient;
            $mpPayment = $client->cancel($payment->gateway_transaction_id);

            $payment->update([
                'status' => 'cancelled',
                'gateway_response' => (array) $mpPayment,
            ]);

            return ['success' => true];
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refund(Payment $payment, ?float $amount = null): array
    {
        try {
            if (! $payment->gateway_transaction_id) {
                return ['success' => false, 'error' => 'ID da transação não encontrado'];
            }

            $client = new PaymentClient;
            $refund = $client->refund($payment->gateway_transaction_id, $amount ? ['amount' => $amount] : []);

            $payment->update([
                'status' => 'refunded',
                // Append refund info safely
            ]);

            return [
                'success' => true,
                'refund' => (array) $refund,
            ];
        } catch (MPApiException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPublicKey(): ?string
    {
        return $this->publicKey;
    }
}
