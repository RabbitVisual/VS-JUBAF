<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SocialAction\App\Models\SocialKit;
use Modules\SocialAction\App\Models\SocialPantryItem;

class KitManagerController extends Controller
{
    public function index()
    {
        $kits = SocialKit::withCount('items')->orderBy('name')->get();
        return view('socialaction::admin.kits.index', compact('kits'));
    }

    public function create()
    {
        $items = SocialPantryItem::orderBy('name')->get();
        return view('socialaction::admin.kits.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.id' => 'exists:social_pantry_items,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        $kit = SocialKit::create($request->only('name', 'description'));

        foreach ($request->items as $itemData) {
            if ($itemData['quantity'] > 0) {
                // Assuming a pivot table or related logic.
                // Since I haven't seen the migration for pivot, I assume standard M:N with quantity
                $kit->items()->attach($itemData['id'], ['quantity' => $itemData['quantity']]);
            }
        }

        return redirect()->route('socialaction.admin.kits.index')
            ->with('success', 'Kit "Smart Pantry" criado com sucesso.');
    }

    public function edit($id)
    {
        $kit = SocialKit::with('items')->findOrFail($id);
        $items = SocialPantryItem::orderBy('name')->get();
        return view('socialaction::admin.kits.edit', compact('kit', 'items'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
        ]);

        $kit = SocialKit::findOrFail($id);
        $kit->update($request->only('name', 'description'));

        if ($request->has('items')) {
             $syncData = [];
            foreach ($request->items as $itemData) {
                if ($itemData['quantity'] > 0) {
                    $syncData[$itemData['id']] = ['quantity' => $itemData['quantity']];
                }
            }
            $kit->items()->sync($syncData);
        }

        return redirect()->route('socialaction.admin.kits.index')
            ->with('success', 'Kit atualizado com sucesso.');
    }

    public function destroy($id)
    {
        SocialKit::destroy($id);
        return redirect()->route('socialaction.admin.kits.index')
            ->with('success', 'Kit removido.');
    }
}
