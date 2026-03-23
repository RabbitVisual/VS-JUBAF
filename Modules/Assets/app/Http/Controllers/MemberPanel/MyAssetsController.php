<?php

namespace Modules\Assets\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Assets\App\Models\Asset;
use Modules\Assets\App\Models\AssetMovement;
use Modules\Assets\App\Models\AssetResponsibilityTerm;

class MyAssetsController extends Controller
{
    public function index()
    {
        // Assets where the user is the responsible person
        // This relies on 'responsible_id' or implicit responsibility via latest movement?
        // Let's use 'responsible_id' if we added it to AssetMovement, or checking the latest movement.
        // But better yet, I should have a clear way to know which asset is CURRENTLY with the user.
        // Logic: Get assets where last movement left it with this user?
        // Or maybe I added 'responsible_id' to logic in AssetTrackingService.

        // Let's query based on movements for now, or if I add a direct relation on Asset model?
        // Asset model status 'borrowed' doesn't say WHO has it.
        // Movement table has 'responsible_id'.
        // So I need to find assets where the LAST movement has 'responsible_id' = Auth::id() AND type != return.

        // Simpler approach: Query AssetMovement where responsible_id is user, distinct by asset_id, filter by latest.
        // OR better: In pure SQL:
        // Select * from assets where id in (select asset_id from asset_movements where id in (select max(id) from asset_movements group by asset_id) and responsible_id = user_id)

        // Simplified for Eloquent:
        $userId = Auth::id();
        $assets = Asset::whereHas('movements', function ($query) use ($userId) {
            $query->where('id', function ($sub) {
                $sub->selectRaw('max(id)')->from('asset_movements')->whereColumn('asset_id', 'assets.id');
            })->where('responsible_id', $userId);
        })->with('category')->get();

        // Also get terms to sign?
        $pendingTerms = AssetResponsibilityTerm::where('user_id', $userId)
            ->whereNull('signed_at')
            ->get();

        return view('assets::memberpanel.my-assets.index', compact('assets', 'pendingTerms'));
    }

    public function signTerm($id)
    {
        $term = AssetResponsibilityTerm::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Logic to sign (e.g. click button to timestamp)
        $term->update(['signed_at' => now()]);

        return redirect()->back()->with('success', 'Termo assinado digitalmente com sucesso.');
    }
}
