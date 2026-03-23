<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetMaintenance;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetMaintenance::with('asset');

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($aq) use ($search) {
                        $aq->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $maintenances = $query->latest()->paginate(10)->withQueryString();

        return view('assets::admin.maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $assets = Asset::all();

        return view('assets::admin.maintenances.create', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'status' => 'required|string',
        ]);

        AssetMaintenance::create($request->all());

        return redirect()->route('assets.admin.maintenances.index')->with('success', 'Maintenance record created.');
    }

    public function show($id)
    {
        $maintenance = AssetMaintenance::with('asset')->findOrFail($id);

        return view('assets::admin.maintenances.show', compact('maintenance'));
    }
}
