<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\AssetCategory;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = AssetCategory::latest()->paginate(10);

        return view('assets::admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('assets::admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AssetCategory::create($request->all());

        return redirect()->route('assets.admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = AssetCategory::findOrFail($id);

        return view('assets::admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category = AssetCategory::findOrFail($id);
        $category->update($request->all());

        return redirect()->route('assets.admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = AssetCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('assets.admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
