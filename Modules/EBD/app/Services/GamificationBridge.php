<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EbdXpRule;

class GamificationBridge
{
    public function __construct(
        protected GamificationService $gamificationService,
        protected EbdAchievementService $ebdAchievementService
    ) {}

    public function awardLessonComplete(User $user, EBDLesson $lesson): int
    {
        $rule = EbdXpRule::where('source_type', 'lesson')->where('is_active', true)->first();
        $xp = $rule ? $rule->calculateXp(100) : 100;

        if ($xp > 0) {
            $this->gamificationService->addXp($user, $xp, 'lesson', "Aula: {$lesson->title}");
            $this->ebdAchievementService->evaluateForUser($user->fresh());
        }

        return $xp;
    }

    public function awardQuizScore(User $user, int $score, string $context = 'quiz'): int
    {
        $rule = EbdXpRule::where('source_type', 'quiz')->where('is_active', true)->first();
        $xp = $rule ? $rule->calculateXp($score) : min(50, max(1, (int) ($score * 0.1)));

        if ($xp > 0) {
            $this->gamificationService->addXp($user, $xp, $context, "Quiz: {$context}");
            $this->ebdAchievementService->evaluateForUser($user->fresh());
        }

        return $xp;
    }
}
