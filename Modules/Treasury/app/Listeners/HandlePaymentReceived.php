<?php

namespace Modules\Treasury\App\Listeners;

use Modules\PaymentGateway\App\Events\PaymentReceived;
use Modules\Treasury\App\Models\FinancialEntry;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlePaymentReceived
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $payment = $event->payment;

        // Check if entry already exists to prevent duplication
        // Assuming payment_id or transaction_id linkage
        // Since the FinancialEntry model wasn't provided, assuming standard fields based on context
        // or a generic creation logic. Ideally, we link via payment_id column.

        // We will assume a 'payment_id' column exists or we store it in description/notes.
        // Based on previous context, Payment model has financialEntry relationship.

        if ($payment->financialEntry) {
            return;
        }

        // Event registration income is created only by RegistrationConfirmedListener (single source of truth).
        if ($payment->payment_type === 'event_registration') {
            return;
        }

        // Marketplace order income is created only by CreateMarketplaceTreasuryEntryListener.
        if ($payment->payment_type === 'marketplace_order') {
            return;
        }

        $ministryId = null;
        $campaignId = null;
        $category = 'Doação';

        // Resolve Ministry or Campaign from payable relationship
        if ($payment->payable_type === 'Modules\Ministries\App\Models\Ministry') {
            $ministryId = $payment->payable_id;
            $category = 'Ministério';
        } elseif ($payment->payable_type === 'Modules\Treasury\App\Models\Campaign') {
            $campaignId = $payment->payable_id;
            $category = 'Campanha';
        }

        // Specific category mapping based on payment_type
        if ($payment->payment_type === 'tithe') {
            $category = 'Dízimo';
        } elseif ($payment->payment_type === 'offering') {
            $category = 'Oferta';
        } elseif ($payment->payment_type === 'event_registration') {
            $category = 'Evento';
        }

        FinancialEntry::create([
            'title' => ($payment->payment_type === 'tithe' ? 'Dízimo' : 'Doação') . ' - ' . ($payment->payer_name ?? 'Anônimo'),
            'description' => $payment->description ?? 'Recebimento via Gateway',
            'amount' => $payment->amount,
            'type' => 'income',
            'category' => $category,
            'entry_date' => $payment->paid_at ?? now(),
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'ministry_id' => $ministryId,
            'campaign_id' => $campaignId,
            'payment_method' => $payment->payment_method ?? 'gateway',
            'reference_number' => $payment->transaction_id,
            'metadata' => [
                'gateway_transaction_id' => $payment->gateway_transaction_id,
                'gateway_name' => optional($payment->gateway)->name,
            ],
        ]);
    }
}
