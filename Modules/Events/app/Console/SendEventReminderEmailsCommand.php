<?php

namespace Modules\Events\App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventRegistration;

class SendEventReminderEmailsCommand extends Command
{
    protected $signature = 'events:send-reminders';

    protected $description = 'Send reminder email to participants 1 day before event start.';

    public function handle(): int
    {
        $tomorrow = Carbon::tomorrow()->toDateString();
        $events = Event::whereDate('start_date', $tomorrow)
            ->where('status', Event::STATUS_PUBLISHED)
            ->get();

        $sent = 0;
        foreach ($events as $event) {
            $registrations = EventRegistration::with(['event', 'participants', 'user'])
                ->where('event_id', $event->id)
                ->where('status', EventRegistration::STATUS_CONFIRMED)
                ->get();

            foreach ($registrations as $registration) {
                $email = $registration->user?->email ?? $registration->participants->first()?->email;
                $name = $registration->user?->name ?? $registration->participants->first()?->name ?? 'Participante';
                if (! $email) {
                    continue;
                }

                try {
                    $ticketUrl = $registration->uuid ? route('events.public.ticket.download', $registration->uuid) : null;
                    Mail::send('events::emails.event-reminder', [
                        'registration' => $registration,
                        'event' => $event,
                        'userName' => $name,
                        'ticketUrl' => $ticketUrl,
                    ], function ($message) use ($email, $name, $event) {
                        $message->to($email, $name)
                            ->subject('Lembrete: '.$event->title.' amanhã!');
                    });
                    $sent++;
                } catch (\Throwable $e) {
                    $this->error("Failed registration #{$registration->id}: ".$e->getMessage());
                }
            }
        }

        $this->info("Sent {$sent} reminder email(s).");
        return 0;
    }
}
