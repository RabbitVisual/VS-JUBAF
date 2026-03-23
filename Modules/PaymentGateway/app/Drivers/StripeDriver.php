<?php

namespace Modules\PaymentGateway\App\Drivers;

use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Contracts\PaymentGatewayInterface;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeDriver implements PaymentGatewayInterface
{
    protected array $config;
    protected StripeClient $client;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new StripeClient($config['secret_key'] ?? '');
    }

    public function charge(float $amount, array $data): array
    {
        try {
            // Map Vertex methods to Stripe types
            $vertexMethods = $data['supported_methods'] ?? ['credit_card'];
            $stripeMethods = [];

            foreach ($vertexMethods as $method) {
                switch ($method) {
                    case 'credit_card':
                        $stripeMethods[] = 'card';
                        break;
                    case 'boleto':
                        $stripeMethods[] = 'boleto';
                        break;
                    case 'pix':
                        $stripeMethods[] = 'pix';
                        break;
                }
            }

            // Fallback to card if empty
            if (empty($stripeMethods)) {
                $stripeMethods = ['card'];
            }

            $session = $this->client->checkout->sessions->create([
                'payment_method_types' => $stripeMethods,
                'line_items' => [[
                    'price_data' => [
                        'currency' => strtolower($data['currency'] ?? 'brl'),
                        'product_data' => [
                            'name' => $data['description'] ?? 'Doação / Inscrição',
                        ],
                        'unit_amount' => (int) ($amount * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'customer_email' => $data['payer']['email'] ?? null,
                'success_url' => route('checkout.show', $data['transaction_id']) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.show', $data['transaction_id']),
                'metadata' => [
                    'transaction_id' => $data['transaction_id'],
                ],
                'payment_method_options' => in_array('boleto', $stripeMethods) ? [
                    'boleto' => [
                        'expires_after_days' => 3,
                    ]
                ] : [],
            ]);

            return [
                'status' => 'pending',
                'transaction_id' => $session->id,
                'redirect_url' => $session->url,
                'payload' => $session->toArray(),
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe Charge Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function refund(string $transactionId, ?float $amount = null): array
    {
        try {
            // transactionId here is the Checkout Session ID
            $session = $this->client->checkout->sessions->retrieve($transactionId);
            $paymentIntentId = $session->payment_intent;

            if (!$paymentIntentId) {
                throw new \Exception('Payment Intent not found for this session.');
            }

            $refund = $this->client->refunds->create([
                'payment_intent' => $paymentIntentId,
                'amount' => $amount ? (int) ($amount * 100) : null,
            ]);

            return [
                'status' => 'refunded',
                'transaction_id' => $refund->id,
                'payload' => $refund->toArray(),
            ];
        } catch (\Exception $e) {
            \Log::error('Stripe Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getPaymentStatus(string $transactionId): string
    {
        try {
            $session = $this->client->checkout->sessions->retrieve($transactionId);

            if ($session->payment_status === 'paid') {
                return 'completed';
            }

            return match ($session->status) {
                'complete' => 'completed',
                'expired' => 'cancelled',
                default => 'pending',
            };
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function generatePixQrCode(float $amount, array $data): array
    {
        // Stripe handles Pix via Checkout. We can force 'pix' as a payment method type.
        // But for now, we use standard Checkout which supports multiple methods.
        return $this->charge($amount, $data);
    }

    public function verifyWebhookSignature(Request $request): bool
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = $this->config['webhook_secret'] ?? '';

        if (!$webhookSecret || !$sigHeader) {
            \Log::warning('Stripe Webhook Secret or Signature missing.');
            return false;
        }

        try {
            Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            return true;
        } catch (\UnexpectedValueException $e) {
            \Log::error('Stripe Webhook: Invalid payload.');
            return false;
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            \Log::error('Stripe Webhook: Invalid signature.');
            return false;
        }
    }
}
