<?php

namespace Modules\PaymentGateway\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Services\PaymentService;
use Modules\PaymentGateway\App\Factories\PaymentGatewayFactory;

class GatewayWebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle incoming webhooks.
     *
     * @param Request $request
     * @param string $driver
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, string $driverName)
    {
        try {
            // 1. Resolve Driver Instance (Verify it exists and is active)
            $driver = PaymentGatewayFactory::make($driverName);

            // 2. Verify Signature
            if (! $driver->verifyWebhookSignature($request)) {
                return response()->json(['message' => 'Invalid signature'], 401);
            }

            // 3. Process Payload
            if ($driverName === 'stripe') {
                return $this->handleStripe($request);
            }

            if ($driverName === 'mercado_pago') {
                return $this->handleMercadoPago($request);
            }

            return response()->json(['message' => 'Driver webhook not supported'], 400);

        } catch (\Exception $e) {
            \Log::error('Gateway webhook error', [
                'driver' => $driverName,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $message = config('app.debug') ? $e->getMessage() : 'Webhook processing failed.';
            return response()->json(['error' => $message], 500);
        }
    }

    protected function handleStripe(Request $request)
    {
        $payload = $request->all();
        $type = $payload['type'] ?? null;

        if ($type === 'checkout.session.completed' || $type === 'payment_intent.succeeded') {
            $object = $payload['data']['object'];

            // 1. Find our internal Transaction ID from metadata (Preferred)
            $internalId = $object['metadata']['transaction_id'] ?? null;
            $txnId = $object['id']; // Stripe Session ID or PaymentIntent ID

            $payment = null;
            if ($internalId) {
                $payment = Payment::where('transaction_id', $internalId)->first();
            }

            // 2. Fallback to gateway_transaction_id if metadata is missing
            if (!$payment) {
                $payment = Payment::where('gateway_transaction_id', $txnId)->first();
            }

            if ($payment) {
                \Log::info("Webhook Stripe: Confirming payment {$payment->transaction_id}");
                $this->paymentService->confirmPayment($payment, 'webhook');
            } else {
                \Log::warning("Webhook Stripe: Payment not found for txn {$txnId} / internal {$internalId}");
            }
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleMercadoPago(Request $request)
    {
        // MP envia GET: ?topic=payment&id=123  ou POST: type=payment, data.id=123
        $topic = $request->input('topic');
        $type = $request->input('type');
        $id = $request->input('id') ?? $request->input('data.id');

        $isPayment = ($topic === 'payment' || $type === 'payment') && $id;

        if ($isPayment) {
            $payment = Payment::where('gateway_transaction_id', $id)->first();

            if (! $payment) {
                \Log::info('Mercado Pago webhook: payment not found', ['mp_id' => $id]);
            }

            if ($payment) {
                $this->paymentService->checkPaymentStatus($payment, 'webhook');
            }
        }

        return response()->json(['status' => 'success']);
    }
}
