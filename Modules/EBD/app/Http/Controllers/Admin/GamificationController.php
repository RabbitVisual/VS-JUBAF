<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EBD\App\Models\EbdGamificationLevel;
use Modules\EBD\App\Models\EbdBadge;

class GamificationController extends Controller
{
    /**
     * Display a listing of the levels.
     */
    public function indexLevels()
    {
        $levels = EbdGamificationLevel::orderBy('level_number')->get();
        return view('ebd::admin.gamification.levels.index', compact('levels'));
    }

    /**
     * Store a newly created level in storage.
     */
    public function storeLevel(Request $request)
    {
        $validated = $request->validate([
            'level_number' => 'required|integer|unique:ebd_gamification_levels,level_number',
            'name' => 'required|string|max:255',
            'xp_required' => 'required|integer|min:0',
            'icon_path' => 'required|string',
        ]);

        EbdGamificationLevel::create($validated);

        return back()->with('success', 'Nível criado com sucesso.');
    }

    /**
     * Delete a level.
     */
    public function destroyLevel($id)
    {
        $level = EbdGamificationLevel::findOrFail($id);
        $level->delete();
        return back()->with('success', 'Nível removido.');
    }

    /**
     * Display a listing of the badges.
     */
    public function indexBadges()
    {
        $badges = EbdBadge::all();
        return view('ebd::admin.gamification.badges.index', compact('badges'));
    }

    /**
     * Store a newly created badge in storage.
     */
    public function storeBadge(Request $request)
    {
        $validated = $request->validate([
            'slug' => 'required|string|unique:ebd_badges,slug',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon_path' => 'required|string',
            'is_hidden' => 'boolean',
        ]);

        EbdBadge::create($validated);

        return back()->with('success', 'Medalha criada com sucesso.');
    }

    /**
     * Delete a badge.
     */
    public function destroyBadge($id)
    {
        $badge = EbdBadge::findOrFail($id);
        $badge->delete();
        return back()->with('success', 'Medalha removida.');
    }
}
