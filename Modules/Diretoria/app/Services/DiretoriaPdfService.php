<?php

namespace Modules\Diretoria\App\Services;

use App\Services\PdfService;
use Modules\Diretoria\App\Models\diretoriaMeeting;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DiretoriaPdfService
{
    public function __construct(
        private PdfService $pdf
    ) {}

    /**
     * Export meeting minutes (ata) as a professional PDF.
     */
    public function downloadMinutesPdf(diretoriaMeeting $meeting): StreamedResponse
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
            ?? $meeting->minutesVersions->sortByDesc('version')->firstWhere('state', 'diretoria_approved')
            ?? $meeting->minutesVersions->sortByDesc('version')->first();

        $settings = [
            'church_name' => \App\Models\Settings::get('church_name', config('app.name')),
            'diretoria_name' => DiretoriaSettings::diretoriaName(),
        ];

        return $this->pdf->downloadView(
            'Diretoria::admin.pdf.minutes',
            [
                'meeting' => $meeting,
                'latestMinutes' => $latestMinutes,
                'settings' => $settings,
            ],
            'ata-reuniao-diretoria-' . $meeting->id . '.pdf'
        );
    }

    /**
     * Export convocation / edital PDF for a meeting.
     */
    public function downloadConvocationPdf(diretoriaMeeting $meeting): StreamedResponse
    {
        $meeting->load(['president.user']);

        $settings = [
            'church_name' => \App\Models\Settings::get('church_name', config('app.name')),
            'diretoria_name' => DiretoriaSettings::diretoriaName(),
            'meeting_frequency' => DiretoriaSettings::meetingFrequency(),
        ];

        return $this->pdf->downloadView(
            'Diretoria::admin.pdf.convocation',
            [
                'meeting' => $meeting,
                'settings' => $settings,
            ],
            'edital-convocacao-diretoria-' . $meeting->id . '.pdf'
        );
    }
}
