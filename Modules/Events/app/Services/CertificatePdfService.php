<?php

namespace Modules\Events\App\Services;

use App\Services\PdfService;
use Illuminate\Support\Facades\Log;
use Modules\Events\App\Models\EventCertificate;
use Modules\Events\App\Models\EventRegistration;

class CertificatePdfService
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Replace placeholders in certificate template with registration data.
     */
    public function replacePlaceholders(string $html, EventRegistration $registration): string
    {
        $registration->load(['event', 'participants', 'user']);
        $event = $registration->event;
        $participantName = $registration->user?->name ?? $registration->participants->first()?->name ?? 'Participante';
        $eventDate = $event->start_date->format('d/m/Y');

        $replace = [
            '{{ nome }}' => $participantName,
            '{{ evento }}' => $event->title,
            '{{ data }}' => $eventDate,
            '{{ data_evento }}' => $eventDate,
            '{{ participante }}' => $participantName,
        ];

        return str_replace(array_keys($replace), array_values($replace), $html);
    }

    /**
     * Generate certificate PDF for a confirmed registration (after release_after).
     */
    public function generateCertificatePdf(EventRegistration $registration, EventCertificate $certificate): string
    {
        $html = $this->replacePlaceholders($certificate->template_html, $registration);
        $html = $this->wrapHtml($html);

        try {
            return $this->pdfService->landscape($html, [15, 15, 15, 15]);
        } catch (\Throwable $e) {
            Log::error('Certificate PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Wrap raw HTML in a complete document structure.
     */
    protected function wrapHtml(string $content): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Georgia, serif;
            margin: 40px;
            color: #333;
            line-height: 1.6;
        }
        h1 { font-size: 28px; margin-bottom: 20px; }
        h2 { font-size: 22px; margin-bottom: 15px; }
        p { margin-bottom: 10px; }
        .certificate-border {
            border: 3px double #8B4513;
            padding: 40px;
            background: linear-gradient(135deg, #fefefe 0%, #f9f6f2 100%);
        }
        .certificate-title {
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .certificate-body {
            text-align: center;
            font-size: 18px;
        }
        .participant-name {
            font-size: 28px;
            font-weight: bold;
            color: #8B4513;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    {$content}
</body>
</html>
HTML;
    }

    /**
     * Check if certificate is released for this registration (release_after <= now()).
     */
    public function isReleased(EventCertificate $certificate): bool
    {
        return $certificate->release_after && $certificate->release_after->isPast();
    }
}
