<?php

namespace Modules\Admin\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Gamification\App\Models\Badge;
use Modules\Gamification\App\Models\GamificationLevel;
use Nwidart\Modules\Facades\Module;

// Core Models (Using aliases if Model not found to avoid crash, but using direct paths where known)

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // --- 1. Modules Overview ---
        $modules = Module::all();
        $modulesData = [];

        foreach ($modules as $module) {
            $modulesData[] = [
                'name' => $module->getName(),
                'alias' => $module->get('alias', $module->getLowerName()),
                'enabled' => $module->isEnabled(),
                'priority' => $module->get('priority', 0),
                'description' => $module->get('description', ''),
            ];
        }

        // Sort by priority
        usort($modulesData, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        // --- 2. Core Statistics (Aggregated) ---
        $stats = [
            'total_users' => DB::table('users')->count(),
            'active_users' => DB::table('users')->where('is_active', true)->count(),
            'total_modules' => count($modulesData),
            'enabled_modules' => count(array_filter($modulesData, fn ($m) => $m['enabled'])),
        ];

        // --- 3. Treasury Stats (if module enabled) ---
        if (Module::has('Treasury') && Module::isEnabled('Treasury')) {
            $stats['treasury_balance'] = DB::table('financial_entries')->sum('amount');
            $stats['treasury_income_month'] = DB::table('financial_entries')
                ->where('type', 'income')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount');
            $stats['treasury_expense_month'] = DB::table('financial_entries')
                ->where('type', 'expense')
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('amount');

            // Recent entries
            $recentEntries = DB::table('financial_entries')
                ->latest()
                ->take(5)
                ->get();
        } else {
            $stats['treasury_balance'] = 0;
            $recentEntries = collect([]);
        }

        // --- 4. Events Stats (if module enabled) ---
        if (Module::has('Events') && Module::isEnabled('Events')) {
            $stats['upcoming_events'] = DB::table('events')
                ->where('start_date', '>=', Carbon::now())
                ->count();
            $stats['recent_registrations'] = DB::table('event_registrations')
                ->whereMonth('created_at', Carbon::now()->month)
                ->count();

            $upcomingEvents = DB::table('events')
                ->where('start_date', '>=', Carbon::now())
                ->orderBy('start_date', 'asc')
                ->take(5)
                ->get();
        } else {
            $stats['upcoming_events'] = 0;
            $upcomingEvents = collect([]);
        }

        // --- 4b. Optional module stats (safe checks) ---
        $stats['notifications_today'] = 0;
        $stats['sermons_count'] = 0;
        $stats['worship_songs'] = 0;
        $stats['worship_setlists'] = 0;
        $stats['assets_count'] = 0;
        $stats['prayer_requests'] = 0;
        $stats['council_agendas_pending'] = 0;
        if (Module::has('Notifications') && Module::isEnabled('Notifications') && Schema::hasTable('system_notifications')) {
            $stats['notifications_today'] = DB::table('system_notifications')
                ->whereDate('created_at', Carbon::now()->toDateString())
                ->count();
        }
        if (Module::has('Sermons') && Module::isEnabled('Sermons') && Schema::hasTable('sermons')) {
            $stats['sermons_count'] = DB::table('sermons')->count();
        }
        if (Module::has('Worship') && Module::isEnabled('Worship')) {
            if (Schema::hasTable('worship_songs')) {
                $stats['worship_songs'] = DB::table('worship_songs')->count();
            }
            if (Schema::hasTable('worship_setlists')) {
                $stats['worship_setlists'] = DB::table('worship_setlists')->count();
            }
        }
        if (Module::has('Assets') && Module::isEnabled('Assets') && Schema::hasTable('assets')) {
            $stats['assets_count'] = DB::table('assets')->count();
        }
        if (Module::has('Intercessor') && Module::isEnabled('Intercessor') && Schema::hasTable('prayer_requests')) {
            $stats['prayer_requests'] = DB::table('prayer_requests')->where('status', 'pending')->count();
        }
        if (Module::has('ChurchCouncil') && Module::isEnabled('ChurchCouncil') && Schema::hasTable('council_agendas')) {
            $stats['council_agendas_pending'] = DB::table('council_agendas')
                ->whereIn('status', ['pending', 'discussed'])
                ->count();
        }

        // --- 5. EBD Stats (if module enabled and tables exist) ---
        $stats['ebd_active_classes'] = 0;
        $stats['ebd_students'] = 0;
        if (Module::has('EBD') && Module::isEnabled('EBD')) {
            if (Schema::hasTable('ebd_classes')) {
                $stats['ebd_active_classes'] = DB::table('ebd_classes')->where('is_active', true)->count();
            }
            if (Schema::hasTable('ebd_students')) {
                $stats['ebd_students'] = DB::table('ebd_students')->where('is_active', true)->distinct('user_id')->count('user_id');
            }
        }

        // --- 6. Gamification Stats ---
        $gamificationStats = [
            'total_badges' => Badge::count(),
            'active_badges' => Badge::where('is_active', true)->count(),
            'total_levels' => GamificationLevel::count(),
            'active_levels' => GamificationLevel::where('is_active', true)->count(),
            'total_badges_awarded' => DB::table('user_badges')->count(),
            'users_with_badges' => DB::table('user_badges')->distinct('user_id')->count('user_id'),
            'most_awarded_badge' => DB::table('user_badges')
                ->select('badge_id', DB::raw('count(*) as total'))
                ->groupBy('badge_id')
                ->orderBy('total', 'desc')
                ->first(),
            'average_points' => User::where('is_active', true)->get()->map(function ($user) {
                return $user->getGamificationPoints();
            })->average(),
        ];

        if ($gamificationStats['most_awarded_badge']) {
            $badge = Badge::find($gamificationStats['most_awarded_badge']->badge_id);
            $gamificationStats['most_awarded_badge_name'] = $badge ? $badge->name : 'N/A';
        } else {
            $gamificationStats['most_awarded_badge_name'] = 'N/A';
        }

        // --- 7. Charts Data ---
        // Growth Chart (Last 6 months users)
        $growthChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = DB::table('users')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $growthChart['labels'][] = $date->format('M/Y');
            $growthChart['data'][] = $count;
        }

        // Financial Chart (Last 6 months)
        $financialChart = ['labels' => [], 'income' => [], 'expense' => []];
        if (Module::has('Treasury') && Module::isEnabled('Treasury')) {
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $financialChart['labels'][] = $date->format('M');

                $income = DB::table('financial_entries')
                    ->where('type', 'income')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount');

                $expense = DB::table('financial_entries')
                    ->where('type', 'expense')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'); // Expense stored as positive usually, math handled in chart

                $financialChart['income'][] = $income;
                $financialChart['expense'][] = $expense;
            }
        }

        return view('admin::dashboard', compact(
            'modulesData',
            'stats',
            'gamificationStats',
            'recentEntries',
            'upcomingEvents',
            'growthChart',
            'financialChart'
        ));
    }
}
