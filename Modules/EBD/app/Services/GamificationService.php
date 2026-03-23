<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\EBD\App\Events\StudentLeveledUp;
use Modules\EBD\App\Models\EbdBadge;
use Modules\EBD\App\Models\EbdGamificationLevel;
use Modules\EBD\App\Models\EbdGamificationPoint;
use Modules\EBD\App\Models\EbdUserBadge;

class GamificationService
{
    public function addXp(User $user, int $points, string $source, string $description = ''): void
    {
        DB::transaction(function () use ($user, $points, $source, $description) {
            EbdGamificationPoint::create([
                'user_id' => $user->id,
                'points' => $points,
                'source_type' => $source,
                'description' => $description,
            ]);

            $oldLevel = (int) ($user->level ?? 1);
            $user->xp = (int) ($user->xp ?? 0) + $points;

            $levelModel = $this->getLevelForXp($user->xp);
            $newLevelNumber = $levelModel ? $levelModel->level_number : (int) (floor($user->xp / 100) + 1);

            $user->level = $newLevelNumber;

            if ($levelModel) {
                event(new StudentLeveledUp($user, $levelModel));
            }

            $user->save();

            $this->checkBadges($user, $source);
        });
    }

    public function getUserStats(User $user): array
    {
        $totalXp = (int) ($user->xp ?? 0);
        $currentLevel = $this->getLevelForXp($totalXp);
        $nextLevel = $this->getNextLevel($currentLevel);

        $progress = 0;
        if ($currentLevel && $nextLevel) {
            $xpInLevel = $totalXp - $currentLevel->xp_required;
            $xpForNext = $nextLevel->xp_required - $currentLevel->xp_required;
            $progress = $xpForNext > 0 ? round(($xpInLevel / $xpForNext) * 100, 1) : 100;
        }

        $badges = EbdUserBadge::where('user_id', $user->id)
            ->with('badge')
            ->get()
            ->pluck('badge');

        return [
            'xp' => $totalXp,
            'level' => (int) ($user->level ?? 1),
            'level_name' => $currentLevel?->name ?? 'Iniciante',
            'progress_to_next' => $progress,
            'next_level_xp' => $nextLevel?->xp_required,
            'badges' => $badges,
            'badges_count' => $badges->count(),
        ];
    }

    public function awardBadge(User $user, string $badgeSlug): bool
    {
        $badge = EbdBadge::where('slug', $badgeSlug)->first();
        if (! $badge) {
            return false;
        }

        $existing = EbdUserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->exists();

        if ($existing) {
            return false;
        }

        EbdUserBadge::create([
            'user_id' => $user->id,
            'badge_id' => $badge->id,
            'awarded_at' => now(),
        ]);

        return true;
    }

    private function getLevelForXp(int $xp): ?EbdGamificationLevel
    {
        return EbdGamificationLevel::where('xp_required', '<=', $xp)
            ->orderBy('level_number', 'desc')
            ->first();
    }

    private function getNextLevel(?EbdGamificationLevel $current): ?EbdGamificationLevel
    {
        if (! $current) {
            return EbdGamificationLevel::orderBy('level_number')->first();
        }

        return EbdGamificationLevel::where('level_number', '>', $current->level_number)
            ->orderBy('level_number')
            ->first();
    }

    private function checkBadges(User $user, string $source): void
    {
        $totalXp = (int) ($user->xp ?? 0);

        if ($totalXp >= 100) {
            $this->awardBadge($user, 'first-100-xp');
        }
        if ($totalXp >= 1000) {
            $this->awardBadge($user, 'scholar-1000');
        }

        if ($source === 'presence') {
            $presenceCount = EbdGamificationPoint::where('user_id', $user->id)
                ->where('source_type', 'presence')
                ->count();
            if ($presenceCount >= 10) {
                $this->awardBadge($user, 'faithful-10');
            }
        }

        if ($source === 'quiz') {
            $quizCount = EbdGamificationPoint::where('user_id', $user->id)
                ->where('source_type', 'quiz')
                ->count();
            if ($quizCount >= 5) {
                $this->awardBadge($user, 'quiz-master-5');
            }
        }
    }
}
