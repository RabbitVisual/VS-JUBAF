<?php

namespace Modules\SocialAction\App\Services;

use App\Services\PdfService;
use Modules\SocialAction\App\Models\SocialAssistance;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptGeneratorService
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Generate a professional receipt PDF for social assistance.
     */
    public function generate(SocialAssistance $assistance): StreamedResponse
    {
        return $this->pdfService->downloadView(
            'socialaction::admin.assistances.receipt',
            compact('assistance'),
            "recibo-assistencia-{$assistance->id}.pdf",
            'A4',
            'Portrait',
            [15, 15, 15, 15]
        );
    }

    /**
     * Generate receipt and return as string (for email attachment, etc).
     */
    public function generateString(SocialAssistance $assistance): string
    {
        $html = view('socialaction::admin.assistances.receipt', compact('assistance'))->render();
        return $this->pdfService->portrait($html, [15, 15, 15, 15]);
    }
}
