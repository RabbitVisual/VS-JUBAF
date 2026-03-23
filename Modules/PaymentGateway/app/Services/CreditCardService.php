<?php

namespace Modules\PaymentGateway\App\Services;

use Modules\PaymentGateway\App\Models\Payment;

class CreditCardService implements GatewayInterface
{
    /**
     * Cartão de crédito usa Stripe ou Mercado Pago como backend
     */
    public function process(Payment $payment): array
    {
        // Verifica qual gateway está configurado
        $stripeGateway = \Modules\PaymentGateway\App\Models\PaymentGateway::where('name', 'stripe')->where('is_active', true)->first();
        $mercadoPagoGateway = \Modules\PaymentGateway\App\Models\PaymentGateway::where('name', 'mercado_pago')->where('is_active', true)->first();

        if ($stripeGateway && $stripeGateway->isConfigured()) {
            $service = app(StripeService::class);

            return $service->process($payment);
        }

        if ($mercadoPagoGateway && $mercadoPagoGateway->isConfigured()) {
            $service = app(MercadoPagoService::class);

            return $service->process($payment);
        }

        return [
            'success' => false,
            'error' => 'Nenhum gateway de cartão de crédito configurado',
        ];
    }

    public function checkStatus(Payment $payment): array
    {
        if ($payment->gateway_transaction_id) {
            if (str_starts_with($payment->gateway_transaction_id, 'pi_')) {
                // Stripe
                $service = app(StripeService::class);

                return $service->checkStatus($payment);
            } else {
                // Mercado Pago
                $service = app(MercadoPagoService::class);

                return $service->checkStatus($payment);
            }
        }

        return [
            'success' => false,
            'error' => 'ID da transação não encontrado',
        ];
    }

    public function cancel(Payment $payment): array
    {
        if (str_starts_with($payment->gateway_transaction_id, 'pi_')) {
            $service = app(StripeService::class);

            return $service->cancel($payment);
        } else {
            $service = app(MercadoPagoService::class);

            return $service->cancel($payment);
        }
    }

    public function refund(Payment $payment, ?float $amount = null): array
    {
        if (str_starts_with($payment->gateway_transaction_id, 'pi_')) {
            $service = app(StripeService::class);

            return $service->refund($payment, $amount);
        } else {
            $service = app(MercadoPagoService::class);

            return $service->refund($payment, $amount);
        }
    }
}
