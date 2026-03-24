<?php

namespace Modules\Events\App\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Events\App\Services\EventService;
use Modules\Events\App\Models\EventRegistration;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Notifications\App\Services\InAppNotificationService;

class ConfirmRegistrationOnPaymentCompleted
{
    public function __construct(
        protected EventService $eventService,
        protected InAppNotificationService $notificationService
    ) {}

    /**
     * Handle payment completion for event registrations.
     * Listens to 'payment.completed' (payload = Payment model) from PaymentObserver.
     */
    public function handle($payload): void
    {
        $payment = $payload instanceof Payment
            ? $payload
            : (is_object($payload) && isset($payload->payment) ? $payload->payment : null);

        if (! $payment || ! $payment instanceof Payment) {
            return;
        }

        if ($payment->payment_type !== 'event_registration') {
            return;
        }

        if ($payment->status !== 'completed') {
            return;
        }

        try {
            // Find registration by payable relationship
            $registration = EventRegistration::where('id', $payment->payable_id)
                ->where('status', EventRegistration::STATUS_PENDING)
                ->with(['event', 'user']) // eager load pra notificação
                ->first();

            if (! $registration) {
                return;
            }

            // Update registration with payment info
            $registration->update([
                'payment_method' => $payment->payment_method ?? 'online',
                'payment_reference' => $payment->transaction_id,
                'paid_at' => $payment->paid_at ?? now(),
            ]);

            // Confirm registration (this will dispatch RegistrationConfirmed event)
            $this->eventService->confirmRegistration($registration);

            // Notify user about victory! (Passo 4)
            if ($registration->user) {
                $this->notificationService->sendToUser(
                    $registration->user,
                    "Inscrição Confirmada!",
                    "Sua vaga no evento '{$registration->event->title}' está garantida! Prepare as malas!",
                    [
                        'type' => 'success',
                        'action_url' => route('memberpanel.events.show-registration', $registration),
                        'action_text' => 'Ver Meu Ingresso',
                    ]
                );
            }

            Log::info("Registration #{$registration->id} confirmed after payment #{$payment->id} completion");
        } catch (\Exception $e) {
            Log::error("Error confirming registration for payment #{$payment->id}: ".$e->getMessage());
        }
    }
}
