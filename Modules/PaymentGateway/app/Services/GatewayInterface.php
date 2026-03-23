<?php

namespace Modules\PaymentGateway\App\Services;

use Modules\PaymentGateway\App\Models\Payment;

interface GatewayInterface
{
    /**
     * Processa o pagamento
     */
    public function process(Payment $payment): array;

    /**
     * Verifica o status do pagamento
     */
    public function checkStatus(Payment $payment): array;

    /**
     * Cancela o pagamento
     */
    public function cancel(Payment $payment): array;

    /**
     * Reembolsa o pagamento
     */
    public function refund(Payment $payment, ?float $amount = null): array;
}
