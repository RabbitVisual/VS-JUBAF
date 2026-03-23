<?php

namespace Modules\PaymentGateway\App\Services;

use Illuminate\Support\Str;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentAuditLog;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\PaymentGateway\App\Factories\PaymentGatewayFactory;
use Modules\PaymentGateway\App\Events\PaymentReceived;

class PaymentService
{
    /**
     * Cria um novo pagamento
     */
    public function createPayment(array $data): Payment
    {
        $gateway = PaymentGateway::findOrFail($data['payment_gateway_id']);

        $payment = Payment::create([
            'user_id' => $data['user_id'] ?? null,
            'payment_gateway_id' => $gateway->id,
            'payment_type' => $data['payment_type'],
            'payable_type' => $data['payable_type'] ?? null,
            'payable_id' => $data['payable_id'] ?? null,
            'transaction_id' => $this->generateTransactionId(),
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'BRL',
            'status' => 'pending',
            'payment_method' => $data['payment_method'] ?? null,
            'description' => $data['description'] ?? null,
            'payer_name' => $data['payer_name'] ?? null,
            'payer_email' => $data['payer_email'] ?? null,
            'payer_document' => $data['payer_document'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);

        return $payment;
    }

    /**
     * Processa a transação de pagamento (Unified API).
     *
     * @param Payment $payment
     * @return array
     */
    public function process(Payment $payment): array
    {
        try {
            // 1. Resolve Driver (payment->gateway is PaymentGateway; name = driver key: stripe, mercado_pago, pix_mtls)
            $driverName = $payment->gateway->name;

            $driver = PaymentGatewayFactory::make($driverName);

            // 2. Prepare Data
            $data = [
                'transaction_id' => $payment->transaction_id,
                'description' => $payment->description,
                'supported_methods' => $payment->gateway->supported_methods,
                'currency' => $payment->currency ?? 'BRL',
                'payer' => [
                    'email' => $payment->payer_email,
                    'name' => $payment->payer_name,
                    'document' => $payment->payer_document,
                ],
                'metadata' => $payment->metadata ?? [],
                'token' => $payment->metadata['token'] ?? null, // For Card payments
            ];

            // 3. Execute Charge
            // Handle specific logic for Pix if needed (Driver might separate charge vs QR gen)
            // But verify if the driver actually SUPPORTS native Pix generation (e.g. Stripe throws exception)
            if ($payment->payment_method === 'pix' && $driverName !== 'stripe' && method_exists($driver, 'generatePixQrCode')) {
                // If the driver implements PaymentGatewayInterface, it has generatePixQrCode
                $response = $driver->generatePixQrCode($payment->amount, $data);
            } else {
                $response = $driver->charge($payment->amount, $data);
            }

            // 4. Update Payment
            $status = $response['status'] ?? 'pending';

            $payment->update([
                'gateway_transaction_id' => $response['transaction_id'] ?? null,
                'gateway_response' => $response,
                'status' => $status,
            ]);

            // Auto-confirm if completed immediately
            if ($status === 'completed') {
                $this->confirmPayment($payment);
            }

            return $response;

        } catch (\Exception $e) {
            $payment->update([
                'status' => 'failed',
                'gateway_response' => ['error' => $e->getMessage()]
            ]);
            throw $e;
        }
    }

    /**
     * Alias for process (Legacy support)
     */
    public function processPayment(Payment $payment): array
    {
        return $this->process($payment);
    }

    /**
     * Processa o pagamento através do Brick (Legacy Adapter)
     */
    public function processPaymentBrick(Payment $payment, array $brickData): array
    {
        // Merge brick data into metadata so 'process' can use it
        $metadata = $payment->metadata ?? [];
        $payment->metadata = array_merge($metadata, $brickData);
        $payment->save(); // Save metadata

        return $this->process($payment);
    }

    /**
     * Confirma o pagamento e dispara evento para Tesouraria.
     *
     * @param  string  $source  webhook, checkout_return, admin, system
     */
    public function confirmPayment(Payment $payment, string $source = 'system'): void
    {
        if ($payment->status === 'completed') {
            return;
        }

        $fromStatus = $payment->status;
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->logAudit($payment, $fromStatus, 'completed', $source);

        PaymentReceived::dispatch($payment);
    }

    /**
     * Gera um ID único de transação
     */
    protected function generateTransactionId(): string
    {
        return 'TXN-'.strtoupper(Str::random(12)).'-'.time();
    }

    /**
     * Atualiza o status do pagamento (auditável).
     *
     * @param  string  $source  webhook, checkout_return, admin, system
     */
    public function updatePaymentStatus(Payment $payment, string $status, array $gatewayResponse = [], string $source = 'system'): void
    {
        $fromStatus = $payment->status;
        $payment->update([
            'status' => $status,
            'gateway_response' => array_merge($payment->gateway_response ?? [], $gatewayResponse),
            'paid_at' => $status === 'completed' ? now() : null,
        ]);

        $this->logAudit($payment, $fromStatus, $status, $source);

        if ($status === 'completed') {
            PaymentReceived::dispatch($payment);
        }
    }

    /**
     * Registra alteração de status para auditoria.
     */
    protected function logAudit(Payment $payment, ?string $fromStatus, string $toStatus, string $source = 'system'): void
    {
        if (! class_exists(PaymentAuditLog::class)) {
            return;
        }

        try {
            PaymentAuditLog::create([
                'payment_id' => $payment->id,
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'source' => $source,
                'gateway_transaction_id' => $payment->gateway_transaction_id,
            ]);
        } catch (\Exception $e) {
            \Log::warning('Payment audit log failed: ' . $e->getMessage());
        }
    }

    /**
     * Confirma pagamento a partir do retorno do Stripe Checkout (session_id na URL).
     * Chamado quando o usuário volta da página do Stripe com ?session_id=xxx
     */
    public function confirmPaymentFromStripeSession(Payment $payment, string $sessionId): void
    {
        $driver = PaymentGatewayFactory::make($payment->gateway->name);
        if (! method_exists($driver, 'getPaymentStatus')) {
            return;
        }
        $status = $driver->getPaymentStatus($sessionId);
        if ($status === 'completed') {
            $payment->update([
                'gateway_transaction_id' => $payment->gateway_transaction_id ?? $sessionId,
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['session_id' => $sessionId]),
            ]);
            $this->confirmPayment($payment, 'checkout_return');
        }
    }

    /**
     * Verifica o status do pagamento no gateway.
     *
     * @param  string  $source  webhook, checkout_return, system (para auditoria)
     */
    public function checkPaymentStatus(Payment $payment, string $source = 'system'): array
    {
        try {
            $driverName = $payment->gateway->name;
            $driver = PaymentGatewayFactory::make($driverName);

            $status = $driver->getPaymentStatus($payment->gateway_transaction_id ?? $payment->transaction_id);

            if ($status === 'completed' && $payment->status !== 'completed') {
                $this->confirmPayment($payment, $source);
            }

            return ['status' => $status];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Obtém estatísticas de pagamentos
     */
    public function getStatistics(array $filters = []): array
    {
        $query = Payment::query();

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $total = $query->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $totalAmount = (clone $query)->where('status', 'completed')->sum('amount');
        $pending = (clone $query)->where('status', 'pending')->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $pending,
            'total_amount' => $totalAmount,
            'average_amount' => $completed > 0 ? $totalAmount / $completed : 0,
        ];
    }
}
