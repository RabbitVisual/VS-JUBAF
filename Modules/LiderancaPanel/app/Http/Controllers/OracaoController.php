<?php

namespace Modules\LiderancaPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OracaoController extends Controller
{
    /**
     * Lista de solicitações pastorais pendentes.
     */
    public function index(): View
    {
        $pendingRequests = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);

        return view('liderancapanel::oracao.index', compact('pendingRequests'));
    }

    /**
     * O fluxo legado não está mais disponível no escopo atual.
     */
    public function show(int $request): View
    {
        abort(404);
    }

    /**
     * Mantido por compatibilidade de rota legada.
     */
    public function markAsPrayed(Request $req, int $request): RedirectResponse
    {
        $back = $req->input('from', $req->query('from', 'dashboard'));
        if ($back === 'list') {
            return redirect()->route('lideranca.oracao.index')->with('success', 'Fluxo legado removido do escopo JUBAF.');
        }

        return redirect()->route('lideranca.dashboard')->with('success', 'Fluxo legado removido do escopo JUBAF.');
    }
}
