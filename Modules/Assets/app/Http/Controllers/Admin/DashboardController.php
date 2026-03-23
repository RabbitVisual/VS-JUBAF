<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetMovement;

class DashboardController extends Controller
{
    public function index()
    {
        $totalAssets = Asset::count();
        $totalValue = Asset::sum('purchase_value');
        $assetsInMaintenance = Asset::where('status', 'maintenance')->count();
        $recentMovements = AssetMovement::with(['asset', 'user'])->latest()->take(5)->get();

        return view('assets::admin.dashboard', compact(
            'totalAssets',
            'totalValue',
            'assetsInMaintenance',
            'recentMovements'
        ));
    }
}
