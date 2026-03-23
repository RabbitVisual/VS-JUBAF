<?php

namespace Modules\LiderancaPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventRegistration;
use Modules\Events\App\Services\BadgePdfService;
use Modules\Events\App\Services\EventService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventosController extends Controller
{
    /**
     * Lista de eventos (painel liderancaal).
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Event::class);

        $query = Event::with(['creator', 'priceRules', 'eventType'])->withCount('batches');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }
        if ($request->filled('event_type_id')) {
            $query->where('event_type_id', $request->event_type_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $events = $query->orderBy('start_date', 'desc')->paginate(15)->withQueryString();

        foreach ($events as $event) {
            $event->total_participants = $event->confirmedRegistrations()
                ->with(['participants'])
                ->get()
                ->sum(fn ($r) => $r->participants->count());
        }

        $eventTypes = \Modules\Events\App\Models\EventType::orderBy('order')->get();

        return view('events::liderancapanel.index', compact('events', 'eventTypes'));
    }

    /**
     * Detalhe do evento (somente leitura, com links para inscrições e check-in).
     */
    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load(['creator', 'priceRules', 'eventType', 'registrations']);
        $event->total_participants = $event->confirmedRegistrations()
            ->with(['participants'])
            ->get()
            ->sum(fn ($r) => $r->participants->count());

        return view('events::liderancapanel.show', compact('event'));
    }

    /**
     * Lista de inscrições do evento.
     */
    public function registrationsIndex(Event $event, Request $request): View
    {
        $this->authorize('manageRegistrations', $event);

        $query = $event->registrations()->with(['user', 'participants']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('events::liderancapanel.registrations.index', compact('event', 'registrations'));
    }

    /**
     * Detalhe de uma inscrição.
     */
    public function registrationShow(Event $event, EventRegistration $registration): View
    {
        $this->authorize('manageRegistrations', $event);
        $registration->load(['user', 'participants']);

        return view('events::liderancapanel.registrations.show', compact('event', 'registration'));
    }

    /**
     * Confirmar inscrição.
     */
    public function confirmRegistration(Request $request, Event $event, EventRegistration $registration): RedirectResponse
    {
        $this->authorize('manageRegistrations', $event);

        try {
            app(EventService::class)->confirmRegistration($registration);
            return redirect()->route('lideranca.eventos.registrations.show', [$event, $registration])
                ->with('success', __('events::messages.registration_confirmed_success') ?? 'Inscrição confirmada com sucesso!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancelar inscrição.
     */
    public function cancelRegistration(Request $request, Event $event, EventRegistration $registration): RedirectResponse
    {
        $this->authorize('manageRegistrations', $event);

        $reason = $request->validate(['reason' => 'nullable|string|max:500'])['reason'] ?? null;
        app(EventService::class)->cancelRegistration($registration, $reason ?? __('events::messages.cancelled_by_admin') ?? 'Cancelado pelo administrador');

        return redirect()->route('lideranca.eventos.registrations.show', [$event, $registration])
            ->with('success', __('events::messages.registration_cancelled_success') ?? 'Inscrição cancelada com sucesso!');
    }

    /**
     * Exportar lista de presença PDF.
     */
    public function exportPdf(Event $event, Request $request): StreamedResponse
    {
        $this->authorize('export', $event);

        $query = $event->registrations()->with(['user', 'participants']);
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $registrations = $query->orderBy('created_at', 'desc')->get();

        return app(PdfService::class)->downloadView(
            'events::admin.registrations.export-pdf',
            compact('event', 'registrations'),
            'lista-presenca-' . $event->slug . '.pdf',
            'A4',
            'Portrait',
            [10, 10, 10, 10]
        );
    }

    /**
     * Exportar crachás PDF.
     */
    public function exportBadges(Event $event, Request $request): StreamedResponse
    {
        $this->authorize('export', $event);

        $query = $event->registrations()->with(['user', 'participants']);
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $registrations = $query->orderBy('created_at', 'desc')->get();
        $pdf = app(BadgePdfService::class)->generateBadgesPdf($event, $registrations);

        return response()->streamDownload(
            fn () => print($pdf),
            'crachas-' . $event->slug . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Exportar inscrições Excel/CSV.
     */
    public function exportExcel(Event $event, Request $request)
    {
        $this->authorize('export', $event);

        $query = $event->registrations()->with(['user', 'participants']);
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        $registrations = $query->orderBy('created_at', 'desc')->get();

        $filename = 'inscricoes_' . $event->slug . '_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($registrations) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($file, [
                'ID', 'Nome do Responsável', 'Email', 'Status', 'Valor Total', 'Data Inscrição', 'Data Pagamento', 'Participantes', 'Idades',
            ], ';');
            foreach ($registrations as $registration) {
                $participants = $registration->participants;
                $names = $participants->pluck('name')->implode(', ');
                $ages = $participants->map(fn ($p) => $p->birth_date ? \Carbon\Carbon::parse($p->birth_date)->age : (__('events::messages.not_informed') ?? 'Não informado'))->implode(', ');
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
     * Tela de check-in (scanner).
     */
    public function checkinIndex(): View
    {
        $this->authorize('checkin', Event::class);

        return view('events::liderancapanel.checkin.scanner');
    }

    /**
     * Validar check-in (QR / código).
     */
    public function checkinValidate(Request $request)
    {
        $this->authorize('checkin', Event::class);

        $request->validate(['ticket_hash' => 'required|string']);
        $registration = EventRegistration::where('ticket_hash', $request->ticket_hash)->first();

        if (! $registration) {
            return response()->json(['success' => false, 'message' => 'Ingresso não encontrado.']);
        }
        if ($registration->status !== EventRegistration::STATUS_CONFIRMED) {
            return response()->json(['success' => false, 'message' => 'Ingresso não confirmado (Status: ' . $registration->status . ').']);
        }
        if ($registration->checked_in_at) {
            return response()->json(['success' => false, 'message' => 'ATENÇÃO: Este ingresso JÁ FOI UTILIZADO em ' . $registration->checked_in_at->format('d/m H:i') . '.']);
        }

        $registration->update(['checked_in_at' => now()]);

        return response()->json([
            'success' => true,
            'user_name' => $registration->user->name ?? 'Visitante',
            'ticket_type' => $registration->batch->name ?? 'Geral',
        ]);
    }
}
