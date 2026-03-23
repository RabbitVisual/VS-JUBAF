<?php

namespace Modules\Events\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventRegistration;
use Modules\Events\App\Services\BadgePdfService;
use Modules\Events\App\Services\EventService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationController extends Controller
{
    public function __construct(
        protected EventService $eventService,
        protected BadgePdfService $badgePdfService,
        protected PdfService $pdfService
    ) {}

    /**
     * Display registrations for an event
     */
    public function index(Event $event, Request $request): View
    {
        $this->authorize('manageRegistrations', $event);
        $query = $event->registrations()->with(['user', 'participants']);

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('age_group') && ! empty($request->age_group)) {
            $ageGroup = $request->age_group;
            $query->whereHas('participants', function ($q) use ($ageGroup) {
                $today = now();
                switch ($ageGroup) {
                    case '0-12':
                        $q->whereDate('birth_date', '>=', $today->copy()->subYears(12));
                        break;
                    case '13-17':
                        $q->whereDate('birth_date', '>=', $today->copy()->subYears(18))
                            ->whereDate('birth_date', '<', $today->copy()->subYears(12));
                        break;
                    case '18-29':
                        $q->whereDate('birth_date', '>=', $today->copy()->subYears(30))
                            ->whereDate('birth_date', '<', $today->copy()->subYears(18));
                        break;
                    case '30-49':
                        $q->whereDate('birth_date', '>=', $today->copy()->subYears(50))
                            ->whereDate('birth_date', '<', $today->copy()->subYears(30));
                        break;
                    case '50-64':
                        $q->whereDate('birth_date', '>=', $today->copy()->subYears(65))
                            ->whereDate('birth_date', '<', $today->copy()->subYears(50));
                        break;
                    case '65+':
                        $q->whereDate('birth_date', '<', $today->copy()->subYears(65));
                        break;
                }
            });
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('events::admin.registrations.index', compact('event', 'registrations'));
    }

    /**
     * Show registration details
     */
    public function show(Event $event, EventRegistration $registration): View
    {
        $this->authorize('manageRegistrations', $event);
        $registration->load(['user', 'participants']);

        return view('events::admin.registrations.show', compact('event', 'registration'));
    }

    /**
     * Export registrations to PDF
     */
    public function exportPdf(Event $event, Request $request): StreamedResponse
    {
        $this->authorize('export', $event);
        $query = $event->registrations()->with(['user', 'participants']);

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->get();

        return $this->pdfService->downloadView(
            'events::admin.registrations.export-pdf',
            compact('event', 'registrations'),
            'lista-presenca-' . $event->slug . '.pdf',
            'A4',
            'Portrait',
            [10, 10, 10, 10]
        );
    }

    /**
     * Export badges to PDF (high quality via html2pdf.app/mPDF).
     */
    public function exportBadges(Event $event, Request $request): StreamedResponse
    {
        $this->authorize('export', $event);
        $query = $event->registrations()->with(['user', 'participants']);

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->get();
        $pdf = $this->badgePdfService->generateBadgesPdf($event, $registrations);
        $filename = 'crachas-' . $event->slug . '.pdf';

        return response()->streamDownload(
            fn () => print($pdf),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Export registrations to Excel
     */
    public function exportExcel(Event $event, Request $request)
    {
        $this->authorize('export', $event);
        $query = $event->registrations()->with(['user', 'participants']);

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->get();

        $filename = 'inscricoes_'.$event->slug.'_'.now()->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($registrations) {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'ID',
                'Nome do Responsável',
                'Email',
                'Status',
                'Valor Total',
                'Data Inscrição',
                'Data Pagamento',
                'Participantes',
                'Idades',
            ], ';');

            foreach ($registrations as $registration) {
                $participants = $registration->participants;
                $names = $participants->pluck('name')->implode(', ');
                $ages = $participants->map(function ($p) {
                    return $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->age : (__('events::messages.not_informed') ?? 'Não informado');
                })->implode(', ');

                fputcsv($file, [
                    $registration->id,
                    $registration->user->name ?? (__('events::messages.visitor') ?? 'Visitante'),
                    $registration->user->email ?? (__('events::messages.not_informed') ?? 'Não informado'),
                    $registration->status_display,
                    number_format($registration->total_amount, 2, ',', '.'),
                    $registration->created_at->format('d/m/Y H:i'),
                    $registration->paid_at ? $registration->paid_at->format('d/m/Y H:i') : (__('events::messages.pending') ?? 'Pendente'),
                    $names,
                    $ages,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Confirm a registration
     */
    public function confirm(Request $request, Event $event, EventRegistration $registration): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('manageRegistrations', $event);
        try {
            $this->eventService->confirmRegistration($registration);

            return redirect()->back()
                ->with('success', __('events::messages.registration_confirmed_success') ?? 'Inscrição confirmada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel a registration
     */
    public function cancel(Request $request, Event $event, EventRegistration $registration): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('manageRegistrations', $event);
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $this->eventService->cancelRegistration($registration, $validated['reason'] ?? (__('events::messages.cancelled_by_admin') ?? 'Cancelado pelo administrador'));

        return redirect()->back()
            ->with('success', __('events::messages.registration_cancelled_success') ?? 'Inscrição cancelada com sucesso!');
    }
}
