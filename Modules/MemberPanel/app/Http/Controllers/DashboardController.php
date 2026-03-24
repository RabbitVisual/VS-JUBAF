<?php

namespace Modules\MemberPanel\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the member dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load(['role', 'igreja']);

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

        $progressData = [
            'points' => $user->getGamificationPoints(),
            'level' => $user->getGamificationLevel(),
            'next_level' => null,
            'progress_percent' => 0,
            'points_to_next' => 0,
            'points_max_display' => null,
        ];

        $gamificationServiceClass = \Modules\Gamification\App\Services\GamificationService::class;
        if (class_exists($gamificationServiceClass)) {
            $gamificationService = app($gamificationServiceClass);
            if (method_exists($gamificationService, 'getProgressForUser')) {
                $progressData = $gamificationService->getProgressForUser($user);
            }
        }

        $dailyReading = null;
        $dailyReadingServiceClass = \Modules\Gamification\App\Services\DailyReadingService::class;
        if (class_exists($dailyReadingServiceClass)) {
            $dailyReadingService = app($dailyReadingServiceClass);
            if (method_exists($dailyReadingService, 'getDailyReadingForDate')) {
                $dailyReading = $dailyReadingService->getDailyReadingForDate(now());
            }
        }

        $stats = [
            'points' => $progressData['points'],
            'level' => $progressData['level'],
            'next_level' => $progressData['next_level'],
            'progress_percent' => $progressData['progress_percent'],
            'points_to_next' => $progressData['points_to_next'],
            'points_max_display' => $progressData['points_max_display'],
            'profile_completion' => $user->getProfileCompletionPercentage(),
            'time_congregating' => $user->time_congregating_months ?? 0,
            'is_baptized' => $user->is_baptized,
        ];

        return view('memberpanel::dashboard', compact(
            'user', 'stats', 'dailyReading', 'inscricoesCount', 'avisosRecentes', 'desafioBiblico'
        ));
    }

    // Mantido sem o método calculateProfileCompletion(): o cálculo oficial
    // de completude agora vem sempre de User::getProfileCompletionPercentage().
}
