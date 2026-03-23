<?php

namespace Modules\Events\App\Http\Controllers\Public;

/**
 * @deprecated Legacy controller (eventos-v2 reserve-then-pay flow). Not registered in routes.
 * Payment page is EventController::showPaymentPage + startPayment. Ticket download is EventController::downloadTicket (route events.public.ticket.download).
 */

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventBatch;
use Modules\Events\App\Models\EventRegistration;
use Modules\Events\App\Services\TicketConcurrencyService;
use Modules\Events\App\Services\QrCodeGeneratorService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Events\App\Services\TicketPdfService;

class CheckoutController extends Controller
{
    public function __construct(
        protected TicketConcurrencyService $ticketService,
        protected QrCodeGeneratorService $qrCodeService,
        protected TicketPdfService $ticketPdfService
    ) {}

    /**
     * Process the checkout start (Reserve Ticket).
     */
    public function process(Request $request, $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('url.intended', url()->current());
        }

        $event = Event::where('slug', $slug)->firstOrFail();

        $request->validate([
            'batch_id' => 'required|exists:event_batches,id',
        ]);

        $batch = EventBatch::findOrFail($request->batch_id);

        if ($batch->event_id !== $event->id) {
            abort(403, 'Este lote não pertence a este evento.');
        }

        try {
            // Attempt to reserve the ticket using Atomic Locks
            $registration = $this->ticketService->reserveTicket($batch, Auth::user());

            // Success: Redirect to Confirmation/Payment page
            return redirect()->route('events.public.payment', ['uuid' => $registration->uuid]);

        } catch (Exception $e) {
            return back()->with('error', 'Erro ao processar reserva: ' . $e->getMessage());
        }
    }

    /**
     * Confirmation page (Timer & Payment).
     */
    public function confirmation($uuid)
    {
        $registration = EventRegistration::with(['event', 'batch', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Verify if user owns this registration
        if (Auth::id() !== $registration->user_id) {
            abort(403);
        }

        // Calculate expiration (e.g. 15 mins from creation)
        $expiration = $registration->created_at->addMinutes(15);

        // If expired and not paid, we might want to show expired state immediately
        // But the view handles the timer.
        // If status is already 'confirmed', the timer shouldn't matter (view logic handles success state?)
        // The view "checkout/confirmation.blade.php" seems designed for the "Pending" state with a timer.
        // If confirmed, we should probably show a different view or the same view adapted.
        // For this task, let's assume it handles pending state primarily.

        return view('events::public.checkout.confirmation', compact('registration', 'expiration'));
    }

    /**
     * Download Ticket PDF
     */
    public function downloadTicket($uuid)
    {
        $registration = EventRegistration::with(['event', 'batch', 'user', 'participants'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (Auth::id() !== $registration->user_id) {
            return redirect()->route('events.public.ticket.download', ['uuid' => $uuid]);
        }

        $pdf = $this->ticketPdfService->generateTicketPdf($registration);
        $filename = 'ingresso-'.$registration->event->slug.'.pdf';

        return response()->streamDownload(
            fn () => print($pdf),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }
}
