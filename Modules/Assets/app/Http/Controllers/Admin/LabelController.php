<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Services\AssetLabelService;

class LabelController extends Controller
{
    protected $labelService;

    public function __construct(AssetLabelService $labelService)
    {
        $this->labelService = $labelService;
    }

    public function select()
    {
        // Select assets to print labels for
        $assets = Asset::all();

        return view('assets::admin.labels.select', compact('assets'));
    }

    public function print(Request $request)
    {
        $request->validate([
            'assets' => 'required|array',
            'assets.*' => 'exists:assets,id',
        ]);

        $assets = $this->labelService->getAssetsForLabels($request->assets);

        // Return a view optimized for printing (e.g. A4 sheet, Pimaco template)
        return view('assets::admin.labels.print', compact('assets'));
    }
}
