<?php

namespace Modules\Gamification\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Modules\Gamification\App\Events\BadgeAwarded;
use Modules\Gamification\App\Models\Badge;
use Modules\Gamification\App\Models\GamificationLevel;

class GamificationService
{
    /**
     * Retorna dados unificados de progresso para o usuário (nível atual, próximo, %, meta).
     * Fonte única para perfil, dashboard e Admin.
     *
     * @return array{level: array, next_level: array|null, points: int, progress_percent: float, points_to_next: int, points_max_display: int|null}
     */
    public function getProgressForUser(User $user): array
    {
        $points = $user->getGamificationPoints();
        $levelModel = GamificationLevel::getLevelByPoints($points);

        $level = [
            'name' => 'Novato',
            'icon' => 'user',
            'color' => 'gray',
            'points_min' => 0,
            'points_max' => 99,
        ];

        if ($levelModel) {
            $level = [
                'name' => $levelModel->name,
                'icon' => $levelModel->icon ?? 'star',
                'color' => $levelModel->color ?? 'gray',
                'points_min' => (int) $levelModel->points_min,
                'points_max' => $levelModel->points_max !== null ? (int) $levelModel->points_max : null,
            ];
        }

        $nextLevelModel = $levelModel ? GamificationLevel::getNextLevelAfter($levelModel) : null;
        $nextLevel = null;
        $pointsToNext = 0;
        $pointsMaxDisplay = null;
        $progressPercent = 100.0;

        if ($nextLevelModel) {
            $nextLevel = [
                'name' => $nextLevelModel->name,
                'icon' => $nextLevelModel->icon ?? 'star',
                'color' => $nextLevelModel->color ?? 'gray',
                'points_min' => (int) $nextLevelModel->points_min,
                'points_max' => $nextLevelModel->points_max !== null ? (int) $nextLevelModel->points_max : null,
            ];
            $pointsMaxDisplay = (int) $nextLevelModel->points_min;
            $pointsToNext = max(0, $pointsMaxDisplay - $points);
            $rangeTotal = $pointsMaxDisplay - ($level['points_min'] ?? 0);
            $rangeCurrent = $points - ($level['points_min'] ?? 0);
            $progressPercent = $rangeTotal > 0 ? min(100.0, ($rangeCurrent / $rangeTotal) * 100) : 100.0;
        } else {
            $pointsMaxDisplay = $level['points_max'];
            if ($pointsMaxDisplay !== null) {
                $rangeTotal = $pointsMaxDisplay - ($level['points_min'] ?? 0);
                $rangeCurrent = $points - ($level['points_min'] ?? 0);
                $progressPercent = $rangeTotal > 0 ? min(100.0, ($rangeCurrent / $rangeTotal) * 100) : 100.0;
            }
        }

        return [
            'level' => $level,
            'next_level' => $nextLevel,
            'points' => $points,
            'progress_percent' => round($progressPercent, 1),
            'points_to_next' => $pointsToNext,
            'points_max_display' => $pointsMaxDisplay,
        ];
    }

    public function checkAndAwardBadges(User $user): void
    {
        $points = $user->getGamificationPoints();

        $autoBadges = Badge::active()
            ->where('criteria_type', '!=', 'manual')
            ->get();

        foreach ($autoBadges as $badge) {
            if ($user->badges()->where('badge_id', $badge->id)->exists()) {
                continue;
            }
            if ($this->checkCriteria($user, $badge, $points)) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    private function checkCriteria(User $user, Badge $badge, int $points): bool
    {
        switch ($badge->criteria_type) {
            case 'points':
                return $points >= $badge->points_required;
            case 'time_congregating':
                $months = $badge->criteria_value['months'] ?? 0;
                return ($user->time_congregating_months ?? 0) >= $months;
            case 'is_baptized':
                return $user->is_baptized === true;
            case 'profile_complete':
                $percentage = $badge->criteria_value['percentage'] ?? 100;
                return $this->getProfileCompletion($user) >= $percentage;
            case 'ministries_count':
            case 'ministries_joined':
                $count = $badge->criteria_value['count'] ?? 1;
                return method_exists($user, 'activeMinistries') ? $user->activeMinistries()->count() >= $count : false;
            case 'bible_favorites':
                $count = $badge->criteria_value['count'] ?? 1;
                return method_exists($user, 'bibleFavorites') ? $user->bibleFavorites()->count() >= $count : false;
            case 'contributions_made':
                $required = (int) ($badge->criteria_value['count'] ?? 1);
                if (! method_exists($user, 'financialEntries')) {
                    return false;
                }
                $count = $user->financialEntries()->income()->where(function ($q) {
                    $q->whereNull('payment_id')
                        ->orWhereHas('payment', fn ($p) => $p->where('status', 'completed'));
                })->count();
                return $count >= $required;
            case 'events_attended':
                $required = (int) ($badge->criteria_value['count'] ?? 1);
                return method_exists($user, 'registrations') ? $user->registrations()->where('status', 'confirmed')->count() >= $required : false;
            default:
                return false;
        }
    }

    private function getProfileCompletion(User $user): int
    {
        // Usa a mesma lógica centralizada da model User para evitar
        // percentuais diferentes entre dashboard, badges e bot Elias.
        return (int) $user->getProfileCompletionPercentage();
    }

    public function awardBadge(User $user, Badge $badge, ?string $notes = null): void
    {
        if ($user->badges()->where('badge_id', $badge->id)->exists()) {
            return;
        }
        $user->badges()->attach($badge->id, [
            'earned_at' => now(),
            'notes' => $notes,
        ]);
        Log::info("Badge '{$badge->name}' atribuído ao usuário {$user->id}");
        event(new BadgeAwarded($user, $badge));
    }

    public function removeBadge(User $user, Badge $badge): void
    {
        $user->badges()->detach($badge->id);
        Log::info("Badge '{$badge->name}' removido do usuário {$user->id}");
    }

    public function checkAllUsers(): void
    {
        $users = User::where('is_active', true)->get();
        foreach ($users as $user) {
            $this->checkAndAwardBadges($user);
        }
    }
}
