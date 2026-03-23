<?php

namespace Modules\EBD\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\EBD\App\Models\Game;
use Modules\EBD\App\Models\UserGameSession;
use Modules\EBD\App\Models\EbdGamificationPoint;
use Modules\EBD\App\Models\EbdXpRule;
use Modules\EBD\App\Services\GamificationService;
use Modules\EBD\App\Services\EbdLevelService;
use Modules\EBD\App\Services\EbdAchievementService;
use Modules\Bible\App\Models\Verse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArcadeController extends Controller
{
    protected $gamificationService;

    protected $ebdLevelService;

    public function __construct(
        GamificationService $gamificationService,
        EbdLevelService $ebdLevelService,
        protected EbdAchievementService $ebdAchievementService
    ) {
        $this->gamificationService = $gamificationService;
        $this->ebdLevelService = $ebdLevelService;
    }

    /**
     * Display Arcade Menu (Salão de Jogos).
     * Passa XP e nível EBD para exibir no topo – mesma fonte que o dashboard (users.xp).
     */
    public function index()
    {
        $user = Auth::user();
        $currentXp = (int) ($user->xp ?? 0);
        $sumFromHistory = (int) EbdGamificationPoint::where('user_id', $user->id)->sum('points');
        if ($currentXp === 0 && $sumFromHistory > 0) {
            $user->xp = $sumFromHistory;
            $user->save();
            $currentXp = $sumFromHistory;
        }

        $levelInfo = $this->ebdLevelService->getLevelInfo($currentXp);
        $userLevel = $levelInfo['currentLevel'];
        $nextLevel = $levelInfo['nextLevel'] ?? null;
        $nextLevelXp = $levelInfo['nextLevelXpTotal'];
        $xpInCurrentLevel = $levelInfo['xpInCurrentLevel'];
        $xpToNextLevel = $levelInfo['xpToNextLevel'];
        $hasNextLevel = $nextLevel !== null;

        $gamesPlayed = UserGameSession::where('user_id', $user->id)->count();
        $highScore = UserGameSession::where('user_id', $user->id)->max('score') ?? 0;
        $userXp = $currentXp;

        // Calculate user streak (consecutive days played)
        $userStreak = $this->calculateUserStreak($user->id);

        // Get weekly top 5 ranking (unified: one row per user, sum of scores)
        $weeklyRanking = $this->getRankingForFilter('weekly', 5);

        // Get current user rank
        $userRank = $this->getUserRank($user->id, 'weekly');

        $levelTier = $userLevel->tier ?? 'bronze';

        return view('ebd::games.index', compact(
            'userXp',
            'userLevel',
            'levelTier',
            'nextLevelXp',
            'xpInCurrentLevel',
            'xpToNextLevel',
            'hasNextLevel',
            'gamesPlayed',
            'highScore',
            'userStreak',
            'weeklyRanking',
            'userRank'
        ));
    }

    /**
     * Calculate consecutive days played streak.
     */
    private function calculateUserStreak(int $userId): int
    {
        $dates = UserGameSession::where('user_id', $userId)
            ->where('completed_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(completed_at) as play_date')
            ->groupBy('play_date')
            ->orderBy('play_date', 'desc')
            ->pluck('play_date')
            ->toArray();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Check if played today or yesterday (to continue streak)
        if (!in_array($today, $dates) && !in_array($yesterday, $dates)) {
            return 0;
        }

        // Count consecutive days
        $checkDate = in_array($today, $dates) ? now() : now()->subDay();
        
        for ($i = 0; $i < 30; $i++) {
            if (in_array($checkDate->toDateString(), $dates)) {
                $streak++;
                $checkDate->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Get user's rank in leaderboard (1-based position).
     */
    private function getUserRank(int $userId, string $filter = 'weekly'): int|string
    {
        $rows = $this->getRankingRowsForFilter($filter, null);
        $position = 1;
        foreach ($rows as $row) {
            if ((int) $row->user_id === $userId) {
                return $position;
            }
            $position++;
        }
        return '-';
    }

    /**
     * Build ranking: one entry per user with total_score (unified, no duplicates).
     * Returns collection of stdClass with user_id, total_score and loaded user relation.
     */
    private function getRankingForFilter(string $filter, ?int $limit = 50): \Illuminate\Support\Collection
    {
        $rows = $this->getRankingRowsForFilter($filter, $limit);
        $userIds = $rows->pluck('user_id')->unique()->values()->all();
        $users = \App\Models\User::whereIn('id', $userIds)->get()->keyBy('id');

        return $rows->map(function ($row, $index) use ($users) {
            $row->position = $index + 1;
            $row->user = $users->get($row->user_id);
            $row->total_score = (int) $row->total_score;
            return $row;
        })->values();
    }

    /**
     * Raw ranking rows: one row per user_id with SUM(score), ordered by total_score desc.
     */
    private function getRankingRowsForFilter(string $filter, ?int $limit): \Illuminate\Support\Collection
    {
        $query = DB::table('ebd_user_game_sessions')
            ->select('user_id', DB::raw('SUM(score) as total_score'))
            ->whereNotNull('completed_at')
            ->groupBy('user_id')
            ->orderByDesc('total_score');

        if ($filter === 'weekly') {
            $query->where('completed_at', '>=', now()->startOfWeek());
        } elseif ($filter === 'monthly') {
            $query->where('completed_at', '>=', now()->startOfMonth());
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $rows = $query->get();

        // Garantir um único registro por user_id (por segurança)
        return $rows->unique('user_id')->values();
    }

    /**
     * Display Leaderboard.
     */
    public function leaderboard(Request $request)
    {
        $filter = $request->query('filter', 'weekly');
        $rankings = $this->getRankingForFilter($filter, 50);

        $userRank = '—';
        $userScore = 0;
        $currentUser = Auth::user();

        if ($currentUser) {
            $userEntry = $rankings->firstWhere('user_id', $currentUser->id);
            if ($userEntry) {
                $userRank = $userEntry->position;
                $userScore = (int) $userEntry->total_score;
            } else {
                $allRows = $this->getRankingRowsForFilter($filter, null);
                $myRow = $allRows->firstWhere('user_id', $currentUser->id);
                $userScore = $myRow ? (int) $myRow->total_score : 0;
                $position = 1;
                foreach ($allRows as $row) {
                    if ((int) $row->user_id === (int) $currentUser->id) {
                        $userRank = $position;
                        break;
                    }
                    $position++;
                }
            }
        }

        return view('ebd::memberpanel.arcade.leaderboard', compact('rankings', 'filter', 'userRank', 'userScore'));
    }

    /**
     * Display Quiz Game.
     */
    public function quiz()
    {
        return view('ebd::memberpanel.arcade.quiz');
    }

    /**
     * Get Quiz Data (Questions/Answers).
     */
    public function getQuizData()
    {
        $game = Game::where('slug', 'mestre-do-conhecimento')->firstOrFail();

        $seenIds = session()->get('arcade_seen_questions', []);

        $questions = $game->questions()->with('answers')
            ->whereNotIn('id', $seenIds)
            ->inRandomOrder()
            ->take(10)
            ->get();

        // Check if we need more questions (if ran out)
        if ($questions->count() < 10) {
            session()->forget('arcade_seen_questions');
            $seenIds = [];
            $needed = 10 - $questions->count();

            $moreQuestions = $game->questions()->with('answers')
                ->whereNotIn('id', $questions->pluck('id'))
                ->inRandomOrder()
                ->take($needed)
                ->get();

            $questions = $questions->merge($moreQuestions);
        }

        // Update Session
        $seenIds = array_merge($seenIds, $questions->pluck('id')->toArray());
        if (count($seenIds) > 100) $seenIds = array_slice($seenIds, -50); // Keep last 50
        session()->put('arcade_seen_questions', $seenIds);

        return response()->json([
            'questions' => $questions
        ]);
    }

    /**
     * Display VerseMaster Game.
     */
    public function versemaster()
    {
        $verse = $this->getRandomExcludingSession(Verse::with(['chapter.book']), 'arcade_seen_verses');
        return view('ebd::memberpanel.arcade.versemaster', compact('verse'));
    }

    public function memory()
    {
        return view('ebd::memberpanel.arcade.memory');
    }

    public function whoSaidIt()
    {
        return view('ebd::memberpanel.arcade.who-said-it');
    }

    public function timeline()
    {
        return view('ebd::memberpanel.arcade.timeline');
    }

    public function sword()
    {
        return view('ebd::memberpanel.arcade.sword');
    }

    public function hangman()
    {
        return view('ebd::memberpanel.arcade.hangman');
    }

    /**
     * Display Word Search Game.
     */
    public function wordsearch()
    {
        return view('ebd::memberpanel.arcade.wordsearch');
    }

    /**
     * Display Hero of Faith Game.
     */
    public function hero()
    {
        return view('ebd::memberpanel.arcade.hero');
    }

    /**
     * Display Fill the Blank Game.
     */
    public function fillblank()
    {
        return view('ebd::memberpanel.arcade.fillblank');
    }

    /**
     * Display Book Challenge Game.
     */
    public function bookchallenge()
    {
        return view('ebd::memberpanel.arcade.bookchallenge');
    }

    /**
     * Display Biblical Trio Game.
     */
    public function trio()
    {
        return view('ebd::memberpanel.arcade.trio');
    }

    /**
     * Display Crossword Game.
     */
    public function crossword()
    {
        return view('ebd::memberpanel.arcade.crossword');
    }

    /**
     * Display Parables Match Game.
     */
    public function parables()
    {
        return view('ebd::memberpanel.arcade.parables');
    }

    /**
     * Display Bible Navigator Game.
     */
    public function navigator()
    {
        return view('ebd::memberpanel.arcade.navigator');
    }

    /**
     * Get a random verse for game AJAX updates.
     */
    public function getVerse()
    {
        $verse = $this->getRandomExcludingSession(Verse::with(['chapter.book']), 'arcade_seen_verses');

        if (!$verse) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum versículo encontrado no banco de dados.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'reference' => $verse->full_reference,
            'text' => $verse->text,
        ]);
    }

    /**
     * Helper to get random item excluding session history
     */
    private function getRandomExcludingSession($query, $sessionKey) {
        $seenIds = session()->get($sessionKey, []);

        $item = $query->whereNotIn('id', $seenIds)->inRandomOrder()->first();

        if (!$item) {
            session()->forget($sessionKey);
            $seenIds = [];
            $item = $query->inRandomOrder()->first();
        }

        if ($item) {
            $seenIds[] = $item->id;
            if (count($seenIds) > 20) array_shift($seenIds);
            session()->put($sessionKey, $seenIds);
        }

        return $item;
    }

    /**
     * Submit a score for a game.
     * Returns JSON in all cases (success and errors) so the frontend never receives HTML.
     */
    public function submitScore(Request $request, $gameSlug)
    {
        try {
            $request->validate([
                'score' => 'required|integer',
                'metadata' => 'nullable|array',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'xp_gained' => 0,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $game = Game::where('slug', $gameSlug)->where('is_active', true)->first();

        // Ensure arcade games exist if seeder was not run
        if (!$game) {
            $defaults = $this->getArcadeGameDefaults($gameSlug);
            if ($defaults) {
                $game = Game::firstOrCreate(
                    ['slug' => $gameSlug],
                    array_merge($defaults, ['is_active' => true])
                );
            }
        }

        if (!$game) {
            return response()->json([
                'success' => false,
                'xp_gained' => 0,
                'message' => 'Jogo não encontrado ou inativo.',
            ], 404);
        }

        $user = Auth::user();

        // Save Score
        UserGameSession::create([
            'user_id' => $user->id,
            'game_id' => $game->id,
            'score' => $request->score,
            'completed_at' => now(),
            'duration' => (int) ($request->input('metadata.duration') ?? 0),
        ]);

        // XP from ebd_xp_rules (balanceamento em DB)
        $rule = EbdXpRule::where('source_type', 'game')
            ->where(function ($q) use ($gameSlug) {
                $q->where('source_slug', $gameSlug)->orWhereNull('source_slug');
            })
            ->where('is_active', true)
            ->orderByRaw('source_slug IS NOT NULL DESC')
            ->first();
        $xp = $rule ? $rule->calculateXp((int) $request->score) : min(50, max(1, (int) ($request->score * 0.1)));

        if ($xp > 0) {
            $this->gamificationService->addXp($user, $xp, 'game', "Jogo: {$game->name}");
        }

        $this->ebdAchievementService->evaluateForUser($user->fresh());

        return response()->json([
            'success' => true,
            'xp_gained' => $xp,
            'message' => "Pontuação salva! +{$xp} XP",
        ]);
    }

    /**
     * Default attributes for arcade games when seeder was not run.
     * Keys must match route slugs used by the frontend (e.g. heroi-da-fe, trio-biblico).
     */
    private function getArcadeGameDefaults(string $slug): ?array
    {
        $games = [
            'heroi-da-fe' => ['name' => 'Herói da Fé', 'description' => 'Descubra personagens bíblicos através de dicas.', 'icon' => 'user-crown'],
            'trio-biblico' => ['name' => 'Trio Bíblico', 'description' => 'Encontre grupos de 3 itens relacionados.', 'icon' => 'clone'],
            'mestre-do-conhecimento' => ['name' => 'Mestre do Conhecimento', 'description' => 'Teste seus conhecimentos bíblicos.', 'icon' => 'brain-circuit'],
            'arca-da-memoria' => ['name' => 'Arca da Memória', 'description' => 'Encontre pares de personagens e eventos bíblicos.', 'icon' => 'cards'],
            'quem-disse' => ['name' => 'Quem Disse?', 'description' => 'Adivinhe qual personagem bíblico fez a citação.', 'icon' => 'comment-quote'],
            'linha-do-tempo' => ['name' => 'Linha do Tempo', 'description' => 'Ordene eventos bíblicos cronologicamente.', 'icon' => 'hourglass-start'],
            'espada-afiada' => ['name' => 'Espada Afiada', 'description' => 'Teste seus reflexos com os livros da Bíblia.', 'icon' => 'sword'],
            'forca-da-fe' => ['name' => 'Forca da Fé', 'description' => 'Adivinhe palavras bíblicas letra por letra.', 'icon' => 'keyboard'],
            'versemaster' => ['name' => 'VerseMaster', 'description' => 'Monte versículos na ordem correta.', 'icon' => 'scroll'],
            'caca-palavras' => ['name' => 'Caça-Palavras Bíblico', 'description' => 'Encontre palavras no grid.', 'icon' => 'magnifying-glass'],
            'complete-o-versiculo' => ['name' => 'Complete o Versículo', 'description' => 'Preencha as lacunas em versículos.', 'icon' => 'pen-to-square'],
            'desafio-dos-livros' => ['name' => 'Desafio dos Livros', 'description' => 'Categorize os 66 livros da Bíblia.', 'icon' => 'book-bible'],
            'palavras-cruzadas' => ['name' => 'Palavras Cruzadas', 'description' => 'Termos bíblicos.', 'icon' => 'grid-2'],
            'parabolas' => ['name' => 'Parábolas em Ação', 'description' => 'Conecte parábolas aos ensinos.', 'icon' => 'book-open'],
            'navegador-biblico' => ['name' => 'Navegador Bíblico', 'description' => 'Encontre versículos contra o relógio.', 'icon' => 'compass'],
        ];

        return $games[$slug] ?? null;
    }
}
