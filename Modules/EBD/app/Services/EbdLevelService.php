<?php

namespace Modules\EBD\App\Services;

use Modules\EBD\App\Models\EbdGamificationLevel;

/**
 * Single source of truth for EBD level calculation.
 * Uses ebd_gamification_levels (500 levels, cumulative XP). When table is empty, uses formula fallback.
 */
class EbdLevelService
{
    /** XP per level step when using formula fallback (level 1 = 0, 2 = 100, 3 = 200, ...) */
    public const XP_PER_LEVEL = 100;

    /**
     * Get current level model (or stdClass fallback) for a given total XP.
     */
    public function getCurrentLevel(int $xp): ?object
    {
        $level = EbdGamificationLevel::where('xp_required', '<=', $xp)
            ->orderBy('level_number', 'desc')
            ->first();

        if ($level) {
            return $level;
        }

        return $this->fallbackLevelForXp($xp, true);
    }

    /**
     * Get next level model (or stdClass fallback) for a given total XP.
     */
    public function getNextLevel(int $xp): ?object
    {
        $level = EbdGamificationLevel::where('xp_required', '>', $xp)
            ->orderBy('level_number', 'asc')
            ->first();

        if ($level) {
            return $level;
        }

        return $this->fallbackLevelForXp($xp, false);
    }

    /**
     * Get all data needed for progress bar and labels.
     * Returns: currentLevel, nextLevel, xpInCurrentLevel, xpToNextLevel, nextLevelXpTotal.
     */
    public function getLevelInfo(int $xp): array
    {
        $current = $this->getCurrentLevel($xp);
        $next = $this->getNextLevel($xp);

        $currentXpMin = $current ? (int) $current->xp_required : 0;
        $nextXpMin = $next ? (int) $next->xp_required : ($currentXpMin + self::XP_PER_LEVEL);

        $xpInCurrentLevel = max(0, $xp - $currentXpMin);
        $xpToNextLevel = max(1, $nextXpMin - $currentXpMin);

        return [
            'currentLevel' => $current,
            'nextLevel' => $next,
            'xpInCurrentLevel' => $xpInCurrentLevel,
            'xpToNextLevel' => $xpToNextLevel,
            'nextLevelXpTotal' => $nextXpMin,
        ];
    }

    /**
     * Fallback when ebd_gamification_levels is empty. Formula: level N needs (N-1)*100 XP.
     */
    private function fallbackLevelForXp(int $xp, bool $current): ?object
    {
        $levelNumber = $xp < 0 ? 1 : (int) (floor($xp / self::XP_PER_LEVEL) + 1);
        $levelNumber = min(500, max(1, $levelNumber));

        $tier = $levelNumber <= 125 ? 'bronze' : ($levelNumber <= 250 ? 'silver' : ($levelNumber <= 375 ? 'gold' : 'platinum'));
        if ($current) {
            $xpRequired = ($levelNumber - 1) * self::XP_PER_LEVEL;
            return (object) [
                'id' => null,
                'level_number' => $levelNumber,
                'name' => $levelNumber === 1 ? 'Iniciante' : "Nível {$levelNumber}",
                'xp_required' => $xpRequired,
                'icon_path' => 'trophy',
                'tier' => $tier,
            ];
        }

        if ($levelNumber >= 500) {
            return null;
        }

        $nextNumber = $levelNumber + 1;
        $xpRequired = ($nextNumber - 1) * self::XP_PER_LEVEL;
        $nextTier = $nextNumber <= 125 ? 'bronze' : ($nextNumber <= 250 ? 'silver' : ($nextNumber <= 375 ? 'gold' : 'platinum'));
        return (object) [
            'id' => null,
            'level_number' => $nextNumber,
            'name' => "Nível {$nextNumber}",
            'xp_required' => $xpRequired,
            'icon_path' => 'trophy',
            'tier' => $nextTier,
        ];
    }
}
