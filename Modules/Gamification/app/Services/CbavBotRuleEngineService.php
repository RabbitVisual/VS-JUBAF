<?php

namespace Modules\Gamification\App\Services;

use App\Models\User;
use Carbon\Carbon;
use Modules\Gamification\App\Events\MedalUnlocked;
use Modules\Gamification\Models\Achievement;
use Modules\Gamification\Models\CoachingRule;
use Modules\Gamification\Models\Insight;
use Modules\Gamification\Models\UserMedal;

/**
 * Motor de regras do CBAV Bot (contexto Gospel / igreja).
 * Retorna um insight por vez para exibir no bot.
 * Apenas conteúdo edificante/igreja; insights financeiros (Vertex Pro) são ignorados.
 */
class CbavBotRuleEngineService
{
    /** Triggers de contexto financeiro: nunca exibir no painel do membro (igreja). */
    private const FINANCIAL_TRIGGER_EVENTS = [
        'low_balance', 'budget_reached', 'savings_milestone', 'daily_tip',
        'page_goals', 'page_categories', 'page_reports', 'page_budgets',
        'page_income', 'page_transactions', 'page_tickets',
    ];

    /**
     * Mapeamento rota -> tourId para "Tour desta página" (bot presente em todas as páginas do painel).
     * Rotas exatas têm prioridade; rotas não listadas usam ROUTE_TOUR_PREFIX_FALLBACK.
     */
    public const ROUTE_TOUR_MAP = [
        'memberpanel.dashboard' => 'dashboard',
        'memberpanel.dashboard.index' => 'dashboard',
        'memberpanel.profile.show' => 'profile',
        'memberpanel.profile.edit' => 'profile-edit',
        'memberpanel.notifications.index' => 'notifications',
        'memberpanel.bible.index' => 'bible-read',
        'memberpanel.bible.read' => 'bible-read',
        'memberpanel.bible.book' => 'bible-book',
        'memberpanel.bible.chapter' => 'bible-chapter',
        'memberpanel.bible.search' => 'bible-search',
        'memberpanel.bible.favorites' => 'bible-read',
        'memberpanel.events.index' => 'events-list',
        'memberpanel.events.show' => 'events-show',
        'memberpanel.events.my-registrations' => 'events-my-registrations',
        'memberpanel.events.show-registration' => 'events-my-registrations',
        'memberpanel.events.registration.pending' => 'events-my-registrations',
        'memberpanel.events.registration.confirmed' => 'events-my-registrations',
        'memberpanel.events.registration.retry' => 'events-my-registrations',
        'memberpanel.ministries.index' => 'ministries',
        'memberpanel.ministries.show' => 'ministries',
        'memberpanel.treasury.dashboard' => 'treasury',
        'memberpanel.treasury.dashboard.index' => 'treasury',
        'memberpanel.treasury.entries.index' => 'treasury-entries',
        'memberpanel.treasury.entries.create' => 'treasury-entries',
        'memberpanel.treasury.entries.edit' => 'treasury-entries',
        'memberpanel.treasury.campaigns.index' => 'treasury-campaigns',
        'memberpanel.treasury.campaigns.show' => 'treasury-campaigns',
        'memberpanel.treasury.campaigns.create' => 'treasury-campaigns',
        'memberpanel.treasury.campaigns.edit' => 'treasury-campaigns',
        'memberpanel.treasury.goals.index' => 'treasury-goals',
        'memberpanel.treasury.goals.show' => 'treasury-goals',
        'memberpanel.treasury.goals.create' => 'treasury-goals',
        'memberpanel.treasury.goals.edit' => 'treasury-goals',
        'memberpanel.treasury.reports.index' => 'treasury-reports',
        'memberpanel.treasury.reports.export' => 'treasury-reports',
        'memberpanel.donations.index' => 'donations',
        'memberpanel.donations.create' => 'donations-create',
        'memberpanel.donations.show' => 'donations',
        'memberpanel.donations.retry' => 'donations',
        'memberpanel.sermons.index' => 'sermons',
        'memberpanel.sermons.show' => 'sermons',
        'memberpanel.sermons.my-favorites' => 'sermons-favorites',
        'memberpanel.sermons.my-sermons' => 'sermons',
        'memberpanel.sermons.create' => 'sermons',
        'memberpanel.series.index' => 'sermons-series',
        'memberpanel.series.show' => 'sermons-series',
        'memberpanel.studies.index' => 'sermons-studies',
        'memberpanel.studies.show' => 'sermons-studies',
        'memberpanel.commentaries.index' => 'sermons',
        'memberpanel.commentaries.show' => 'sermons',
        'memberpanel.cbav-bot.analysis' => 'profile',
        'memberpanel.churchcouncil.index' => 'churchcouncil',
        'memberpanel.churchcouncil.meetings.index' => 'churchcouncil',
        'memberpanel.churchcouncil.meetings.show' => 'churchcouncil',
        'memberpanel.churchcouncil.agendas.index' => 'churchcouncil',
        'memberpanel.churchcouncil.agendas.create' => 'churchcouncil',
        'memberpanel.churchcouncil.approvals.index' => 'churchcouncil',
        'memberpanel.churchcouncil.approvals.pending' => 'churchcouncil',
        'memberpanel.churchcouncil.approvals.show' => 'churchcouncil',
        'memberpanel.churchcouncil.profile.index' => 'churchcouncil',
        'memberpanel.churchcouncil.documents.index' => 'churchcouncil',
        'memberpanel.churchcouncil.projects.index' => 'churchcouncil',
        'memberpanel.churchcouncil.projects.create' => 'churchcouncil',
        'memberpanel.churchcouncil.projects.show' => 'churchcouncil',
        'memberpanel.projection.index' => 'projection',
        'memberpanel.projection.console' => 'projection',
        'memberpanel.projection.screen' => 'projection',
        'memberpanel.ebd.student.index' => 'ebd-student',
        'memberpanel.ebd.student.my-classes' => 'ebd-student',
        'memberpanel.ebd.student.lessons' => 'ebd-student',
        'memberpanel.ebd.student.lessons.show' => 'ebd-student',
        'memberpanel.ebd.student.my-progress' => 'ebd-student',
        'memberpanel.ebd.teacher.index' => 'ebd-teacher',
        'memberpanel.ebd.teacher.my-classes' => 'ebd-teacher',
        'memberpanel.ebd.teacher.classes.show' => 'ebd-teacher',
        'memberpanel.ebd.teacher.lessons' => 'ebd-teacher',
        'memberpanel.ebd.teacher.lessons.show' => 'ebd-teacher',
        'memberpanel.ebd.teacher.attendance.manage' => 'ebd-teacher',
        'memberpanel.ebd.teacher.evaluations' => 'ebd-teacher',
        'memberpanel.ebd.teacher.evaluations.grade' => 'ebd-teacher',
        'memberpanel.ebd.arcade.leaderboard' => 'ebd-dashboard',
        'ebd.member.lms.player' => 'ebd-dashboard',
        'memberpanel.ebd.arcade.index' => 'ebd-arcade',
        'memberpanel.ebd.arcade.leaderboard' => 'ebd-arcade',
        'memberpanel.ebd.arcade.versemaster' => 'ebd-arcade',
        'memberpanel.ebd.arcade.quiz' => 'ebd-arcade',
        'memberpanel.ebd.arcade.memory' => 'ebd-arcade',
        'memberpanel.ebd.arcade.who-said-it' => 'ebd-arcade',
        'memberpanel.ebd.arcade.timeline' => 'ebd-arcade',
        'pastor.dashboard' => 'pastor-dashboard',
        'pastor.dashboard.index' => 'pastor-dashboard',
        'memberpanel.ebd.arcade.sword' => 'ebd-arcade',
        'memberpanel.ebd.arcade.hangman' => 'ebd-arcade',
        'ebd.member.quiz.client' => 'ebd-arcade',
        'member.intercessor.dashboard' => 'intercessor-room',
        'member.intercessor.requests.index' => 'intercessor-requests-list',
        'member.intercessor.requests.create' => 'intercessor-request-create',
        'member.intercessor.requests.show' => 'intercessor-requests-list',
        'member.intercessor.requests.edit' => 'intercessor-requests-list',
        'member.intercessor.room.index' => 'intercessor-room',
        'member.intercessor.room.testimonies' => 'intercessor-room',
        'member.intercessor.room.show' => 'intercessor-room-show',
        'worship.member.rosters.index' => 'worship-rosters',
        'worship.member.rehearsal.index' => 'worship-rehearsal',
        'worship.member.rehearsal.show' => 'worship-rehearsal',
        'worship.member.academy.index' => 'worship-academy',
        'worship.member.academy.classroom' => 'worship-academy',
        'worship.member.academy.course' => 'worship-academy',
        'worship.member.stage.view' => 'worship',
    ];

    /**
     * Fallback: prefixo de rota -> tourId (ordem do mais específico ao mais genérico).
     * Usado quando a rota não está em ROUTE_TOUR_MAP.
     */
    private const ROUTE_TOUR_PREFIX_FALLBACK = [
        'memberpanel.profile.' => 'profile',
        'memberpanel.notifications.' => 'notifications',
        'memberpanel.bible.' => 'bible-read',
        'memberpanel.events.' => 'events-list',
        'memberpanel.ministries.' => 'ministries',
        'memberpanel.treasury.' => 'treasury',
        'memberpanel.donations.' => 'donations',
        'memberpanel.sermons.' => 'sermons',
        'memberpanel.series.' => 'sermons-series',
        'memberpanel.studies.' => 'sermons-studies',
        'memberpanel.commentaries.' => 'sermons',
        'memberpanel.churchcouncil.' => 'churchcouncil',
        'memberpanel.projection.' => 'projection',
        'memberpanel.ebd.' => 'ebd-dashboard',
        'ebd.member.' => 'ebd-dashboard',
        'member.intercessor.' => 'intercessor-room',
        'worship.member.' => 'worship',
        'pastor.' => 'pastor-dashboard',
    ];

    /**
     * Mapeamento rota (prefixo ou nome) -> trigger_event para fallback de insights por página.
     */
    private const ROUTE_TO_PAGE_TRIGGER = [
        'memberpanel.dashboard' => 'page_dashboard',
        'memberpanel.profile' => 'page_profile',
        'memberpanel.notifications' => 'page_notifications',
        'memberpanel.bible' => 'page_bible',
        'memberpanel.events' => 'page_events',
        'memberpanel.ministries' => 'page_ministries',
        'memberpanel.treasury' => 'page_treasury',
        'memberpanel.donations' => 'page_donations',
        'memberpanel.sermons' => 'page_sermons',
        'memberpanel.series' => 'page_sermons',
        'memberpanel.studies' => 'page_sermons',
        'ebd.member' => 'page_ebd',
        'memberpanel.ebd' => 'page_ebd',
        'member.intercessor' => 'page_intercessor',
        'pastor.dashboard' => 'pastor_dashboard',
        'pastor.' => 'pastor_dashboard',
    ];

    /**
     * Avalia regras e retorna o primeiro insight aplicável, ou null.
     *
     * @return array{content: string, level: string, trigger: string, insight_key: string, tour_id?: string, medal?: array}|null
     */
    public function evaluate(User $user, ?string $routeName = null): ?array
    {
        $dismissed = session('cbav_bot_dismissed', []);
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $rules = CoachingRule::with(['medal', 'insight'])
            ->where('is_active', true)
            ->orderByDesc('priority')
            ->get();

        foreach ($rules as $rule) {
            $achievementKey = $rule->trigger_key . '_' . now()->format('Y-m');
            if (in_array($achievementKey, $dismissed, true)) {
                continue;
            }
            if (Achievement::hasAchieved($user, $achievementKey, $start)) {
                continue;
            }

            if (! $this->evaluateCondition($rule, $user)) {
                continue;
            }

            $content = $rule->message_override ?? $rule->insight?->content ?? '';
            if ($content === '') {
                continue;
            }

            Achievement::create([
                'user_id' => $user->id,
                'achievement_key' => $achievementKey,
                'triggered_at' => now(),
                'metadata' => ['route' => $routeName],
            ]);

            $medalPayload = null;
            if ($rule->medal_id && $rule->medal && ! UserMedal::hasUnlocked($user, $rule->medal_id)) {
                UserMedal::create([
                    'user_id' => $user->id,
                    'medal_id' => $rule->medal_id,
                    'unlocked_at' => now(),
                ]);
                event(new MedalUnlocked($user, $rule->medal));
                $medalPayload = [
                    'id' => $rule->medal->id,
                    'title' => $rule->medal->title,
                    'icon_name' => $rule->medal->icon_name,
                    'color' => $rule->medal->color,
                ];
            }

            $tourId = $routeName ? self::getTourIdForRoute($routeName) : null;

            return [
                'content' => $content,
                'level' => $rule->level ?? 'info',
                'trigger' => $rule->trigger_key,
                'insight_key' => $achievementKey,
                'tour_id' => $tourId,
                'medal' => $medalPayload,
            ];
        }

        return $this->getFallbackInsightForRoute($routeName);
    }

    /**
     * Fallback: retorna um insight aleatório da insights_bank para o contexto da rota atual.
     * Usa sessão para evitar repetição de dicas na mesma sessão e cooldown de 1 hora.
     *
     * @return array{content: string, level: string, trigger: string, insight_key: string, tour_id?: string}|null
     */
    public function getFallbackInsightForRoute(?string $routeName): ?array
    {
        $pageTrigger = $this->getPageTriggerForRoute($routeName);
        if ($pageTrigger === null) {
            return null;
        }

        $dismissed = session('cbav_bot_dismissed', []);
        $shownInsights = session('cbav_bot_shown_insights', []);
        $lastShownTime = session('cbav_bot_last_shown_time', 0);
        $cooldownMinutes = 60;

        $insightKey = 'fallback_' . $pageTrigger . '_' . now()->format('Y-m-d');
        if (in_array($insightKey, $dismissed, true)) {
            return null;
        }

        if (time() - $lastShownTime < ($cooldownMinutes * 60)) {
            return null;
        }

        $shownIds = $shownInsights[$pageTrigger] ?? [];

        $query = Insight::where('trigger_event', $pageTrigger)
            ->where('is_active', true)
            ->whereNotIn('trigger_event', self::FINANCIAL_TRIGGER_EVENTS);

        if (! empty($shownIds)) {
            $query->whereNotIn('id', $shownIds);
        }

        $insight = $query->inRandomOrder()->first();

        if (! $insight) {
            $shownInsights[$pageTrigger] = [];
            session(['cbav_bot_shown_insights' => $shownInsights]);

            $insight = Insight::where('trigger_event', $pageTrigger)
                ->where('is_active', true)
                ->whereNotIn('trigger_event', self::FINANCIAL_TRIGGER_EVENTS)
                ->inRandomOrder()
                ->first();
        }

        if (! $insight || $insight->content === '') {
            return null;
        }

        $shownInsights[$pageTrigger][] = $insight->id;
        session(['cbav_bot_shown_insights' => $shownInsights]);
        session(['cbav_bot_last_shown_time' => time()]);

        $tourId = $routeName ? self::getTourIdForRoute($routeName) : null;

        return [
            'content' => $insight->content,
            'level' => $insight->level ?? 'info',
            'trigger' => 'fallback_' . $pageTrigger,
            'insight_key' => $insightKey,
            'tour_id' => $tourId,
        ];
    }

    private function getPageTriggerForRoute(?string $routeName): ?string
    {
        if ($routeName === null || $routeName === '') {
            return 'page_dashboard';
        }
        foreach (self::ROUTE_TO_PAGE_TRIGGER as $prefix => $trigger) {
            if (str_starts_with($routeName, $prefix)) {
                return $trigger;
            }
        }
        return 'page_dashboard';
    }

    /**
     * Retorna o insight de "perfil completo" para exibir após o usuário salvar o perfil,
     * sem verificar achievement/dismissed (para garantir parabéns na próxima página).
     *
     * @return array{content: string, level: string, trigger: string, insight_key: string, tour_id?: string, medal?: array}|null
     */
    public function getProfileCompleteInsightForUser(User $user, ?string $routeName = null): ?array
    {
        $rule = CoachingRule::with(['medal', 'insight'])
            ->where('is_active', true)
            ->where('trigger_key', 'gospel_profile_complete')
            ->first();

        if (! $rule || ! $this->evaluateCondition($rule, $user)) {
            return null;
        }

        $content = $rule->message_override ?? $rule->insight?->content ?? '';
        if ($content === '') {
            return null;
        }

        $achievementKey = $rule->trigger_key . '_' . now()->format('Y-m');
        Achievement::firstOrCreate(
            [
                'user_id' => $user->id,
                'achievement_key' => $achievementKey,
            ],
            [
                'triggered_at' => now(),
                'metadata' => ['route' => $routeName, 'source' => 'profile_just_completed'],
            ]
        );

        $medalPayload = null;
        if ($rule->medal_id && $rule->medal && ! UserMedal::hasUnlocked($user, $rule->medal_id)) {
            UserMedal::create([
                'user_id' => $user->id,
                'medal_id' => $rule->medal_id,
                'unlocked_at' => now(),
            ]);
            event(new MedalUnlocked($user, $rule->medal));
            $medalPayload = [
                'id' => $rule->medal->id,
                'title' => $rule->medal->title,
                'icon_name' => $rule->medal->icon_name,
                'color' => $rule->medal->color,
            ];
        }

        $tourId = $routeName ? self::getTourIdForRoute($routeName) : self::getTourIdForRoute('memberpanel.profile.show');

        return [
            'content' => $content,
            'level' => $rule->level ?? 'success',
            'trigger' => $rule->trigger_key,
            'insight_key' => $achievementKey,
            'tour_id' => $tourId,
            'medal' => $medalPayload,
        ];
    }

    private function evaluateCondition(CoachingRule $rule, User $user): bool
    {
        $params = $rule->condition_params ?? [];
        $type = $rule->condition_type ?? '';

        return match ($type) {
            'profile_complete' => $this->checkProfileComplete($user, $params),
            'bible_favorites' => $this->checkBibleFavorites($user, $params),
            'events_count' => $this->checkEventsCount($user, $params),
            'ministries_count' => $this->checkMinistriesCount($user, $params),
            'first_steps' => $this->checkFirstSteps($user),
            default => false,
        };
    }

    private function checkProfileComplete(User $user, array $params): bool
    {
        $min = (int) ($params['percentage'] ?? 80);
        return $this->getProfileCompletion($user) >= $min;
    }

    /**
     * Completude do perfil (mesmos campos que User::getProfileCompletionPercentage / painel).
     */
    private function getProfileCompletion(User $user): int
    {
        $fields = User::PROFILE_FIELDS;
        $filled = 0;
        foreach ($fields as $field) {
            $value = $user->$field;
            if ($value !== null && $value !== '') {
                $filled++;
            }
        }
        return (int) round((count($fields) > 0 ? ($filled / count($fields)) : 0) * 100);
    }

    private function checkBibleFavorites(User $user, array $params): bool
    {
        $min = (int) ($params['count'] ?? 1);
        if (! method_exists($user, 'bibleFavorites')) {
            return false;
        }
        return $user->bibleFavorites()->count() >= $min;
    }

    private function checkEventsCount(User $user, array $params): bool
    {
        $min = (int) ($params['count'] ?? 1);
        if (! method_exists($user, 'registrations')) {
            return false;
        }
        return $user->registrations()->where('status', 'confirmed')->count() >= $min;
    }

    private function checkMinistriesCount(User $user, array $params): bool
    {
        $min = (int) ($params['count'] ?? 1);
        if (! method_exists($user, 'activeMinistries')) {
            return false;
        }
        return $user->activeMinistries()->count() >= $min;
    }

    private function checkFirstSteps(User $user): bool
    {
        $completed = $this->getProfileCompletion($user) >= 30;
        if ($completed && method_exists($user, 'bibleFavorites')) {
            $completed = $user->bibleFavorites()->count() >= 1;
        }
        return $completed;
    }

    public static function getTourIdForRoute(string $routeName): ?string
    {
        if ($routeName === '') {
            return null;
        }
        if (isset(self::ROUTE_TOUR_MAP[$routeName])) {
            return self::ROUTE_TOUR_MAP[$routeName];
        }
        foreach (self::ROUTE_TOUR_PREFIX_FALLBACK as $prefix => $tourId) {
            if (str_starts_with($routeName, $prefix)) {
                return $tourId;
            }
        }
        return null;
    }
}
