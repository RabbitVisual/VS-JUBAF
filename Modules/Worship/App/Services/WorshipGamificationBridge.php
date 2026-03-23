<?php

namespace Modules\Worship\App\Services;

use App\Models\User;
use Modules\Worship\App\Models\AcademyLesson;

/**
 * Centralizes XP and badge awards for Worship Academy lesson completion.
 * Uses EBD GamificationService when available for consistent XP/level; otherwise updates User.xp.
 * Optionally awards "Músico em Treinamento" badge (Gamification module).
 */
class WorshipGamificationBridge
{
    public const XP_PER_LESSON = 50;

    public const BADGE_NAME_MUSICIAN = 'Músico em Treinamento';

    /**
     * Award XP (and optionally badge) for completing an Academy lesson.
     * Call only on first completion (was_first_completion).
     *
     * @return int XP awarded
     */
    public function awardLessonComplete(User $user, AcademyLesson $lesson): int
    {
        $xp = self::XP_PER_LESSON;

        if (class_exists(\Modules\EBD\App\Services\GamificationService::class)) {
            app(\Modules\EBD\App\Services\GamificationService::class)
                ->addXp($user, $xp, 'worship_lesson', "Lição: {$lesson->title}");
        } else {
            $user->increment('xp', $xp);
        }

        $this->awardMusicianBadgeIfEligible($user);

        return $xp;
    }

    /**
     * Award "Músico em Treinamento" badge if the user doesn't have it yet.
     */
    protected function awardMusicianBadgeIfEligible(User $user): void
    {
        if (! class_exists(\Modules\Gamification\App\Services\GamificationService::class)) {
            return;
        }

        $badge = \Modules\Gamification\App\Models\Badge::active()
            ->where('name', self::BADGE_NAME_MUSICIAN)
            ->first();

        if (! $badge) {
            return;
        }

        $gamification = app(\Modules\Gamification\App\Services\GamificationService::class);
        $gamification->awardBadge($user, $badge, 'Conclusão de lição na Worship Academy');
    }
}
