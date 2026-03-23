<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetCategory;
use Modules\Assets\App\Models\AssetLocation;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('location_id') && $request->location_id) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $assets = $query->latest()->paginate(10)->withQueryString();

        $categories = AssetCategory::all();
        $locations = AssetLocation::all();

        return view('assets::admin.assets.index', compact('assets', 'categories', 'locations'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        $locations = AssetLocation::all();

        return view('assets::admin.assets.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code',
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:asset_locations,id',
            'status' => 'required|string',
        ]);

        Asset::create($request->all());

        return redirect()->route('assets.admin.assets.index')->with('success', 'Asset created successfully.');
    }

    public function show($id)
    {
        $asset = Asset::with(['movements.user', 'maintenances', 'responsibilityTerms.user'])->findOrFail($id);

        return view('assets::admin.assets.show', compact('asset'));
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $categories = AssetCategory::all();
        $locations = AssetLocation::all();

        return view('assets::admin.assets.edit', compact('asset', 'categories', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:assets,code,'.$id,
            'category_id' => 'required|exists:asset_categories,id',
            'location_id' => 'required|exists:asset_locations,id',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update($request->all());

        return redirect()->route('assets.admin.assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return redirect()->route('assets.admin.assets.index')->with('success', 'Asset deleted successfully.');
    }
}
