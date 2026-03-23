<?php

namespace Modules\Notifications\App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Notifications\App\Models\SystemNotification;
use Modules\Notifications\App\Models\UserNotification;

class SendEventRegistrationNotification
{
    /**
     * Handle the event.
     */
    public function handle(RegistrationConfirmed $event): void
    {
        $registration = $event->registration;

        try {
            // Get user email (from registration user or first participant)
            $userEmail = null;
            $userName = null;
            $userId = null;

            if ($registration->user_id) {
                $user = $registration->user;
                $userEmail = $user->email;
                $userName = $user->name;
                $userId = $user->id;
            } else {
                // For public registrations, use first participant's email
                $firstParticipant = $registration->participants->first();
                if ($firstParticipant) {
                    $userEmail = $firstParticipant->email;
                    $userName = $firstParticipant->name;
                }
            }

            if (! $userEmail) {
                Log::warning("No email found for registration #{$registration->id}");

                return;
            }

            // Create system notification
            $notification = SystemNotification::create([
                'title' => "Inscrição Confirmada: {$registration->event->title}",
                'message' => "Sua inscrição para o evento '{$registration->event->title}' foi confirmada com sucesso! Valor pago: R$ ".number_format($registration->total_amount, 2, ',', '.'),
                'type' => 'success',
                'priority' => 'normal',
                'target_users' => $userId ? [$userId] : null,
                'action_url' => $userId ? route('memberpanel.events.show-registration', $registration->id) : null,
                'action_text' => 'Ver Detalhes',
                'created_by' => 1, // System
            ]);

            // Link notification to user if exists
            if ($userId) {
                UserNotification::create([
                    'user_id' => $userId,
                    'notification_id' => $notification->id,
                    'is_read' => false,
                ]);
            }

            // Send email notification
            $this->sendEmailNotification($registration, $userEmail, $userName);

            Log::info("Notification sent for registration #{$registration->id} to {$userEmail}");
        } catch (\Exception $e) {
            Log::error("Error sending notification for registration #{$registration->id}: ".$e->getMessage());
        }
    }

    /**
     * Send email notification with registration details
     */
    protected function sendEmailNotification($registration, string $email, string $name): void
    {
        try {
            $event = $registration->event;
            $participants = $registration->participants;

            // Prepare email data
            $emailData = [
                'registration' => $registration,
                'event' => $event,
                'participants' => $participants,
                'userName' => $name,
                'totalAmount' => number_format($registration->total_amount, 2, ',', '.'),
            ];

            // Send email using Laravel Mail
            Mail::send('events::emails.registration-confirmed', array_merge($emailData, ['userId' => $registration->user_id]), function ($message) use ($email, $name, $event) {
                $message->to($email, $name)
                    ->subject("Inscrição Confirmada: {$event->title}");
            });
        } catch (\Exception $e) {
            Log::error("Error sending email for registration #{$registration->id}: ".$e->getMessage());
            // Don't throw exception, just log it
        }
    }
}
