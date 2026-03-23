<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\SocialAction\App\Models\SocialAssistance;
use Modules\SocialAction\App\Models\SocialBeneficiary;
use Modules\SocialAction\App\Models\SocialKit;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Modules\SocialAction\App\Models\SocialPantryItem;
use Modules\SocialAction\App\Services\SocialActionApiService;
use Modules\SocialAction\App\Services\StockManagerService;
use Modules\SocialAction\App\Services\TreasuryIntegrationService;

class AssistanceHistoryController extends Controller
{
    public function __construct(
        private SocialActionApiService $api,
        private TreasuryIntegrationService $treasury,
        private StockManagerService $stockManager
    ) {}

    public function index(Request $request): View
    {
        $assistances = $this->api->listAssistances(
            20,
            $request->query('type'),
            $request->query('beneficiary_id') ? (int) $request->query('beneficiary_id') : null
        );
        return view('socialaction::admin.assistance.history', compact('assistances'));
    }

    public function create(): View
    {
        $beneficiaries = $this->api->listBeneficiariesForSelect();
        $items = SocialPantryItem::orderBy('name')->where('current_quantity', '>', 0)->get();
        $kits = SocialKit::withCount('items')->orderBy('name')->get();
        $canCreateTreasuryEntries = $this->treasury->canCurrentUserCreateTreasuryEntries();
        return view('socialaction::admin.assistance.register', compact('beneficiaries', 'items', 'kits', 'canCreateTreasuryEntries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'social_beneficiary_id' => 'required|exists:social_beneficiaries,id',
            'type' => 'required|in:item,kit,financial',
            'social_pantry_item_id' => 'required_if:type,item|nullable|exists:social_pantry_items,id',
            'kit_id' => 'required_if:type,kit|nullable|exists:social_kits,id',
            'quantity' => 'required_if:type,item|required_if:type,kit|nullable|numeric|min:0.01',
            'registered_at' => 'required|date',
            'notes' => 'nullable|string',
        ];
        if ($request->type === 'financial') {
            $rules['amount'] = 'required|numeric|min:0.01';
            $rules['description'] = 'nullable|string|max:500';
        }
        $validated = $request->validate($rules);

        return DB::transaction(function () use ($request, $validated) {
            $financialEntryId = null;
            if ($request->type === 'financial') {
                if (! $this->treasury->canCurrentUserCreateTreasuryEntries()) {
                    throw ValidationException::withMessages(['type' => 'Para registrar auxílio financeiro, é necessário permissão para criar entradas na Tesouraria.']);
                }

                $beneficiary = SocialBeneficiary::findOrFail($request->social_beneficiary_id);
                $entry = $this->treasury->createExpenseForAssistance(
                    'Ação Social - Auxílio: ' . ($beneficiary->full_name ?? 'Beneficiário #' . $beneficiary->id),
                    (float) $request->amount,
                    $request->description ?? 'Assistência financeira registrada pelo módulo Ação Social.',
                    $request->registered_at,
                    $beneficiary->id,
                    null,
                    auth()->user()
                );
                $financialEntryId = $entry->id;
            }

            if ($request->type === 'item') {
                $item = SocialPantryItem::findOrFail($request->social_pantry_item_id);
                if ($item->current_quantity < $request->quantity) {
                    throw ValidationException::withMessages(['quantity' => 'Estoque insuficiente (' . $item->current_quantity . ' ' . $item->unit . ' disponível).']);
                }
                $this->stockManager->adjustStock($item, $request->quantity, 'out', 'Entrega de item individual', auth()->id());
            } elseif ($request->type === 'kit') {
                $kit = SocialKit::with('items')->findOrFail($request->kit_id);
                foreach ($kit->items as $kitItem) {
                    $needed = $kitItem->pivot->quantity * $request->quantity;
                    if ($kitItem->current_quantity < $needed) {
                        throw ValidationException::withMessages(['quantity' => "Estoque insuficiente para o item '{$kitItem->name}' no kit."]);
                    }
                }
                $this->stockManager->deductKit($kit, (int) $request->quantity, auth()->id());
            }

            $data = array_merge($validated, [
                'quantity' => $request->type === 'financial' ? 1 : $request->quantity,
                'social_pantry_item_id' => $request->type === 'financial' ? null : $request->social_pantry_item_id,
                'kit_id' => $request->type === 'financial' ? null : $request->kit_id,
                'amount' => $request->type === 'financial' ? $request->amount : null,
                'financial_entry_id' => $financialEntryId,
                'volunteer_id' => auth()->id(),
            ]);
            SocialAssistance::create($data);

            $msg = $request->type === 'financial'
                ? 'Auxílio financeiro registrado e despesa lançada na Tesouraria.'
                : 'Assistência registrada e estoque atualizado.';

            return redirect()->route('socialaction.admin.assistance.index')->with('success', $msg);
        });
    }

    public function show($id)
    {
        // Not used strictly, usually history is list only
        return redirect()->route('socialaction.admin.assistance.index');
    }

    public function edit($id)
    {
        // Usually assistance records are immutable logs, but let's allow basic edit if needed or just return back
        return redirect()->route('socialaction.admin.assistance.index');
    }

    public function update(Request $request, $id) {}

    public function destroy($id) {}
}
