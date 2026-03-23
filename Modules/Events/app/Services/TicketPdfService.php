<?php

namespace Modules\Events\App\Services;

use App\Services\PdfService;
use Illuminate\Support\Facades\Log;
use Modules\Events\App\Models\EventRegistration;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketPdfService
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Generate ticket PDF for a registration.
     */
    public function generateTicketPdf(EventRegistration $registration): string
    {
        $registration->load(['event', 'participants', 'user']);
        $event = $registration->event;

        $qrCode = $this->generateQrCodeBase64($registration->ticket_hash ?? $registration->uuid);

        $html = view('events::public.ticket-pdf', [
            'registration' => $registration,
            'event' => $event,
            'qrCode' => $qrCode,
        ])->render();

        try {
            return $this->pdfService->portrait($html, [0, 0, 0, 0]);
        } catch (\Throwable $e) {
            Log::error('Ticket PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate payment receipt (comprovante) PDF.
     */
    public function generateComprovantePdf(EventRegistration $registration): string
    {
        $registration->load(['event', 'participants', 'user', 'payments']);
        $event = $registration->event;

        $html = view('events::public.comprovante-pdf', [
            'registration' => $registration,
            'event' => $event,
        ])->render();

        try {
            return $this->pdfService->portrait($html, [15, 15, 15, 15]);
        } catch (\Throwable $e) {
            Log::error('Comprovante PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate QR Code as base64 PNG.
     */
    protected function generateQrCodeBase64(string $content): string
    {
        return base64_encode(
            QrCode::format('png')
                ->size(250)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($content)
        );
    }
}
