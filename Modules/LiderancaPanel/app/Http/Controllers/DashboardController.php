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
        $user = auth()->user();
        $user->load(['role', 'igreja']);

        $stats = $this->gatherStats();
        $aniversariantes = self::aniversariantesDaSemana();
        $pedidosOracaoPendentes = collect();
        $eliasInsight = null;
        
        $inscricoesCount = 0;
        if (class_exists('Modules\Events\App\Models\EventRegistration')) {
            $inscricoesCount = \Modules\Events\App\Models\EventRegistration::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->count();
        }

        $avisosRecentes = collect();
        if (class_exists('Modules\Comunicacao\App\Models\Postagem')) {
            $avisosRecentes = \Modules\Comunicacao\App\Models\Postagem::orderBy('created_at', 'desc')->limit(3)->get();
        }

        $desafioBiblico = null;
        if (class_exists('Modules\Bible\App\Models\BiblePlan')) {
            $desafioBiblico = \Modules\Bible\App\Models\BiblePlan::where('is_active', true)->orderBy('created_at', 'desc')->first();
        }
        
        $sermoesRecentes = collect();
        if (class_exists('Modules\Sermons\App\Models\Sermon')) {
            $sermoesRecentes = \Modules\Sermons\App\Models\Sermon::where('is_published', true)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        return view('liderancapanel::dashboard', compact(
            'user', 'stats', 'aniversariantes', 'pedidosOracaoPendentes', 'eliasInsight',
            'inscricoesCount', 'avisosRecentes', 'desafioBiblico', 'sermoesRecentes'
        ));
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
