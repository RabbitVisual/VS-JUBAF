<?php

namespace Modules\PaymentGateway\App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Charge a payment.
     *
     * @param float $amount
     * @param array $data
     * @return array
     */
    public function charge(float $amount, array $data): array;

    /**
     * Refund a payment.
     *
     * @param string $transactionId
     * @param float|null $amount
     * @return array
     */
    public function refund(string $transactionId, ?float $amount = null): array;

    /**
     * Get payment status.
     *
     * @param string $transactionId
     * @return string
     */
    public function getPaymentStatus(string $transactionId): string;

    /**
     * Generate PIX QR Code.
     *
     * @param float $amount
     * @param array $data
     * @return array
     */
    public function generatePixQrCode(float $amount, array $data): array;

    /**
     * Verify webhook signature.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function verifyWebhookSignature(\Illuminate\Http\Request $request): bool;
}
