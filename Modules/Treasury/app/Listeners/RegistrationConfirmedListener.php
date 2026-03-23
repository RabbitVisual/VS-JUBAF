<?php

namespace Modules\Treasury\App\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Treasury\App\Models\FinancialEntry;

/**
 * Single point of creation for FinancialEntry when an event registration is confirmed.
 * Idempotent: uses reference_number REG-{id} to avoid duplicates (Events\CreateFinancialEntry must not create entries).
 */
class RegistrationConfirmedListener
{
    public function handle(RegistrationConfirmed $event): void
    {
        $registration = $event->registration;

        if ($registration->total_amount <= 0) {
            return;
        }

        $reference = 'REG-' . $registration->id;
        if (FinancialEntry::where('reference_number', $reference)->exists()) {
            return;
        }

        $payment = $registration->latestPayment;
        $paymentId = $payment?->id;
        $event = $registration->event;
        $campaignId = $event->treasury_campaign_id ?? null;

        try {
            FinancialEntry::create([
                'type' => 'income',
                'category' => 'Eventos',
                'title' => "Inscrição: {$event->title}",
                'description' => "Inscrição #{$registration->id} para o evento '{$event->title}'. Participantes: {$registration->participants->count()}",
                'amount' => $registration->total_amount,
                'entry_date' => $registration->paid_at ?? now()->toDateString(),
                'user_id' => $registration->user_id,
                'payment_id' => $paymentId,
                'payment_method' => $registration->payment_method ?? 'outros',
                'reference_number' => $reference,
                'campaign_id' => $campaignId,
                'metadata' => [
                    'event_id' => $registration->event_id,
                    'registration_id' => $registration->id,
                    'participants_count' => $registration->participants->count(),
                    'event_title' => $event->title,
                ],
            ]);

            Log::info("Financial entry created for registration #{$registration->id}");
        } catch (\Exception $e) {
            Log::error("Error creating financial entry for registration #{$registration->id}: " . $e->getMessage());
        }
    }
}
