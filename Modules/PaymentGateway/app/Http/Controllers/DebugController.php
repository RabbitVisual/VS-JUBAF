<?php

namespace Modules\PaymentGateway\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentGateway;
use Modules\PaymentGateway\App\Events\PaymentReceived;

class DebugController extends Controller
{
    public function simulatePayment(string $driver)
    {
        if (! config('app.debug')) {
            abort(404);
        }

        $gateway = PaymentGateway::firstOrCreate(
            ['name' => $driver],
            ['display_name' => ucfirst(str_replace('_', ' ', $driver)), 'is_active' => true, 'is_test_mode' => true, 'credentials' => []]
        );

        $payment = Payment::create([
            'user_id' => 1,
            'payment_gateway_id' => $gateway->id,
            'payment_type' => 'donation',
            'transaction_id' => 'SIM-' . time(),
            'amount' => 50.00,
            'currency' => 'BRL',
            'status' => 'pending',
            'payment_method' => 'credit_card',
            'description' => 'Simulated Payment Debug',
        ]);

        // Confirm
        $payment->update([
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        PaymentReceived::dispatch($payment);

        return response()->json([
            'message' => 'Payment simulated and event dispatched.',
            'payment' => $payment,
            'event' => 'PaymentReceived'
        ]);
    }
}
