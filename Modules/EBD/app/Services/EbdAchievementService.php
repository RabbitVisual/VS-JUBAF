<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\EBD\App\Models\EbdAchievement;
use Modules\EBD\App\Models\EbdUserAchievement;
use Modules\EBD\App\Models\UserGameSession;
use Modules\EBD\App\Models\EbdGamificationPoint;
use Modules\EBD\App\Models\EbdStudentProgress;

/**
 * Avalia e concede conquistas (ebd_achievements) conforme critérios em DB.
 */
class EbdAchievementService
{
    public function __construct(
        protected GamificationService $gamificationService
    ) {}

    /**
     * Avalia todas as conquistas ativas para o usuário e concede as que forem cumpridas.
     */
    public function evaluateForUser(User $user): array
    {
        $awarded = [];
        $achievements = EbdAchievement::where('is_active', true)->orderBy('order')->get();
        $userAchievementIds = EbdUserAchievement::where('user_id', $user->id)->pluck('achievement_id')->all();

        foreach ($achievements as $achievement) {
            if (in_array($achievement->id, $userAchievementIds, true)) {
                continue;
            }
            if ($this->evaluateTrigger($user, $achievement)) {
                $this->award($user, $achievement);
                $awarded[] = $achievement;
            }
        }

        return $awarded;
    }

    protected function evaluateTrigger(User $user, EbdAchievement $achievement): bool
    {
        $type = $achievement->trigger_type;
        $value = $achievement->trigger_value ?? [];

        switch ($type) {
            case 'xp_milestone':
                $xp = (int) ($user->xp ?? 0);
                $required = (int) ($value['xp'] ?? 0);
                return $xp >= $required;

            case 'game_plays':
                $count = UserGameSession::where('user_id', $user->id)->count();
                $min = (int) ($value['min'] ?? 0);
                $slug = $value['game_slug'] ?? null;
                if ($slug) {
                    $count = UserGameSession::where('user_id', $user->id)
                        ->whereHas('game', fn ($q) => $q->where('slug', $slug))
                        ->count();
                }
                return $count >= $min;

            case 'game_first_win':
                $slug = $value['game_slug'] ?? null;
                if (!$slug) {
                    return false;
                }
                return UserGameSession::where('user_id', $user->id)
                    ->whereHas('game', fn ($q) => $q->where('slug', $slug))
                    ->exists();

            case 'lesson_complete':
                $count = EbdStudentProgress::where('user_id', $user->id)->where('status', 'completed')->count();
                $min = (int) ($value['min'] ?? 1);
                return $count >= $min;

            case 'streak_days':
                $days = (int) ($value['days'] ?? 7);
                $playedDates = UserGameSession::where('user_id', $user->id)
                    ->where('completed_at', '>=', now()->subDays(60))
                    ->selectRaw('DATE(completed_at) as d')
                    ->groupBy('d')
                    ->orderBy('d', 'desc')
                    ->pluck('d')
                    ->all();
                $streak = 0;
                $check = now()->toDateString();
                foreach ($playedDates as $d) {
                    if ($d === $check) {
                        $streak++;
                        $check = date('Y-m-d', strtotime($check . ' -1 day'));
                    } else {
                        break;
                    }
                }
                return $streak >= $days;

            default:
                return false;
        }
    }

    protected function award(User $user, EbdAchievement $achievement): void
    {
        DB::transaction(function () use ($user, $achievement) {
            EbdUserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'awarded_at' => now(),
            ]);
            if ($achievement->xp_bonus > 0) {
                $this->gamificationService->addXp($user, $achievement->xp_bonus, 'bonus', "Conquista: {$achievement->name}");
            }
        });
    }
}
