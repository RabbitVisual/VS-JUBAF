<?php

namespace Modules\Sermons\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Sermons\App\Models\SermonCategory;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = SermonCategory::ordered()->withCount('sermons')->paginate(15);
        return view('sermons::pastoralpanel.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('sermons::pastoralpanel.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sermon_categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        SermonCategory::create($validated);
        return redirect()->route('pastor.sermoes.categories.index')->with('success', 'Categoria criada com sucesso!');
    }

    public function edit(SermonCategory $category): View
    {
        return view('sermons::pastoralpanel.categories.edit', compact('category'));
    }

    public function update(Request $request, SermonCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sermon_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        $category->update($validated);
        return redirect()->route('pastor.sermoes.categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(SermonCategory $category): RedirectResponse
    {
        if ($category->sermons()->count() > 0) {
            return redirect()->route('pastor.sermoes.categories.index')
                ->with('error', 'Não é possível deletar uma categoria que possui sermões.');
        }
        $category->delete();
        return redirect()->route('pastor.sermoes.categories.index')->with('success', 'Categoria removida com sucesso!');
    }
}
