<?php

namespace Modules\LiderancaPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;

class DashboardController extends Controller
{
    /**
     * Display the liderancaal dashboard (Gabinete do lideranca).
     */
    public function index()
    {
        $stats = $this->gatherStats();
        $aniversariantes = self::aniversariantesDaSemana();
        $pedidosOracaoPendentes = collect();
        $eliasInsight = null;

        return view('liderancapanel::dashboard', compact('stats', 'aniversariantes', 'pedidosOracaoPendentes', 'eliasInsight'));
    }

    protected function gatherStats(): array
    {
        $stats = [
            'total_ovelhas' => DB::table('users')->where('is_active', true)->count(),
            'pedidos_oracao' => 0,
            'proximos_sermoes' => 0,
        ];

        if (Module::isEnabled('Sermons') && class_exists(\Modules\Sermons\App\Models\Sermon::class)) {
            $stats['proximos_sermoes'] = \Modules\Sermons\App\Models\Sermon::query()
                ->whereNotNull('sermon_date')
                ->where('sermon_date', '>=', Carbon::now()->startOfDay())
                ->limit(5)
                ->count();
        }

        return $stats;
    }

    public static function aniversariantesDaSemana(): Collection
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        return DB::table('users')
            ->select(['id', 'name', 'sobrenome', 'data_nascimento', 'email'])
            ->where('is_active', true)
            ->whereNotNull('data_nascimento')
            ->whereRaw('MONTH(data_nascimento) = ?', [$start->month])
            ->whereRaw('DAY(data_nascimento) >= ?', [$start->day])
            ->whereRaw('DAY(data_nascimento) <= ?', [$end->day])
            ->orderByRaw('DAY(data_nascimento)')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                $item->data_nascimento = $item->data_nascimento ? Carbon::parse($item->data_nascimento) : null;

                return $item;
            });
    }
}
