<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetResponsibilityTerm;
use Modules\Assets\App\Services\TermGeneratorService;

class ResponsibilityTermController extends Controller
{
    protected $termService;

    public function __construct(TermGeneratorService $termService)
    {
        $this->termService = $termService;
    }

    public function index()
    {
        $terms = AssetResponsibilityTerm::with(['asset', 'user'])->latest()->paginate(10);

        return view('assets::admin.terms.index', compact('terms'));
    }

    public function create()
    {
        $assets = Asset::all();
        $users = User::all();

        return view('assets::admin.terms.create', compact('assets', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:loan,permanent_assignment',
        ]);

        $asset = Asset::findOrFail($request->asset_id);
        $user = User::findOrFail($request->user_id);

        $this->termService->generateTerm($asset, $user, $request->type);

        return redirect()->route('assets.admin.terms.index')->with('success', 'Responsibility term generated successfully.');
    }
}
