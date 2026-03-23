<?php

namespace Modules\PaymentGateway\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\PaymentGateway\App\Services\DonationPaymentService;
use Modules\PaymentGateway\App\Services\PaymentService;

class DonationController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected DonationPaymentService $donationPaymentService
    ) {}

    /**
     * Lista histórico de doações
     */
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->whereIn('payment_type', ['donation', 'offering', 'ministry_donation', 'campaign', 'tithe'])
            ->with(['gateway'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('paymentgateway::memberpanel.donations.index', compact('payments'));
    }

    /**
     * Mostra formulário de doação
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'donation'); // donation, offering, ministry_donation, campaign
        $ministryId = $request->get('ministry_id');

        // Buscar apenas gateways ativos E configurados
        $gateways = PaymentGateway::active()
            ->ordered()
            ->get()
            ->filter(function ($gateway) {
                return $gateway->isConfigured();
            });

        // Se não houver gateways configurados, redireciona com mensagem
        if ($gateways->isEmpty()) {
            return redirect()->route('memberpanel.dashboard')
                ->with('warning', 'Sistema de doações temporariamente indisponível. Por favor, tente novamente mais tarde.');
        }

        // Buscar ministérios ativos
        $ministries = \Modules\Ministries\App\Models\Ministry::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Buscar campanha se especificada
        $campaign = null;
        if ($request->has('campaign')) {
            $campaign = \Modules\Treasury\App\Models\Campaign::active()->find($request->campaign);
        }

        // Buscar todas as campanhas ativas para seleção
        $campaigns = \Modules\Treasury\App\Models\Campaign::active()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('paymentgateway::memberpanel.donations.create', compact('gateways', 'type', 'ministryId', 'ministries', 'campaign', 'campaigns'));
    }

    /**
     * Processa doação
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'payment_method' => 'nullable|string',
            'payment_type' => 'required|in:donation,offering,ministry_donation,campaign',
            'ministry_id' => 'nullable|exists:ministries,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'description' => 'nullable|string|max:500',
            'payer_name' => 'nullable|string|max:255',
            'payer_email' => 'nullable|email|max:255',
            'payer_document' => 'nullable|string|max:20',
        ]);

        $brickData = $request->has('brick_payload') ? json_decode($request->input('brick_payload'), true) : null;

        $out = $this->donationPaymentService->createAndProcessDonation($validated, Auth::user(), $brickData);
        $payment = $out['payment'];
        $result = $out['result'];

        if (isset($result['error']) || (isset($result['status']) && $result['status'] === 'failed')) {
            return back()->withErrors(['error' => $result['error'] ?? 'Erro ao processar pagamento'])->withInput();
        }

        if (isset($result['redirect_url'])) {
            return redirect($result['redirect_url']);
        }

        return redirect()->route('checkout.show', $payment->transaction_id)
            ->with('payment_data', $result);
    }

    /**
     * Mostra status do pagamento
     */
    public function show(string $transactionId)
    {
        $payment = Payment::with(['gateway', 'payable'])
            ->where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('paymentgateway::memberpanel.donations.show', compact('payment'));
    }

    /**
     * Verifica status do pagamento (AJAX)
     */
    public function checkStatus(string $transactionId)
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Usa o service central para verificar status
        $result = $this->paymentService->checkPaymentStatus($payment);

        return response()->json($result);
    }

    /**
     * Mostra formulário para trocar o gateway de uma doação pendente
     */
    public function retry(string $transactionId)
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'failed'])
            ->firstOrFail();

        $gateways = PaymentGateway::active()
            ->ordered()
            ->get()
            ->filter(fn($g) => $g->isConfigured());

        return view('paymentgateway::memberpanel.donations.retry', compact('payment', 'gateways'));
    }

    /**
     * Atualiza o gateway e método de uma doação pendente
     */
    public function updateGateway(Request $request, string $transactionId)
    {
        $payment = Payment::where('transaction_id', $transactionId)
            ->where('user_id', Auth::id())
            ->whereIn('status', ['pending', 'failed'])
            ->firstOrFail();

        $validated = $request->validate([
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'payment_method' => 'nullable|string',
            'brick_payload' => 'nullable|string',
        ]);

        $gateway = PaymentGateway::active()->find($validated['payment_gateway_id']);
        if (!$gateway) {
            return back()->with('error', 'Gateway inválido ou inativo.');
        }

        $brickData = null;
        if ($request->has('brick_payload') && !empty($request->brick_payload)) {
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

        // Atualizar o pagamento existente
        $payment->update([
            'payment_gateway_id' => $gateway->id,
            'payment_method' => $paymentMethod ?? 'unknown',
        ]);

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
