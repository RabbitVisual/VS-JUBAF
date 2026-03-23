<?php

namespace Modules\Events\App\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Events\App\Events\RegistrationConfirmed;

class SendTicketEmail
{
    /**
     * Handle the event. Confirmation email (with ticket download link) is sent by
     * Notifications\SendEventRegistrationNotification using events::emails.registration-confirmed.
     */
    public function handle(RegistrationConfirmed $event): void
    {
        $registration = $event->registration;
        Log::info("Registration confirmed #{$registration->id}, ticket download: ".route('events.public.ticket.download', $registration->uuid));
    }
}
