<?php

namespace Modules\Worship\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipInstrumentCategory;

class InstrumentCategoryController extends Controller
{
    public function index()
    {
        $categories = WorshipInstrumentCategory::withCount('instruments')->orderBy('name')->paginate(20);
        return view('worship::admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('worship::admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string',
            'icon' => 'nullable|string',
        ]);

        WorshipInstrumentCategory::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'color' => $request->color,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

        return redirect()->route('worship.admin.categories.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(WorshipInstrumentCategory $category)
    {
        return view('worship::admin.categories.edit', compact('category'));
    }

    public function update(Request $request, WorshipInstrumentCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'color' => $request->color,
            'icon' => $request->icon,
            'description' => $request->description,
        ]);

        return redirect()->route('worship.admin.categories.index')->with('success', 'Categoria atualizada!');
    }

    public function destroy(WorshipInstrumentCategory $category)
    {
        if ($category->instruments()->exists()) {
            return back()->with('error', 'Não é possível excluir categoria com instrumentos vinculados.');
        }
        $category->delete();
        return redirect()->route('worship.admin.categories.index')->with('success', 'Categoria removida!');
    }
}
