<?php

namespace Modules\Notifications\App\Listeners;

use Modules\Notifications\App\Services\InAppNotificationService;
use Modules\PaymentGateway\App\Models\Payment;

class SendPaymentCompletedNotification
{
    public function __construct(
        protected InAppNotificationService $inApp
    ) {}

    /**
     * Handle payment.completed: notify donor (in-app) and admins.
     *
     * @param  Payment  $payment  Payload from Event::dispatch('payment.completed', $payment)
     */
    public function handle(mixed $payment): void
    {
        if (! $payment instanceof Payment || $payment->status !== 'completed') {
            return;
        }

        $amount = number_format($payment->amount ?? 0, 2, ',', '.');
        $title = 'Pagamento confirmado';
        $message = "Seu pagamento de R$ {$amount} foi confirmado com sucesso.";

        if ($payment->user_id && $payment->relationLoaded('user') === false) {
            $payment->load('user');
        }

        if ($payment->user) {
            $this->inApp->sendToUser($payment->user, $title, $message, [
                'type' => 'success',
                'action_url' => null,
                'action_text' => null,
            ]);
        }

        $this->inApp->sendToAdmins("Novo pagamento recebido: R$ {$amount}", "Pagamento #{$payment->id} concluído.", [
            'type' => 'info',
            'priority' => 'normal',
        ]);
    }
}
