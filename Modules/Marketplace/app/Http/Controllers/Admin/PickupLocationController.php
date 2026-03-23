<?php

namespace Modules\Marketplace\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Marketplace\Models\PickupLocation;

class PickupLocationController extends Controller
{
    public function index()
    {
        $pickupLocations = PickupLocation::orderBy('name')->paginate(15);

        return view('marketplace::admin.pickup-locations.index', compact('pickupLocations'));
    }

    public function create()
    {
        return view('marketplace::admin.pickup-locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'instructions' => 'nullable|string',
            'availability' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['availability'] = $validated['availability'] ?? [];

        PickupLocation::create($validated);

        return redirect()->route('admin.marketplace.pickup-locations.index')->with('success', 'Ponto de retirada criado.');
    }

    public function edit(PickupLocation $pickupLocation)
    {
        return view('marketplace::admin.pickup-locations.edit', compact('pickupLocation'));
    }

    public function update(Request $request, PickupLocation $pickupLocation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'instructions' => 'nullable|string',
            'availability' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['availability'] = $validated['availability'] ?? $pickupLocation->availability ?? [];

        $pickupLocation->update($validated);

        return redirect()->route('admin.marketplace.pickup-locations.index')->with('success', 'Ponto de retirada atualizado.');
    }

    public function destroy(PickupLocation $pickupLocation)
    {
        $pickupLocation->delete();

        return redirect()->route('admin.marketplace.pickup-locations.index')->with('success', 'Ponto de retirada removido.');
    }
}
