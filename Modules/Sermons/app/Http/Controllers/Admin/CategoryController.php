<?php

namespace Modules\Sermons\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Sermons\App\Models\SermonCategory;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(): View
    {
        $categories = SermonCategory::ordered()->withCount('sermons')->paginate(15);

        return view('sermons::admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create(): View
    {
        return view('sermons::admin.categories.create');
    }

    /**
     * Store a newly created category
     */
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

        return redirect()->route('admin.sermons.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(SermonCategory $category): View
    {
        return view('sermons::admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, SermonCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sermon_categories,name,'.$category->id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->route('admin.sermons.categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified category
     */
    public function destroy(SermonCategory $category): RedirectResponse
    {
        if ($category->sermons()->count() > 0) {
            return redirect()->route('admin.sermons.categories.index')
                ->with('error', 'Não é possível deletar uma categoria que possui sermões.');
        }

        $category->delete();

        return redirect()->route('admin.sermons.categories.index')
            ->with('success', 'Categoria removida com sucesso!');
    }
}
