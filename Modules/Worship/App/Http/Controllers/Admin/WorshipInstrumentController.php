<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Worship\App\Models\WorshipInstrument;

class WorshipInstrumentController extends Controller
{
    public function index()
    {
        $instruments = WorshipInstrument::withCount('rosters')->paginate(20);

        return view('worship::admin.instruments.index', compact('instruments'));
    }

    public function create()
    {
        $categories = \Modules\Worship\App\Models\WorshipInstrumentCategory::all();
        return view('worship::admin.instruments.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'category_id' => 'required|exists:worship_instrument_categories,id',
        ]);

        WorshipInstrument::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon ?? 'music-note',
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('worship.admin.instruments.index')->with('success', 'Instrumento criado com sucesso!');
    }

    public function edit(WorshipInstrument $instrument)
    {
        $categories = \Modules\Worship\App\Models\WorshipInstrumentCategory::all();
        return view('worship::admin.instruments.edit', compact('instrument', 'categories'));
    }

    public function update(Request $request, WorshipInstrument $instrument)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'category_id' => 'required|exists:worship_instrument_categories,id',
        ]);

        $instrument->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'icon' => $request->icon,
            'category_id' => $request->category_id,
        ]);


        return redirect()->route('worship.admin.instruments.index')->with('success', 'Instrumento atualizado com sucesso!');
    }

    public function destroy(WorshipInstrument $instrument)
    {
        $instrument->delete();

        return redirect()->route('worship.admin.instruments.index')->with('success', 'Instrumento removido com sucesso!');
    }
}
