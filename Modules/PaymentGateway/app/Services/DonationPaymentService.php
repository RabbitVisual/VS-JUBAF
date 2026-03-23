<?php

namespace Modules\PaymentGateway\App\Services;

use App\Models\User;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentGateway;

/**
 * Serviço único para criação e processamento de doações.
 * Usado por MemberPanel\DonationController e Public\DonationController para evitar duplicação.
 */
class DonationPaymentService
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Resolve payment_method a partir do gateway e opcional brick payload.
     */
    public function resolvePaymentMethod(int $paymentGatewayId, ?array $brickPayload = null): string
    {
        $gateway = PaymentGateway::find($paymentGatewayId);
        if (! $gateway) {
            return 'unknown';
        }
        if ($gateway->name === 'stripe') {
            return 'stripe_checkout';
        }
        if ($gateway->name === 'mercado_pago' && $brickPayload) {
            $raw = $brickPayload['payment_method_id'] ?? 'unknown';
            return match ($raw) {
                'bank_transfer', 'pix' => 'pix',
                'ticket', 'bolbradesco' => 'bolbradesco',
                default => $raw,
            };
        }
        return 'unknown';
    }

    /**
     * Resolve payer (name, email, document) a partir de validated, user e brick.
     *
     * @return array{payer_name: string, payer_email: string, payer_document: string|null}
     */
    public function resolvePayer(array $validated, ?User $user, ?array $brickPayload = null): array
    {
        $name = $validated['payer_name'] ?? null;
        $email = $validated['payer_email'] ?? null;
        $document = $validated['payer_document'] ?? null;

        if ($brickPayload) {
            if (isset($brickPayload['payer']['first_name'])) {
                $name = $brickPayload['payer']['first_name'];
                if (isset($brickPayload['payer']['last_name'])) {
                    $name .= ' ' . $brickPayload['payer']['last_name'];
                }
            }
            if (isset($brickPayload['payer']['email'])) {
                $email = $brickPayload['payer']['email'];
            }
            if (isset($brickPayload['payer']['identification']['number'])) {
                $document = $brickPayload['payer']['identification']['number'];
            }
        }

        $name = $name ?? ($user ? $user->name : 'Doador Anônimo');
        $email = $email ?? ($user ? $user->email : 'nao-informado@doacao.com');
        $document = $document ?? ($user ? $user->cpf ?? null : null);

        return [
            'payer_name' => $name,
            'payer_email' => $email,
            'payer_document' => $document,
        ];
    }

    /**
     * Resolve payable_type, payable_id e description a partir de payment_type, ministry_id, campaign_id.
     *
     * @return array{payable_type: string|null, payable_id: int|null, description: string}
     */
    public function resolvePayableAndDescription(
        string $paymentType,
        ?int $ministryId,
        ?int $campaignId,
        string $defaultDescription = 'Doação'
    ): array {
        $payableType = null;
        $payableId = null;
        $description = $defaultDescription;

        if ($paymentType === 'ministry_donation' && $ministryId) {
            $ministry = \Modules\Ministries\App\Models\Ministry::find($ministryId);
            if ($ministry) {
                $payableType = \Modules\Ministries\App\Models\Ministry::class;
                $payableId = $ministry->id;
                $description = $description === 'Doação para Ministério' ? "Doação para {$ministry->name}" : $description;
            }
        } elseif ($paymentType === 'campaign' && $campaignId) {
            $campaign = \Modules\Treasury\App\Models\Campaign::find($campaignId);
            if ($campaign) {
                $payableType = \Modules\Treasury\App\Models\Campaign::class;
                $payableId = $campaign->id;
                $description = ($description === 'Doação' || $description === 'Doação para Campanha') ? "Doação para {$campaign->name}" : $description;
            }
        }

        return ['payable_type' => $payableType, 'payable_id' => $payableId, 'description' => $description];
    }

    /**
     * Cria e processa uma doação. Retorna array com 'payment' (Payment) e 'result' (redirect_url, error, status, etc.).
     *
     * @param  array{amount: float, payment_gateway_id: int, payment_type: string, ministry_id?: int, campaign_id?: int, description?: string, payer_name?: string, payer_email?: string, payer_document?: string, payment_method?: string}  $validated
     * @return array{payment: Payment, result: array}
     */
    public function createAndProcessDonation(array $validated, ?User $user = null, ?array $brickPayload = null): array
    {
        $paymentMethod = $validated['payment_method'] ?? null;
        if (empty($paymentMethod)) {
            $paymentMethod = $this->resolvePaymentMethod((int) $validated['payment_gateway_id'], $brickPayload);
        }

        $payer = $this->resolvePayer($validated, $user, $brickPayload);
        $defaultDesc = $this->getDefaultDescription($validated['payment_type'] ?? 'donation');
        $payable = $this->resolvePayableAndDescription(
            $validated['payment_type'] ?? 'donation',
            $validated['ministry_id'] ?? null,
            $validated['campaign_id'] ?? null,
            $validated['description'] ?? $defaultDesc
        );

        $payment = $this->paymentService->createPayment([
            'user_id' => $user?->id,
            'payment_gateway_id' => $validated['payment_gateway_id'],
            'payment_type' => $validated['payment_type'] ?? 'donation',
            'payable_type' => $payable['payable_type'],
            'payable_id' => $payable['payable_id'],
            'amount' => $validated['amount'],
            'payment_method' => $paymentMethod,
            'description' => $payable['description'],
            'payer_name' => $payer['payer_name'],
            'payer_email' => $payer['payer_email'],
            'payer_document' => $payer['payer_document'],
        ]);

        $result = $brickPayload
            ? $this->paymentService->processPaymentBrick($payment, $brickPayload)
            : $this->paymentService->processPayment($payment);

        return ['payment' => $payment, 'result' => $result];
    }

    protected function getDefaultDescription(string $type): string
    {
        return match ($type) {
            'donation' => 'Doação',
            'offering' => 'Oferta',
            'ministry_donation' => 'Doação para Ministério',
            'campaign' => 'Doação para Campanha',
            'tithe' => 'Dízimo',
            default => 'Pagamento',
        };
    }
}
