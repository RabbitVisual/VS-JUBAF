<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetLocation;
use Modules\Assets\App\Models\AssetMovement;
use Modules\Assets\App\Services\AssetTrackingService;

class MovementController extends Controller
{
    protected $trackingService;

    public function __construct(AssetTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function history(Request $request)
    {
        $query = AssetMovement::with(['asset', 'user', 'responsible', 'previousLocation', 'newLocation']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('asset', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $movements = $query->latest('date')->paginate(15)->withQueryString();

        return view('assets::admin.movements.history', compact('movements'));
    }

    public function create()
    {
        // Simple form to move an asset
        $assets = Asset::where('status', '!=', 'disposed')->get();
        $locations = AssetLocation::all();
        $users = User::all(); // Assuming small user base for now, else use autocomplete

        return view('assets::admin.movements.create', compact('assets', 'locations', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'new_location_id' => 'required|exists:asset_locations,id',
            'type' => 'required|in:transfer,loan,return,maintenance_out,maintenance_return,disposal',
            'responsible_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $asset = Asset::findOrFail($request->asset_id);

        $this->trackingService->moveAsset(
            $asset,
            $request->new_location_id,
            $request->type,
            $request->notes,
            $request->responsible_id
        );

        return redirect()->route('assets.admin.movements.history')->with('success', 'Movement recorded successfully.');
    }
}
