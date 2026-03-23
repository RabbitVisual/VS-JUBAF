<?php

namespace Modules\ChurchCouncil\App\Services;

use App\Services\PdfService;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChurchCouncilPdfService
{
    public function __construct(
        private PdfService $pdf
    ) {}

    /**
     * Export meeting minutes (ata) as a professional PDF.
     */
    public function downloadMinutesPdf(CouncilMeeting $meeting): StreamedResponse
    {
        $meeting->load([
            'president.user',
            'agendas.presenter.user',
            'agendas.decisionMaker.user',
            'minutesVersions.creator',
            'minutesVersions.signatures.user',
        ]);

        $latestMinutes = $meeting->minutesVersions
            ->sortByDesc('version')
            ->firstWhere('state', 'assembly_approved')
            ?? $meeting->minutesVersions->sortByDesc('version')->firstWhere('state', 'council_approved')
            ?? $meeting->minutesVersions->sortByDesc('version')->first();

        $settings = [
            'church_name' => \App\Models\Settings::get('church_name', config('app.name')),
            'council_name' => ChurchCouncilSettings::councilName(),
        ];

        return $this->pdf->downloadView(
            'churchcouncil::admin.pdf.minutes',
            [
                'meeting' => $meeting,
                'latestMinutes' => $latestMinutes,
                'settings' => $settings,
            ],
            'ata-reuniao-conselho-' . $meeting->id . '.pdf'
        );
    }

    /**
     * Export convocation / edital PDF for a meeting.
     */
    public function downloadConvocationPdf(CouncilMeeting $meeting): StreamedResponse
    {
        $meeting->load(['president.user']);

        $settings = [
            'church_name' => \App\Models\Settings::get('church_name', config('app.name')),
            'council_name' => ChurchCouncilSettings::councilName(),
            'meeting_frequency' => ChurchCouncilSettings::meetingFrequency(),
        ];

        return $this->pdf->downloadView(
            'churchcouncil::admin.pdf.convocation',
            [
                'meeting' => $meeting,
                'settings' => $settings,
            ],
            'edital-convocacao-conselho-' . $meeting->id . '.pdf'
        );
    }
}
