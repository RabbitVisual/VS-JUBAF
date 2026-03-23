<?php

namespace Modules\PaymentGateway\App\Services;

use Modules\PaymentGateway\App\Models\Payment;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeService implements GatewayInterface
{
    // ... traits properties ...

    // construct ...

    public function process(Payment $payment): array
    {
        try {
            $checkout_session = Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($payment->currency),
                        'product_data' => [
                            'name' => $payment->description ?? "Pagamento #{$payment->transaction_id}",
                        ],
                        'unit_amount' => (int) ($payment->amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('checkout.show', $payment->transaction_id).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.show', $payment->transaction_id),
                'metadata' => [
                    'transaction_id' => $payment->transaction_id,
                    'payment_id' => $payment->id,
                    'user_id' => $payment->user_id,
                ],
                'automatic_tax' => ['enabled' => false],
            ]);

            $payment->update([
                'gateway_transaction_id' => $checkout_session->id,
                'gateway_response' => $checkout_session->toArray(),
            ]);

            return [
                'success' => true,
                'redirect_url' => $checkout_session->url,
            ];
        } catch (ApiErrorException $e) {
            \Log::error('Stripe Checkout Error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function checkStatus(Payment $payment): array
    {
        try {
            if (! $payment->gateway_transaction_id) {
                return ['success' => false, 'error' => 'ID da transação não encontrado'];
            }

            $session = Session::retrieve($payment->gateway_transaction_id);

            $status = match ($session->status) {
                'complete' => 'completed',
                'expired' => 'cancelled',
                'open' => 'pending',
                default => 'pending',
            };

            // Double check payment status if session is complete
            if ($session->payment_status === 'paid') {
                $status = 'completed';
            }

            $payment->update([
                'status' => $status,
                'gateway_response' => $session->toArray(),
                'paid_at' => $session->payment_status === 'paid' ? now() : null,
            ]);

            return [
                'success' => true,
                'status' => $status,
                'session' => $session->toArray(),
            ];
        } catch (ApiErrorException $e) {
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

            $session = Session::retrieve($payment->gateway_transaction_id);
            if ($session->status === 'open') {
                $session->expire();
                $status = 'cancelled';
            } else {
                $status = $session->status === 'complete' ? 'completed' : 'cancelled';
            }

            $payment->update([
                'status' => $status,
                'gateway_response' => $session->toArray(),
            ]);

            return ['success' => true];
        } catch (ApiErrorException $e) {
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

            // Precisamos do PaymentIntent ID para reembolso, não do Session ID
            $paymentIntentId = $payment->gateway_response['payment_intent'] ?? null;

            if (! $paymentIntentId) {
                // Tenta recuperar da sessão se não estiver no cache local
                $session = Session::retrieve($payment->gateway_transaction_id);
                $paymentIntentId = $session->payment_intent;
            }

            if (! $paymentIntentId) {
                return ['success' => false, 'error' => 'Payment Intent ID não encontrado para reembolso.'];
            }

            $refund = \Stripe\Refund::create([
                'payment_intent' => $paymentIntentId,
                'amount' => $amount ? (int) ($amount * 100) : null,
            ]);

            $payment->update([
                'status' => 'refunded',
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['refund' => $refund->toArray()]),
            ]);

            return [
                'success' => true,
                'refund' => $refund->toArray(),
            ];
        } catch (ApiErrorException $e) {
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
