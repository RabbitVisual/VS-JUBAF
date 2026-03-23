<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Ministries\App\Models\Ministry;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\PaymentGateway\App\Services\DonationPaymentService;
use Modules\Treasury\App\Models\Campaign;

class DonationController extends Controller
{
    public function __construct(
        protected DonationPaymentService $donationPaymentService
    ) {}

    /**
     * Mostra formulário de doação pública
     */
    public function create(Request $request)
    {
        // Buscar apenas gateways ativos E configurados
        $gateways = PaymentGateway::active()
            ->ordered()
            ->get()
            ->filter(function ($gateway) {
                return $gateway->isConfigured();
            });

        // Se não houver gateways configurados, redireciona com mensagem
        if ($gateways->isEmpty()) {
            return redirect()->route('homepage.index')
                ->with('warning', 'Sistema de doações temporariamente indisponível. Por favor, tente novamente mais tarde.');
        }

        $ministries = Ministry::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Buscar campanha se especificada
        $campaign = null;
        if ($request->has('campaign')) {
            $campaign = Campaign::active()->find($request->campaign);
        }

        // Buscar todas as campanhas ativas para seleção
        $campaigns = Campaign::active()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('paymentgateway::public.donations.create', compact('gateways', 'ministries', 'campaign', 'campaigns'));
    }

    /**
     * Processa doação pública
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_gateway_id' => 'required|exists:payment_gateways,id',
            'donation_type' => 'required|in:general,ministry,campaign',
            'ministry_id' => 'required_if:donation_type,ministry|nullable|exists:ministries,id',
            'campaign_id' => 'required_if:donation_type,campaign|nullable|exists:campaigns,id',
            'description' => 'nullable|string|max:500',
            'payer_name' => 'nullable|string|max:255',
            'payer_email' => 'nullable|email|max:255',
            'payer_document' => 'nullable|string|max:20',
            'payment_method' => 'nullable|string',
        ]);

        $paymentType = match ($validated['donation_type']) {
            'ministry' => 'ministry_donation',
            'campaign' => 'campaign',
            default => 'donation',
        };
        $validated['payment_type'] = $paymentType;

        $brickData = $request->has('brick_payload') ? json_decode($request->input('brick_payload'), true) : null;
        $out = $this->donationPaymentService->createAndProcessDonation($validated, auth()->user(), $brickData);
        $payment = $out['payment'];
        $result = $out['result'];

        if (isset($result['error']) || (isset($result['status']) && $result['status'] === 'failed')) {
            return back()->withErrors(['error' => $result['error'] ?? 'Erro ao processar pagamento'])->withInput();
        }

        if (isset($result['redirect_url'])) {
            return redirect($result['redirect_url']);
        }

        if (auth()->check()) {
            return redirect()->route('memberpanel.donations.show', $payment->transaction_id)
                ->with('payment_data', $result);
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
            ->firstOrFail();

        // Se for requisição AJAX, retorna JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
            ]);
        }

        return view('paymentgateway::public.donations.show', compact('payment'));
    }
}
