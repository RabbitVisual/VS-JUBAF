<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EBD\App\Models\Game;
use Modules\EBD\App\Models\UserGameSession;
use Illuminate\Support\Facades\DB;

class GameAdminController extends Controller
{
    public function index()
    {
        $games = Game::withCount('questions')->get();
        // Stats
        $totalSessions = UserGameSession::count();
        $totalPoints = UserGameSession::sum('score');

        return view('ebd::admin.games.index', compact('games', 'totalSessions', 'totalPoints'));
    }

    public function edit(Game $game)
    {
        $game->load('questions.answers');
        return view('ebd::admin.games.edit', compact('game'));
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'is_active' => 'boolean',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $game->update($validated);

        return back()->with('success', 'Jogo atualizado com sucesso.');
    }

    public function resetLeaderboard()
    {
        // Option 1: Truncate (Hard Delete) - Faster, but loses history
        // Option 2: Soft Delete - Better for analytics.
        // Task says "Zerar Ranking (para novas temporadas)".
        // I will use truncate for simplicity of "Reset", or delete all rows.

        DB::transaction(function() {
            // We could archive them, but "Zerar" usually implies cleanup.
            // Let's delete all sessions.
            UserGameSession::query()->delete();
        });

        return back()->with('success', 'Ranking zerado com sucesso! Uma nova temporada começou.');
    }
}
