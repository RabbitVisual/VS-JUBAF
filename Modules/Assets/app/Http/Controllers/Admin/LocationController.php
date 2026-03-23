<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\AssetLocation;

class LocationController extends Controller
{
    public function index()
    {
        $locations = AssetLocation::latest()->paginate(10);

        return view('assets::admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('assets::admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        AssetLocation::create($request->all());

        return redirect()->route('assets.admin.locations.index')->with('success', 'Location created successfully.');
    }

    public function edit($id)
    {
        $location = AssetLocation::findOrFail($id);

        return view('assets::admin.locations.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $location = AssetLocation::findOrFail($id);
        $location->update($request->all());

        return redirect()->route('assets.admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy($id)
    {
        $location = AssetLocation::findOrFail($id);
        $location->delete();

        return redirect()->route('assets.admin.locations.index')->with('success', 'Location deleted successfully.');
    }
}
