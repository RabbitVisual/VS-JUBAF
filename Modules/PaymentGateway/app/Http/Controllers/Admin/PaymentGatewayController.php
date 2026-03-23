<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Models\PaymentGateway;

class PaymentGatewayController extends Controller
{
    /**
     * List all available gateways.
     */
    public function index()
    {
        $gateways = PaymentGateway::orderBy('sort_order')->get();

        return view('paymentgateway::admin.gateways.index', compact('gateways'));
    }

    /**
     * Show edit form for a specific gateway.
     */
    public function edit(string $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        return view('paymentgateway::admin.gateways.edit', compact('gateway'));
    }

    /**
     * Update configuration for a specific gateway.
     */
    public function update(Request $request, string $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        $request->validate([
            'is_active' => 'sometimes|boolean',
            'is_test_mode' => 'sometimes|boolean',
            'credentials' => 'sometimes|array',
            'settings' => 'sometimes|array',
            'supported_methods' => 'sometimes|array',
            'certificate' => 'nullable|file|max:2048', // 2MB max
            'mode' => 'sometimes|in:sandbox,production', // Legacy
        ]);

        // Handle Certificate Upload
        if ($request->hasFile('certificate')) {
             $file = $request->file('certificate');
             $filename = $gateway->name . '_' . time() . '.' . $file->getClientOriginalExtension();
             $file->storeAs('private/certs', $filename, 'local');

             $currentCreds = $gateway->getDecryptedCredentials();
             $currentCreds['certificate_path'] = $filename;
             $gateway->setEncryptedCredentials($currentCreds);
        }

        // Update basic fields
        // Since checkbox is only present when checked, we use has() to detect form submission
        // and boolean() to get the actual state, or default to false if not present but we are in the update flow.
        $gateway->is_active = $request->has('is_active');

        // Update supported methods
        // If the field is missing from the request, it means NO methods were selected.
        $gateway->supported_methods = $request->input('supported_methods', []);

        // Mode handling: View sends 'mode' (sandbox/production), Model uses 'is_test_mode' boolean
        if ($request->has('mode')) {
             $gateway->is_test_mode = ($request->input('mode') === 'sandbox');
        } elseif ($request->has('is_test_mode')) {
             $gateway->is_test_mode = $request->boolean('is_test_mode');
        }

        // Handle Credentials Update
        if ($request->has('credentials')) {
            $newCredentials = $request->input('credentials');
            // Merge with existing decrypted credentials to avoid overwriting missing keys if partial update
            $currentCredentials = $gateway->getDecryptedCredentials();
            $mergedCredentials = array_merge($currentCredentials, $newCredentials);

            $gateway->setEncryptedCredentials($mergedCredentials);
        }

        // Handle Settings Update (Non-sensitive config)
        if ($request->has('settings')) {
            $gateway->settings = array_merge($gateway->settings ?? [], $request->input('settings'));
        }

        $gateway->save();

        return redirect()->route('admin.payment-gateways.index')
            ->with('success', "Configuração do gateway {$gateway->display_name} atualizada com sucesso.");
    }

    /**
     * Statistics Page
     */
    public function statistics()
    {
        $gatewayStats = Payment::with('gateway')
            ->select('payment_gateway_id', DB::raw('count(*) as total'), DB::raw('sum(amount) as total_amount'))
            ->where('status', 'completed')
            ->groupBy('payment_gateway_id')
            ->get();

        $totalPayments = Payment::count();
        $completedPayments = Payment::where('status', 'completed')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $totalAmount = Payment::where('status', 'completed')->sum('amount');

        $stats = [
            'total_payments' => $totalPayments,
            'completed_payments' => $completedPayments,
            'pending_payments' => $pendingPayments,
            'total_amount' => $totalAmount,
            'by_gateway' => $gatewayStats,
        ];

        return view('paymentgateway::admin.statistics', compact('stats'));
    }
}
