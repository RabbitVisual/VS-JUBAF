<?php

namespace Modules\Projection\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Worship\App\Models\WorshipSetlist;

class ProjectionController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $todaySetlist = WorshipSetlist::whereBetween('scheduled_at', [$today, $todayEnd])
            ->orderBy('scheduled_at', 'asc')
            ->first();
        if (! $todaySetlist) {
            $todaySetlist = WorshipSetlist::where('scheduled_at', '>=', now())
                ->orWhere('status', '!=', \Modules\Worship\App\Enums\SetlistStatus::FINISHED)
                ->orderBy('scheduled_at', 'asc')
                ->first();
        }
        $setlists = WorshipSetlist::where('scheduled_at', '>=', now()->subDays(15))
            ->orWhere('status', '!=', \Modules\Worship\App\Enums\SetlistStatus::FINISHED)
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        return view('projection::admin.index', compact('setlists', 'todaySetlist'));
    }

    /**
     * Admin Operator Console
     */
    public function console($setlistId = null)
    {
        $setlist = null;
        if ($setlistId) {
            $setlist = WorshipSetlist::with(['items.song'])->find($setlistId);
        }

        return view('projection::admin.console.index', compact('setlist'));
    }

    /**
     * Admin Preview Screen
     */
    public function screen()
    {
        return view('projection::admin.screen.index');
    }

    /**
     * MemberPanel Operator Console
     */
    public function memberConsole($setlistId = null)
    {
        $setlist = null;
        if ($setlistId) {
            $setlist = WorshipSetlist::with(['items.song'])->find($setlistId);
        }

        return view('projection::memberpanel.console.index', compact('setlist'));
    }

    /**
     * MemberPanel Public Screen (Viewer) — requer login (painel).
     */
    public function memberScreen()
    {
        return view('projection::memberpanel.screen.index');
    }

    /**
     * Tela de projeção pública (sem login). Só exibe a tela se viewer_token for enviado.
     * Use esta URL no navegador do projetor: /projecao/tela?viewer_token=SEU_TOKEN
     */
    public function publicScreen(Request $request)
    {
        if (empty($request->query('viewer_token'))) {
            abort(403, 'Token do viewer é obrigatório. Configure em Admin > Projeção > Configurações e use a URL com ?viewer_token=SEU_TOKEN');
        }

        return view('projection::memberpanel.screen.index');
    }

    /**
     * MemberPanel Dashboard
     */
    public function memberIndex()
    {
        $today = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $todaySetlist = WorshipSetlist::whereBetween('scheduled_at', [$today, $todayEnd])
            ->orderBy('scheduled_at', 'asc')
            ->first();
        if (! $todaySetlist) {
            $todaySetlist = WorshipSetlist::where('scheduled_at', '>=', now())
                ->orWhere('status', '!=', \Modules\Worship\App\Enums\SetlistStatus::FINISHED)
                ->orderBy('scheduled_at', 'asc')
                ->first();
        }
        $setlists = WorshipSetlist::where('scheduled_at', '>=', now()->subDays(7))
            ->orWhere('status', '!=', \Modules\Worship\App\Enums\SetlistStatus::FINISHED)
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        return view('projection::memberpanel.index', compact('setlists', 'todaySetlist'));
    }

    /**
     * Admin Mobile Remote
     */
    public function remote()
    {
        return view('projection::admin.remote');
    }

    /**
     * MemberPanel Mobile Remote
     */
    public function memberRemote()
    {
        return view('projection::memberpanel.remote');
    }
}
