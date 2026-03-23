<?php

namespace Modules\Events\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Events\App\Models\EventCertificate;
use Modules\Events\App\Models\EventRegistration;

class SendCertificateAvailableEmailsCommand extends Command
{
    protected $signature = 'events:send-certificate-emails';

    protected $description = 'Send email to participants when certificate becomes available (release_after reached).';

    public function handle(): int
    {
        $certificates = EventCertificate::with('event')
            ->whereNotNull('release_after')
            ->where('release_after', '<=', now())
            ->get();

        $sent = 0;
        foreach ($certificates as $certificate) {
            $registrations = EventRegistration::with(['event', 'participants', 'user'])
                ->where('event_id', $certificate->event_id)
                ->where('status', EventRegistration::STATUS_CONFIRMED)
                ->get();

            foreach ($registrations as $registration) {
                $email = $registration->user?->email ?? $registration->participants->first()?->email;
                $name = $registration->user?->name ?? $registration->participants->first()?->name ?? 'Participante';
                if (! $email) {
                    continue;
                }

                try {
                    $downloadUrl = route('events.public.certificate.download', $registration->uuid);
                    Mail::send('events::emails.certificate-available', [
                        'registration' => $registration,
                        'event' => $registration->event,
                        'downloadUrl' => $downloadUrl,
                        'userName' => $name,
                    ], function ($message) use ($email, $name, $registration) {
                        $message->to($email, $name)
                            ->subject('Certificado disponível: '.$registration->event->title);
                    });
                    $sent++;
                } catch (\Throwable $e) {
                    Log::error("Failed to send certificate email for registration #{$registration->id}: ".$e->getMessage());
                }
            }
        }

        $this->info("Sent {$sent} certificate availability email(s).");
        return 0;
    }
}
