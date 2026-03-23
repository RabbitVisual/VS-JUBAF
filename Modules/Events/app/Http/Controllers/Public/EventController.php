<?php

namespace Modules\Events\App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\Events\App\Http\Requests\RegisterEventRequest;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Services\EventService;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\PaymentGateway\App\Services\PaymentService;
use Modules\Events\App\Models\EventRegistration;
use Modules\Events\App\Services\CertificatePdfService;
use Modules\Events\App\Services\TicketPdfService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventController extends Controller
{
    public function __construct(
        protected EventService $eventService,
        protected PaymentService $paymentService,
        protected TicketPdfService $ticketPdfService,
        protected CertificatePdfService $certificatePdfService
    ) {}

    /**
     * Display a listing of public events
     */
    public function index(): View
    {
        $events = Event::published()
            ->public()
            ->where(function ($q) {
                $q->where('end_date', '>=', now())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('end_date')
                            ->where('start_date', '>=', now()->subHours(6));
                    });
            })
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        // Enrich with total participants for "Esgotado" badge (single query)
        $totals = EventRegistration::where('status', EventRegistration::STATUS_CONFIRMED)
            ->whereIn('event_id', $events->pluck('id'))
            ->withCount('participants')
            ->get()
            ->groupBy('event_id')
            ->map(fn ($group) => $group->sum('participants_count'));

        $events->getCollection()->each(function ($event) use ($totals) {
            $event->total_participants = $totals->get($event->id, 0);
        });

        return view('events::public.index', [
            'events' => $events,
            'title' => __('events::messages.events'),
        ]);
    }

    /**
     * Display the specified event (canonical landing: hero, about, location, CTA).
     */
    public function show(Event $event): View
    {
        if ($event->status !== 'published' || ($event->visibility === 'members' && ! auth()->check())) {
            abort(404);
        }

        $event->load(['priceRules', 'batches', 'eventType', 'speakers', 'registrationSegments']);

        // Calculate total participants
        $event->total_participants = $event->confirmedRegistrations()
            ->with(['participants'])
            ->get()
            ->sum(fn ($registration) => $registration->participants->count());

        $isFree = $event->isFree();
        $hasBatches = $event->hasBatches();
        $batches = $hasBatches ? $event->batches()->orderBy('price')->get() : collect();
        $eventUrl = route('events.public.show', $event->slug);
        $registrationConfig = $this->eventService->getRegistrationConfig($event);
        $gateways = $isFree ? collect() : PaymentGateway::active()->ordered()->get()->filter(fn ($g) => $g->isConfigured());
        $user = auth()->user();
        $defaultParticipant = $user ? [
            'name' => $user->name,
            'email' => $user->email,
            'document' => $user->document ?? $user->cpf ?? '',
            'phone' => $user->phone ?? '',
            'birth_date' => $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '',
        ] : null;

        return view('events::public.show', [
            'event' => $event,
            'isFree' => $isFree,
            'hasBatches' => $hasBatches,
            'batches' => $batches,
            'eventUrl' => $eventUrl,
            'title' => $event->title,
            'registrationConfig' => $registrationConfig,
            'gateways' => $gateways,
            'defaultParticipant' => $defaultParticipant,
        ]);
    }

    /**
     * Display the event landing page (exclusive / campaign page): hero, about, schedule, speakers, location, CTA/lots.
     */
    public function landing(Event $event): View
    {
        if ($event->status !== 'published' || ($event->visibility === 'members' && ! auth()->check())) {
            abort(404);
        }

        $event->load(['priceRules', 'batches', 'eventType', 'speakers', 'registrationSegments']);

        $event->total_participants = $event->confirmedRegistrations()
            ->with(['participants'])
            ->get()
            ->sum(fn ($registration) => $registration->participants->count());

        $isFree = $event->isFree();
        $hasBatches = $event->hasBatches();
        $batches = $hasBatches ? $event->batches()->orderBy('price')->get() : collect();
        $eventUrl = route('events.public.landing', $event->slug);
        $title = $event->title;
        $registrationConfig = $this->eventService->getRegistrationConfig($event);
        $gateways = $isFree ? collect() : PaymentGateway::active()->ordered()->get()->filter(fn ($g) => $g->isConfigured());
        $user = auth()->user();
        $defaultParticipant = $user ? [
            'name' => $user->name,
            'email' => $user->email,
            'document' => $user->document ?? $user->cpf ?? '',
            'phone' => $user->phone ?? '',
            'birth_date' => $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '',
        ] : null;

        return view('events::public.landing', compact('event', 'batches', 'isFree', 'hasBatches', 'eventUrl', 'title', 'registrationConfig', 'gateways', 'defaultParticipant'));
    }

    /**
     * Single registration form (legacy). GET /eventos/{slug}/inscrever redirects to landing; this method redirects to landing with modal open.
     */
    public function showRegisterForm(Event $event): \Illuminate\Http\RedirectResponse
    {
        if ($event->status !== 'published' || ($event->visibility === 'members' && ! auth()->check())) {
            abort(404);
        }
        return redirect(route('events.public.landing', $event->slug) . '?openRegistration=1');
    }

    /**
     * Payment/confirmation page for a pending registration (GET /eventos/inscricao/{uuid}/pagar).
     */
    public function showPaymentPage(string $uuid): View|\Illuminate\Http\RedirectResponse
    {
        $registration = EventRegistration::with(['event', 'batch', 'user', 'participants'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (auth()->check() && (int) auth()->id() !== (int) $registration->user_id) {
            abort(403);
        }

        if ($registration->status === EventRegistration::STATUS_CONFIRMED) {
            return redirect()->route('events.public.registration.confirmed', $registration->id)
                ->with('success', __('events::messages.registration_confirmed') ?? 'Inscrição já confirmada.');
        }

        if ($registration->status !== EventRegistration::STATUS_PENDING || $registration->total_amount <= 0) {
            return redirect()->route('events.public.show', $registration->event->slug)
                ->with('info', __('events::messages.registration_payment_not_needed') ?? 'Esta inscrição não requer pagamento.');
        }

        $gateways = PaymentGateway::active()->ordered()->get()->filter(fn ($g) => $g->isConfigured());
        $pendingPayment = $registration->payments()->whereIn('status', ['pending', 'processing'])->latest()->first();
        $expiration = $registration->created_at->addMinutes(15);
        $title = (__('events::messages.payment') ?? 'Pagamento') . ' - ' . $registration->event->title;

        return view('events::public.checkout.confirmation', compact('registration', 'expiration', 'title', 'gateways', 'pendingPayment'));
    }

    /**
     * Start or continue payment for a pending registration (POST /eventos/inscricao/{uuid}/iniciar-pagamento).
     */
    public function startPayment(\Illuminate\Http\Request $request, string $uuid): \Illuminate\Http\RedirectResponse
    {
        $registration = EventRegistration::with(['event', 'participants'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (auth()->check() && (int) auth()->id() !== (int) $registration->user_id) {
            abort(403);
        }

        if ($registration->status !== EventRegistration::STATUS_PENDING || $registration->total_amount <= 0) {
            return redirect()->route('events.public.payment', ['uuid' => $uuid])
                ->with('error', __('events::messages.registration_payment_not_needed') ?? 'Esta inscrição não requer pagamento.');
        }

        $validated = $request->validate([
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'payment_method' => 'nullable|string',
            'brick_payload' => 'nullable|string',
        ]);

        $gateway = PaymentGateway::active()->find($validated['payment_gateway_id']);
        if (! $gateway) {
            return redirect()->route('events.public.payment', ['uuid' => $uuid])
                ->with('error', __('paymentgateway::messages.gateway_invalid') ?? 'Gateway inválido ou inativo.');
        }

        $brickData = null;
        if (! empty($request->brick_payload)) {
            $brickData = json_decode($request->input('brick_payload'), true);
        }

        $paymentMethod = $validated['payment_method'] ?? null;
        if (empty($paymentMethod)) {
            if ($gateway->name === 'stripe') {
                $paymentMethod = 'stripe_checkout';
            } elseif ($gateway->name === 'mercado_pago' && $brickData) {
                $rawMethod = $brickData['payment_method_id'] ?? 'unknown';
                if ($rawMethod === 'bank_transfer' || $rawMethod === 'pix') {
                    $paymentMethod = 'pix';
                } elseif ($rawMethod === 'ticket' || $rawMethod === 'bolbradesco') {
                    $paymentMethod = 'bolbradesco';
                } else {
                    $paymentMethod = $rawMethod;
                }
            } else {
                $paymentMethod = 'unknown';
            }
        }

        $pendingPayment = $registration->payments()->whereIn('status', ['pending', 'processing'])->latest()->first();

        if ($pendingPayment) {
            $pendingPayment->update([
                'payment_gateway_id' => $gateway->id,
                'payment_method' => $paymentMethod,
            ]);
            $payment = $pendingPayment;
        } else {
            $firstParticipant = $registration->participants->first();
            $payerName = $brickData['payer']['first_name'] ?? ($firstParticipant->name ?? 'Visitante');
            if (! empty($brickData['payer']['last_name'])) {
                $payerName .= ' ' . $brickData['payer']['last_name'];
            }
            $payerEmail = $brickData['payer']['email'] ?? ($firstParticipant->email ?? null);
            $payerDocument = $brickData['payer']['identification']['number'] ?? ($firstParticipant->document ?? null);

            $payment = $this->paymentService->createPayment([
                'payment_gateway_id' => $gateway->id,
                'payment_type' => 'event_registration',
                'payable_type' => EventRegistration::class,
                'payable_id' => $registration->id,
                'amount' => $registration->total_amount,
                'currency' => 'BRL',
                'payment_method' => $paymentMethod,
                'description' => (__('events::messages.registration') ?? 'Inscrição') . ': ' . $registration->event->title,
                'payer_name' => $payerName,
                'payer_email' => $payerEmail,
                'payer_document' => $payerDocument,
                'metadata' => [
                    'event_id' => $registration->event_id,
                    'event_title' => $registration->event->title,
                    'registration_id' => $registration->id,
                    'registration_uuid' => $registration->uuid,
                    'participants_count' => $registration->participants->count(),
                ],
            ]);

            $registration->update(['payment_reference' => $payment->transaction_id]);
        }

        try {
            if ($brickData) {
                $result = $this->paymentService->processPaymentBrick($payment, $brickData);
            } else {
                $result = $this->paymentService->processPayment($payment);
            }

            if (isset($result['error']) || (isset($result['status']) && $result['status'] === 'failed')) {
                return redirect()->route('events.public.payment', ['uuid' => $uuid])
                    ->with('error', (__('paymentgateway::messages.payment_error') ?? 'Erro ao processar pagamento: ') . ($result['error'] ?? 'Desconhecido'));
            }

            if (isset($result['redirect_url'])) {
                return redirect($result['redirect_url']);
            }

            return redirect()->route('checkout.show', $payment->transaction_id);
        } catch (\Exception $e) {
            return redirect()->route('events.public.payment', ['uuid' => $uuid])
                ->with('error', (__('paymentgateway::messages.payment_error') ?? 'Erro ao processar pagamento: ') . $e->getMessage());
        }
    }

    /**
     * Show registration confirmed page (public).
     */
    public function showRegistrationConfirmed($registration): View
    {
        $registration = EventRegistration::with(['event', 'participants'])
            ->findOrFail($registration);

        $certificate = $registration->event->certificates()->first();
        $certificate_available = $certificate && $this->certificatePdfService->isReleased($certificate);

        return view('events::public.registration.confirmed', compact('registration', 'certificate_available'));
    }

    /**
     * Download ticket PDF by registration uuid (public link, no auth required).
     */
    public function downloadTicket(string $uuid): StreamedResponse|\Illuminate\Http\Response
    {
        $registration = EventRegistration::with(['event', 'participants', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (! $registration->event->hasTicketEnabled()) {
            abort(404);
        }

        if ($registration->status !== EventRegistration::STATUS_CONFIRMED && empty($registration->ticket_hash)) {
            abort(404, 'Ingresso não disponível.');
        }

        $pdf = $this->ticketPdfService->generateTicketPdf($registration);
        $filename = 'ingresso-'.Str::slug($registration->event->title).'.pdf';

        return response()->streamDownload(
            fn () => print($pdf),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Download certificate PDF by registration uuid (after release_after).
     */
    public function downloadCertificate(string $uuid): StreamedResponse|\Illuminate\Http\Response
    {
        $registration = EventRegistration::with(['event.certificates', 'participants', 'user'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if (! $registration->event->hasCertificateEnabled()) {
            abort(404);
        }

        if ($registration->status !== EventRegistration::STATUS_CONFIRMED) {
            abort(404, 'Inscrição não confirmada.');
        }

        $certificate = $registration->event->certificates()->first();
        if (! $certificate || ! $this->certificatePdfService->isReleased($certificate)) {
            abort(404, 'Certificado ainda não disponível.');
        }

        $pdf = $this->certificatePdfService->generateCertificatePdf($registration, $certificate);
        $filename = 'certificado-'.Str::slug($registration->event->title).'.pdf';

        return response()->streamDownload(
            fn () => print($pdf),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Handle registration
     */
    public function register(RegisterEventRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        // For public registration, if authenticated, redirect to member panel registration
        if (auth()->check()) {
            return redirect()->route('memberpanel.events.show', $event->slug)
                ->with('info', __('events::messages.logged_in_redirect_msg') ?? 'Você está logado. Prossiga com a inscrição no painel de membros.');
        }

        $validated = $request->validated();
        $registrationData = [];

        if (isset($validated['batch_id'])) {
            $registrationData['batch_id'] = $validated['batch_id'];
        }

        // Map "Código promocional" / discount_code from formulário único ou por faixa
        $participants = $validated['participants'] ?? [];
        $firstParticipant = $participants[0] ?? null;
        $discountCode = null;

        if (is_array($firstParticipant)) {
            $custom = $firstParticipant['custom_responses'] ?? [];
            if (is_array($custom)) {
                $discountCode = $custom['discount_code'] ?? ($custom['codigo_promocional'] ?? null);
            }
        }

        if (! $discountCode && isset($validated['discount_code'])) {
            $discountCode = $validated['discount_code'];
        }

        if (is_string($discountCode) && $discountCode !== '') {
            $registrationData['discount_code'] = $discountCode;
        }

        try {
            $registration = $this->eventService->createRegistration(
                $event,
                $validated['participants'],
                auth()->check() ? auth()->id() : null,
                $registrationData
            );

            // Redirect to payment gateway if total_amount > 0, otherwise to confirmation
            if ($registration->total_amount > 0) {
                $request->validate([
                    'payment_gateway_id' => 'required|exists:payment_gateways,id',
                    'payment_method' => 'nullable|string',
                ]);

                $gateway = PaymentGateway::active()->find($request->payment_gateway_id);

                if ($gateway) {
                    // Brick Payload Check
                    $brickData = null;
                    if ($request->has('brick_payload')) {
                         $brickData = json_decode($request->input('brick_payload'), true);
                    }

                    // Determinar payment_method
                    $paymentMethod = $request->payment_method ?? null;
                    if (empty($paymentMethod)) {
                        if ($gateway->name === 'stripe') {
                            $paymentMethod = 'stripe_checkout';
                        } elseif ($gateway->name === 'mercado_pago' && $brickData) {
                            $rawMethod = $brickData['payment_method_id'] ?? 'unknown';
                            if ($rawMethod === 'bank_transfer' || $rawMethod === 'pix') {
                                $paymentMethod = 'pix';
                            } elseif ($rawMethod === 'ticket' || $rawMethod === 'bolbradesco') {
                                $paymentMethod = 'bolbradesco';
                            } else {
                                $paymentMethod = $rawMethod;
                            }
                        }
                    }

                    // Create payment
                    $firstParticipant = $registration->participants->first();
                    $payerName = $brickData['payer']['first_name'] ?? ($firstParticipant->name ?? 'Visitante');
                    if (isset($brickData['payer']['last_name'])) {
                        $payerName .= ' ' . $brickData['payer']['last_name'];
                    }
                    $payerEmail = $brickData['payer']['email'] ?? ($firstParticipant->email ?? null);
                    $payerDocument = $brickData['payer']['identification']['number'] ?? ($firstParticipant->document ?? null);

                    $payment = $this->paymentService->createPayment([
                        'payment_gateway_id' => $gateway->id,
                        'payment_type' => 'event_registration',
                        'payable_type' => EventRegistration::class,
                        'payable_id' => $registration->id,
                        'amount' => $registration->total_amount,
                        'currency' => 'BRL',
                        'payment_method' => $paymentMethod ?? 'unknown',
                        'description' => (__('events::messages.registration') ?? 'Inscrição').": {$event->title}",
                        'payer_name' => $payerName,
                        'payer_email' => $payerEmail,
                        'payer_document' => $payerDocument,
                        'metadata' => [
                            'event_id' => $event->id,
                            'event_title' => $event->title,
                            'registration_id' => $registration->id,
                            'participants_count' => $registration->participants->count(),
                        ],
                    ]);

                    // Update registration with payment reference
                    $registration->update([
                        'payment_reference' => $payment->transaction_id,
                    ]);

                    // Process payment and redirect to gateway or checkout
                    try {
                        if ($brickData) {
                            $paymentResult = $this->paymentService->processPaymentBrick($payment, $brickData);
                        } else {
                            $paymentResult = $this->paymentService->processPayment($payment);
                        }

                        if (isset($paymentResult['error']) || (isset($paymentResult['status']) && $paymentResult['status'] === 'failed')) {
                             return redirect()->route('events.public.registration.pending', $registration->id)
                                ->with('error', (__('paymentgateway::messages.payment_error') ?? 'Erro ao processar pagamento: ').($paymentResult['error'] ?? 'Desconhecido'));
                        }

                        if (isset($paymentResult['redirect_url'])) {
                            return redirect($paymentResult['redirect_url']);
                        }

                        // For PIX or cases without redirect_url, show the checkout/results page
                        return redirect()->route('checkout.show', $payment->transaction_id);
                    } catch (\Exception $e) {
                        // If payment processing fails, redirect to pending page
                        return redirect()->route('events.public.registration.pending', $registration->id)
                            ->with('error', (__('paymentgateway::messages.payment_error') ?? 'Erro ao processar pagamento: ').$e->getMessage());
                    }
                }

                return redirect()->route('events.public.payment', ['uuid' => $registration->uuid])
                    ->with('info', __('events::messages.registration_created_payment_msg') ?? 'Inscrição criada. Prossiga para o pagamento.');
            } else {
                // Free registration - confirm immediately
                $this->eventService->confirmRegistration($registration);

                return redirect()->route('events.public.registration.confirmed', $registration->id)
                    ->with('success', __('events::messages.free_registration_confirmed_msg') ?? 'Inscrição gratuita confirmada com sucesso!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
