<?php

namespace Modules\Treasury\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\PaymentGateway\App\Models\Payment;
use Modules\Treasury\App\Models\FinancialEntry;
use Modules\Treasury\App\Models\TreasuryPermission;
use Modules\Treasury\App\Services\TreasuryApiService;

class FinancialEntryController extends Controller
{
    public function __construct(
        private TreasuryApiService $api
    ) {}

    public function index(Request $request)
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $filters = $request->only(['type', 'category', 'start_date', 'end_date', 'campaign_id', 'ministry_id', 'fund_id']);
        $entries = $this->api->listEntries($filters, 20);
        $options = $this->api->getEntryFormOptions();
        $reversedEntryIds = FinancialEntry::whereIn('reversal_of_id', $entries->pluck('id'))->pluck('reversal_of_id')->flip();

        return view('treasury::admin.entries.index', [
            'entries' => $entries,
            'campaigns' => $options['campaigns'],
            'ministries' => $options['ministries'],
            'financial_funds' => $options['financial_funds'] ?? collect(),
            'permission' => $permission,
            'reversedEntryIds' => $reversedEntryIds,
        ]);
    }

    public function reverse(Request $request, FinancialEntry $entry)
    {
        try {
            $this->api->reverseEntry($entry, auth()->user());
            return redirect()->route('treasury.entries.index')
                ->with('success', 'Estorno registrado com sucesso. Foi criada uma entrada inversa vinculada à original.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 422) {
                return redirect()->route('treasury.entries.index')
                    ->with('error', $e->getMessage());
            }
            throw $e;
        }
    }

    public function create()
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $options = $this->api->getEntryFormOptions();

        return view('treasury::admin.entries.create', [
            'campaigns' => $options['campaigns'],
            'goals' => $options['goals'],
            'ministries' => $options['ministries'],
            'financial_categories' => $options['financial_categories'] ?? collect(),
            'financial_funds' => $options['financial_funds'] ?? collect(),
            'payments' => $options['payments'],
            'permission' => $permission,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:64',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'payment_id' => 'nullable|exists:payments,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'goal_id' => 'nullable|exists:financial_goals,id',
            'ministry_id' => 'nullable|exists:ministries,id',
            'fund_id' => 'nullable|exists:financial_funds,id',
            'member_id' => 'nullable|exists:users,id',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $this->api->createEntry($validated, auth()->user());

        return redirect()->route('treasury.entries.index')
            ->with('success', 'Entrada financeira criada com sucesso!');
    }

    public function edit(FinancialEntry $entry)
    {
        $permission = TreasuryPermission::forUserOrAdmin(auth()->user());
        $options = $this->api->getEntryFormOptions();

        return view('treasury::admin.entries.edit', [
            'entry' => $entry,
            'campaigns' => $options['campaigns'],
            'goals' => $options['goals'],
            'ministries' => $options['ministries'],
            'financial_categories' => $options['financial_categories'] ?? collect(),
            'financial_funds' => $options['financial_funds'] ?? collect(),
            'payments' => $options['payments'],
            'permission' => $permission,
        ]);
    }

    public function update(Request $request, FinancialEntry $entry)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:64',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0.01',
            'entry_date' => 'required|date',
            'payment_id' => 'nullable|exists:payments,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'goal_id' => 'nullable|exists:financial_goals,id',
            'ministry_id' => 'nullable|exists:ministries,id',
            'fund_id' => 'nullable|exists:financial_funds,id',
            'member_id' => 'nullable|exists:users,id',
            'expense_status' => 'nullable|in:pending,approved,paid',
            'payment_method' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
        ]);

        $this->api->updateEntry($entry, $validated, auth()->user());

        return redirect()->route('treasury.entries.index')
            ->with('success', 'Entrada financeira atualizada com sucesso!');
    }

    public function destroy(FinancialEntry $entry)
    {
        $this->api->deleteEntry($entry, auth()->user());

        return redirect()->route('treasury.entries.index')
            ->with('success', 'Entrada financeira removida com sucesso!');
    }

    public function importPayment(Payment $payment)
    {
        try {
            $this->api->importPayment($payment, auth()->user());
            return redirect()->route('treasury.entries.index')
                ->with('success', 'Pagamento importado com sucesso!');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            if ($e->getStatusCode() === 422) {
                return redirect()->route('treasury.entries.index')
                    ->with('error', 'Este pagamento já foi importado.');
            }
            throw $e;
        }
    }
}
