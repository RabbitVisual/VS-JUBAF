<?php

namespace Modules\Intercessor\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Intercessor\App\Models\PrayerCategory;
use Illuminate\Http\Request;

class IntercessorCategoryController extends Controller
{
    public function index()
    {
        $categories = PrayerCategory::withCount('requests')->paginate(10);
        return view('intercessor::admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('intercessor::admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        PrayerCategory::create($request->all());

        return redirect()->route('admin.intercessor.categories.index')
            ->with('success', 'Categoria criada com sucesso.');
    }

    public function edit(PrayerCategory $category)
    {
        return view('intercessor::admin.categories.edit', compact('category'));
    }

    public function update(Request $request, PrayerCategory $category)
    {
         $request->validate([
            'name' => 'required|string|max:255',
             'color' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('admin.intercessor.categories.index')
            ->with('success', 'Categoria atualizada com sucesso.');
    }

    public function destroy(PrayerCategory $category)
    {
        $category->delete();
        return back()->with('success', 'Categoria removida com sucesso.');
    }
}
