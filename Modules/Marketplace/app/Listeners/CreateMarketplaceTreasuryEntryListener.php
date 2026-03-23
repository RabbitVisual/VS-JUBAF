<?php

namespace Modules\Marketplace\Listeners;

use Modules\Marketplace\Models\Order;
use Modules\Notifications\App\Services\InAppNotificationService;
use Modules\PaymentGateway\App\Events\PaymentReceived;
use Modules\Treasury\App\Models\FinancialEntry;

class CreateMarketplaceTreasuryEntryListener
{
    public function __construct(
        protected InAppNotificationService $notifications
    ) {}

    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;

        if ($payment->payment_type !== 'marketplace_order') {
            return;
        }

        if ($payment->financialEntry) {
            return;
        }

        $order = Order::find($payment->payable_id);
        if (! $order) {
            return;
        }

        $order->update([
            'status' => Order::STATUS_PAID,
            'paid_at' => $payment->paid_at ?? now(),
        ]);

        $user = $order->user;
        if ($user) {
            $this->notifications->sendToUser($user, 'Pedido aprovado!', 'Seu pedido #' . $order->uuid . ' foi confirmado.', [
                'type' => 'success',
                'action_url' => route('memberpanel.marketplace.orders.show', $order->uuid),
                'action_text' => 'Ver pedido',
            ]);
        }

        $campaignId = $order->campaign_id;
        $referenceNumber = 'ORDER-' . $order->uuid;

        if (FinancialEntry::where('reference_number', $referenceNumber)->exists()) {
            return;
        }

        FinancialEntry::create([
            'title' => 'Loja - ' . ($payment->payer_name ?? 'Anônimo') . ' (Pedido ' . $order->uuid . ')',
            'description' => $payment->description ?? 'Venda Loja Missionária',
            'amount' => $payment->amount,
            'type' => 'income',
            'category' => 'Loja',
            'entry_date' => $payment->paid_at ?? now(),
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'campaign_id' => $campaignId,
            'payment_method' => $payment->payment_method ?? 'gateway',
            'reference_number' => $referenceNumber,
            'metadata' => array_merge($payment->metadata ?? [], [
                'gateway_transaction_id' => $payment->gateway_transaction_id,
                'gateway_name' => optional($payment->gateway)->name,
                'marketplace_order_id' => $order->id,
                'marketplace_order_uuid' => $order->uuid,
            ]),
        ]);

        if ($campaignId && method_exists(\Modules\Treasury\App\Models\Campaign::class, 'updateCurrentAmount')) {
            \Modules\Treasury\App\Models\Campaign::find($campaignId)?->updateCurrentAmount();
        }
    }
}
