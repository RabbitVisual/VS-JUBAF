<?php

namespace Modules\Events\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Events\App\Http\Requests\RegisterEventRequest;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventRegistration;
use Modules\Events\App\Services\EventService;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\PaymentGateway\App\Services\PaymentService;

class EventController extends Controller
{
    protected EventService $eventService;

    protected PaymentService $paymentService;

    public function __construct(EventService $eventService, PaymentService $paymentService)
    {
        $this->eventService = $eventService;
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of events for members
     */
    public function index(): View
    {
        $events = Event::published()
            ->members()
            ->where(function ($q) {
                $q->where('end_date', '>=', now())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('end_date')
                            ->where('start_date', '>=', now()->subHours(6));
                    });
            })
            ->with('priceRules')
            ->orderBy('start_date', 'asc')
            ->paginate(12);

        // Calculate total participants for each event
        foreach ($events as $event) {
            $event->total_participants = $event->confirmedRegistrations()
                ->with(['participants'])
                ->get()
                ->sum(function ($registration) {
                    return $registration->participants->count();
                });
        }

        return view('events::memberpanel.index', compact('events'));
    }

    /**
     * Display the specified event (only events visible to members: visibility in ['members','both'])
     */
    public function show(Event $event): View
    {
        if ($event->status !== 'published') {
            abort(404);
        }
        if (! in_array($event->visibility, [Event::VISIBILITY_MEMBERS, Event::VISIBILITY_BOTH], true)) {
            abort(404, __('events::messages.event_not_available_members') ?? 'Este evento não está disponível no painel de membros.');
        }

        $event->load(['priceRules', 'registrationSegments', 'speakers']);

        // Calculate total participants
        $event->total_participants = $event->confirmedRegistrations()
            ->withCount('participants')
            ->get()
            ->sum('participants_count');

        // Pre-fill first participant with user data if authenticated
        $user = auth()->user();
        $defaultParticipant = null;
        if ($user) {
            $defaultParticipant = [
                'name' => $user->name,
                'email' => $user->email,
                'birth_date' => $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : null,
                'document' => $user->cpf ?? null,
                'cpf' => $user->cpf ?? null,
                'phone' => $user->cellphone ?? $user->phone ?? null,
                'gender' => $user->gender ?? null,
                'city' => $user->city ?? null,
                'state' => $user->state ?? null,
                'address' => $user->address ?? null,
                'zip_code' => $user->zip_code ?? null,
                'neighborhood' => $user->neighborhood ?? null,
            ];
        }

        $registrationConfig = $this->eventService->getRegistrationConfig($event);

        // Fetch active and configured gateways
        $gateways = PaymentGateway::active()
            ->ordered()
            ->get()
            ->filter(fn ($g) => $g->isConfigured());

        return view('events::memberpanel.show', compact('event', 'defaultParticipant', 'gateways', 'registrationConfig'));
    }

    /**
     * Handle registration
     */
    public function register(RegisterEventRequest $request, Event $event): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();

        $registrationData = [];

        // Extrai código promocional (discount_code / codigo_promocional) do primeiro participante ou do payload
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
                auth()->id(),
                $registrationData
            );

            // Redirect to payment gateway if total_amount > 0, otherwise confirm immediately
            if ($registration->total_amount > 0) {
                $request->validate([
                    'payment_gateway_id' => 'required|exists:payment_gateways,id',
                    'payment_method' => 'required|string',
                    'payer_document' => 'nullable|string',
                ]);

                $gateway = PaymentGateway::active()->find($request->payment_gateway_id);

                if ($gateway) {
                    // Brick Payload Check
                    $brickData = null;
                    if ($request->has('brick_payload')) {
                        $brickData = json_decode($request->input('brick_payload'), true);
                    }

                    // Determinar payment_method
                    $paymentMethod = $request->payment_method;
                    if ($gateway->name === 'mercado_pago' && $brickData) {
                        $rawMethod = $brickData['payment_method_id'] ?? 'unknown';
                        if ($rawMethod === 'bank_transfer' || $rawMethod === 'pix') {
                            $paymentMethod = 'pix';
                        } elseif ($rawMethod === 'ticket' || $rawMethod === 'bolbradesco') {
                            $paymentMethod = 'bolbradesco';
                        } else {
                            $paymentMethod = $rawMethod;
                        }
                    }

                    // Payer Data
                    $user = auth()->user();
                    $firstParticipant = $registration->participants->first();
                    $payerName = $brickData['payer']['first_name'] ?? ($firstParticipant->name ?? $user->name);
                    if (isset($brickData['payer']['last_name'])) {
                        $payerName .= ' '.$brickData['payer']['last_name'];
                    }
                    $payerEmail = $brickData['payer']['email'] ?? ($firstParticipant->email ?? $user->email);
                    $payerDocument = $brickData['payer']['identification']['number'] ?? ($request->payer_document ?? $firstParticipant->document ?? $user->cpf);

                    // Create payment
                    $payment = $this->paymentService->createPayment([
                        'user_id' => auth()->id(),
                        'payment_gateway_id' => $gateway->id,
                        'payment_type' => 'event_registration',
                        'payable_type' => EventRegistration::class,
                        'payable_id' => $registration->id,
                        'amount' => $registration->total_amount,
                        'currency' => 'BRL',
                        'payment_method' => $paymentMethod,
                        'description' => "Inscrição: {$event->title}",
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

                    // Process payment
                    try {
                        if ($brickData) {
                            $paymentResult = $this->paymentService->processPaymentBrick($payment, $brickData);
                        } else {
                            $paymentResult = $this->paymentService->processPayment($payment);
                        }

                        // Validate Result (Check for error or failed status)
                        if (isset($paymentResult['error']) || (isset($paymentResult['status']) && $paymentResult['status'] === 'failed')) {
                            return redirect()->route('memberpanel.events.my-registrations')
                                ->with('error', 'Erro ao processar pagamento: '.($paymentResult['error'] ?? 'Desconhecido'));
                        }

                        if (isset($paymentResult['redirect_url'])) {
                            return redirect($paymentResult['redirect_url']);
                        }
                    } catch (\Exception $e) {
                        return redirect()->route('memberpanel.events.my-registrations')
                            ->with('error', 'Erro ao processar pagamento: '.$e->getMessage());
                    }
                }

                return redirect()->route('memberpanel.events.my-registrations')
                    ->with('info', __('events::messages.registration_created_payment_msg') ?? 'Inscrição criada. Prossiga para o pagamento.');
            } else {
                // Free registration - confirm immediately
                $this->eventService->confirmRegistration($registration);

                return redirect()->route('memberpanel.events.registration.confirmed', $registration->id)
                    ->with('success', __('events::messages.free_registration_confirmed_msg') ?? 'Inscrição gratuita confirmada com sucesso!');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display user's registrations
     */
    public function myRegistrations(): View
    {
        $registrations = EventRegistration::where('user_id', auth()->id())
            ->with(['event', 'participants'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('events::memberpanel.my-registrations', compact('registrations'));
    }

    /**
     * Display a specific member registration
     */
    public function showRegistration(EventRegistration $registration): View
    {
        // Ensure the registration belongs to the authenticated user
        if ($registration->user_id !== auth()->id()) {
            abort(403, __('events::messages.unauthorized_access') ?? 'Unauthorized access to this registration.');
        }

        $registration->load(['event.priceRules', 'participants', 'latestPayment']);

        return view('events::memberpanel.registration-show', compact('registration'));
    }

    /**
     * Mostra formulário para trocar o gateway de uma inscrição pendente
     */
    public function retryRegistration(EventRegistration $registration)
    {
        // Verificar se a inscrição pertence ao usuário e está pendente
        if ($registration->user_id !== auth()->id() || $registration->status !== 'pending') {
            abort(403);
        }

        $registration->load(['event', 'latestPayment']);

        $gateways = PaymentGateway::active()
            ->ordered()
            ->get()
            ->filter(fn ($g) => $g->isConfigured());

        return view('events::memberpanel.retry-registration', compact('registration', 'gateways'));
    }

    /**
     * Atualiza o gateway e método de uma inscrição pendente
     */
    public function updateRegistrationGateway(Request $request, EventRegistration $registration)
    {
        if ($registration->user_id !== auth()->id() || $registration->status !== 'pending') {
            abort(403);
        }

        $validated = $request->validate([
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'payment_method' => 'nullable|string',
            'brick_payload' => 'nullable|string',
        ]);

        $gateway = PaymentGateway::active()->find($validated['payment_gateway_id']);
        if (! $gateway) {
            return back()->with('error', 'Gateway inválido ou inativo.');
        }

        $brickData = null;
        if ($request->has('brick_payload') && ! empty($request->brick_payload)) {
            $brickData = json_decode($request->input('brick_payload'), true);
        }

        // Determinar payment_method
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
            }
        }

        // Se já existe um pagamento pendente, vamos atualizar ele em vez de criar outro
        $payment = $registration->latestPayment;

        if ($payment && $payment->status === 'pending') {
            $payment->update([
                'payment_gateway_id' => $gateway->id,
                'payment_method' => $paymentMethod ?? 'unknown',
            ]);
        } else {
            // Se não existe ou não está pendente (ex: expirou), criamos um novo
            $user = auth()->user();
            $payment = $this->paymentService->createPayment([
                'user_id' => auth()->id(),
                'payment_gateway_id' => $gateway->id,
                'payment_type' => 'event_registration',
                'payable_type' => EventRegistration::class,
                'payable_id' => $registration->id,
                'amount' => $registration->total_amount,
                'currency' => 'BRL',
                'payment_method' => $paymentMethod ?? 'unknown',
                'description' => 'Inscrição: '.$registration->event->title,
                'payer_name' => $user->name,
                'payer_email' => $user->email,
                'payer_document' => $user->cpf,
                'metadata' => [
                    'event_id' => $registration->event_id,
                    'event_title' => $registration->event->title,
                    'event_slug' => $registration->event->slug ?? null,
                    'registration_id' => $registration->id,
                    'registration_uuid' => $registration->uuid ?? null,
                ],
            ]);

            $registration->update(['payment_reference' => $payment->transaction_id]);
        }

        // Processar
        if ($brickData) {
            $result = $this->paymentService->processPaymentBrick($payment, $brickData);
        } else {
            $result = $this->paymentService->processPayment($payment);
        }

        if (isset($result['error']) || (isset($result['status']) && $result['status'] === 'failed')) {
            return back()->with('error', $result['error'] ?? 'Erro ao processar pagamento');
        }

        if (isset($result['redirect_url'])) {
            return redirect($result['redirect_url']);
        }

        return redirect()->route('checkout.show', $payment->transaction_id)
            ->with('payment_data', $result);
    }
}
