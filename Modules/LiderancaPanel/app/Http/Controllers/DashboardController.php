<?php

namespace Modules\liderancapanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Nwidart\Modules\Facades\Module;

class DashboardController extends Controller
{
    /**
     * Display the pastoral dashboard (Gabinete do Pastor).
     */
    public function index()
    {
        $stats = $this->gatherStats();
        $aniversariantes = self::aniversariantesDaSemana();
        $pedidosOracaoPendentes = $this->gatherPendingPrayerRequests();
        $eliasInsight = null;

        return view('liderancapanel::dashboard', compact('stats', 'aniversariantes', 'pedidosOracaoPendentes', 'eliasInsight'));
    }

    protected function gatherPendingPrayerRequests(): \Illuminate\Support\Collection
    {
        if (! \Nwidart\Modules\Facades\Module::isEnabled('Intercessor') || ! class_exists(\Modules\Intercessor\App\Models\PrayerRequest::class)) {
            return collect();
        }

        return \Modules\Intercessor\App\Models\PrayerRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc')
            ->limit(8)
            ->get();
    }

    protected function gatherStats(): array
    {
        $stats = [
            'total_ovelhas' => User::where('is_active', true)->count(),
            'pedidos_oracao' => 0,
            'proximos_sermoes' => 0,
        ];

        if (Module::isEnabled('Intercessor') && class_exists(\Modules\Intercessor\App\Models\PrayerRequest::class)) {
            $stats['pedidos_oracao'] = \Modules\Intercessor\App\Models\PrayerRequest::where('status', 'pending')->count();
        }

        if (Module::isEnabled('Sermons') && class_exists(\Modules\Sermons\App\Models\Sermon::class)) {
            $stats['proximos_sermoes'] = \Modules\Sermons\App\Models\Sermon::query()
                ->whereNotNull('sermon_date')
                ->where('sermon_date', '>=', Carbon::now()->startOfDay())
                ->limit(5)
                ->count();
        }

        return $stats;
    }

    public static function aniversariantesDaSemana(): \Illuminate\Support\Collection
    {
        $start = Carbon::now()->startOfWeek();
        $end = Carbon::now()->endOfWeek();

        return User::where('is_active', true)
            ->whereNotNull('date_of_birth')
            ->whereRaw('MONTH(date_of_birth) = ?', [$start->month])
            ->whereRaw('DAY(date_of_birth) >= ?', [$start->day])
            ->whereRaw('DAY(date_of_birth) <= ?', [$end->day])
            ->orderByRaw('DAY(date_of_birth)')
            ->limit(15)
            ->get(['id', 'name', 'first_name', 'last_name', 'date_of_birth', 'email']);
    }
}
