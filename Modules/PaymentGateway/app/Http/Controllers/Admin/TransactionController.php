<?php

namespace Modules\PaymentGateway\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\PaymentGateway\App\Services\PaymentService;

class TransactionController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}
    public function index(Request $request)
    {
        $query = Payment::with(['gateway', 'user'])->latest();

        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                    ->orWhere('payer_name', 'like', "%{$search}%")
                    ->orWhere('payer_email', 'like', "%{$search}%");
            });
        }

        $transactions = $query->paginate(15);

        return view('paymentgateway::admin.transactions.index', compact('transactions'));
    }

    /**
     * Detalhe da transação e histórico de auditoria.
     */
    public function show(Payment $payment)
    {
        $payment->load(['gateway', 'user', 'payable', 'auditLogs']);

        return view('paymentgateway::admin.transactions.show', compact('payment'));
    }

    /**
     * Comprovante para impressão (layout limpo, auditável).
     */
    public function receipt(Payment $payment)
    {
        $payment->load(['gateway', 'user', 'payable', 'auditLogs']);

        return view('paymentgateway::admin.transactions.receipt', compact('payment'));
    }

    public function cancel(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Apenas pagamentos pendentes podem ser cancelados.');
        }

        $this->paymentService->updatePaymentStatus($payment, 'cancelled', [], 'admin');

        return back()->with('success', 'Pagamento cancelado com sucesso.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete(); // Soft delete if model uses it, otherwise hard delete

        return back()->with('success', 'Registro de pagamento excluído.');
    }
}
