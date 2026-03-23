<?php

namespace Modules\PaymentGateway\App\Services;

use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentGateway;

/**
 * Serviço central da API v1 de PaymentGateway.
 * Expõe gateways ativos (sem credenciais) e status de pagamento para frontend/polling.
 */
class PaymentGatewayApiService
{
    /**
     * Lista gateways ativos e configurados para uso no frontend (dropdowns, Events, Donations).
     * Não expõe credenciais.
     *
     * @return array<int, array{id: int, name: string, display_name: string, supported_methods: array, sandbox: bool}>
     */
    public function getActiveGatewaysForFrontend(): array
    {
        return PaymentGateway::active()
            ->get()
            ->map(function (PaymentGateway $g) {
                return [
                    'id' => $g->id,
                    'name' => $g->name,
                    'display_name' => $g->display_name,
                    'supported_methods' => $g->supported_methods ?? [],
                    'sandbox' => (bool) $g->is_test_mode,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Retorna status e dados mínimos do pagamento por transaction_id (para polling).
     *
     * @return array{status: string, transaction_id: string, amount: string, currency: string, paid_at: string|null}|null
     */
    public function getPaymentStatusByTransactionId(string $transactionId): ?array
    {
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (! $payment) {
            return null;
        }

        return [
            'status' => $payment->status,
            'transaction_id' => $payment->transaction_id,
            'amount' => (string) $payment->amount,
            'currency' => $payment->currency ?? 'BRL',
            'paid_at' => $payment->paid_at?->toIso8601String(),
        ];
    }
}
