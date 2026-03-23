<?php

namespace Modules\Events\App\Services;

use App\Services\PdfService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventBadge;

class BadgePdfService
{
    public function __construct(
        protected PdfService $pdfService
    ) {}

    /**
     * Generate high-quality badges PDF.
     */
    public function generateBadgesPdf(Event $event, Collection $registrations): string
    {
        $badgeConfig = $event->badges()->first();
        $orientation = $badgeConfig?->orientation ?? 'portrait';
        $paperSize = $badgeConfig?->paper_size ?? 'A4';
        $badgesPerPage = $badgeConfig?->badges_per_page ?? 8;
        $customTemplate = $badgeConfig?->template_html;

        $html = view('events::admin.registrations.export-badges-pdf', [
            'event' => $event,
            'registrations' => $registrations,
            'badgesPerPage' => $badgesPerPage,
            'customTemplate' => $customTemplate,
        ])->render();

        try {
            if ($orientation === 'landscape') {
                return $this->pdfService->landscape($html, $paperSize, [5, 5, 5, 5]);
            }

            return $this->pdfService->portrait($html, $paperSize, [5, 5, 5, 5]);
        } catch (\Throwable $e) {
            Log::error('Badge PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse custom template with participant data.
     */
    public function parseTemplate(string $template, array $data): string
    {
        $replacements = [
            '{{ nome }}' => $data['nome'] ?? '',
            '{{ evento }}' => $data['evento'] ?? '',
            '{{ funcao }}' => $data['funcao'] ?? '',
            '{{ data }}' => $data['data'] ?? '',
            '{{ local }}' => $data['local'] ?? '',
            '{{nome}}' => $data['nome'] ?? '',
            '{{evento}}' => $data['evento'] ?? '',
            '{{funcao}}' => $data['funcao'] ?? '',
            '{{data}}' => $data['data'] ?? '',
            '{{local}}' => $data['local'] ?? '',
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
