<?php

namespace Modules\SocialAction\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\SocialAction\App\Models\SocialPantryItem;

class PantryStockController extends Controller
{
    public function index()
    {
        $items = SocialPantryItem::orderBy('name')->get();

        return view('socialaction::admin.stock.index', compact('items'));
    }

    public function create()
    {
        return view('socialaction::admin.stock.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|in:unit,kg,liter',
            'min_quantity' => 'numeric|min:0',
            'current_quantity' => 'numeric|min:0',
        ]);

        SocialPantryItem::create($request->all());

        return redirect()->route('socialaction.admin.stock.index')
            ->with('success', 'Item adicionado ao estoque.');
    }

    public function edit($id)
    {
        $item = SocialPantryItem::findOrFail($id);

        return view('socialaction::admin.stock.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|in:unit,kg,liter',
            'min_quantity' => 'numeric|min:0',
            'current_quantity' => 'numeric|min:0',
        ]);

        $item = SocialPantryItem::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('socialaction.admin.stock.index')
            ->with('success', 'Item atualizado com sucesso.');
    }

    public function destroy($id)
    {
        SocialPantryItem::destroy($id);

        return redirect()->route('socialaction.admin.stock.index')
            ->with('success', 'Item removido do estoque.');
    }
}
