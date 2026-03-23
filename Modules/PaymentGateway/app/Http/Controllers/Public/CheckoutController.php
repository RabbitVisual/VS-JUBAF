<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Services\PaymentService;

class CheckoutController extends Controller
{
    /**
     * Exibe status do pagamento, instruções (PIX) e confirma retorno do Stripe (session_id).
     * Fluxo profissional: público sem login ou membro logado.
     */
    public function show(Request $request, string $transactionId)
    {
        $payment = Payment::with(['gateway', 'payable'])
            ->where('transaction_id', $transactionId)
            ->firstOrFail();

        // Para inscrições em eventos, carregar o evento no payable
        if ($payment->payable && method_exists($payment->payable, 'event')) {
            $payment->payable->load('event');
        }

        $paymentService = app(PaymentService::class);

        // Stripe: retorno com session_id → confirmar pagamento via Session e atualizar status
        $sessionId = $request->query('session_id');
        if ($sessionId && $payment->gateway && $payment->gateway->name === 'stripe' && $payment->status !== 'completed') {
            try {
                $paymentService->confirmPaymentFromStripeSession($payment, $sessionId);
                $payment->refresh();
            } catch (\Exception $e) {
                \Log::error('Checkout Stripe session confirm: ' . $e->getMessage());
            }
        }

        // Se status ainda pendente, sincronizar com o gateway (PIX polling, MP webhook atrasado, etc.)
        if (in_array($payment->status, ['pending', 'processing'])) {
            try {
                $paymentService->checkPaymentStatus($payment);
                $payment->refresh();
            } catch (\Exception $e) {
                \Log::warning('Checkout status check: ' . $e->getMessage());
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => $payment->status,
                'transaction_id' => $payment->transaction_id,
            ]);
        }

        if (auth()->check() && $payment->user_id === auth()->id()) {
            return view('paymentgateway::memberpanel.donations.show', compact('payment'));
        }

        return view('paymentgateway::public.checkout.show', compact('payment'));
    }
}
