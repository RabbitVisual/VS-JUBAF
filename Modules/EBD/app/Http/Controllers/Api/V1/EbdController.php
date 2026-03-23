<?php

namespace Modules\EBD\App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\EBD\App\Services\EbdApiService;
use Modules\EBD\App\Services\QuizPollingService;

class EbdController extends Controller
{
    public function __construct(
        private QuizPollingService $quizService,
        private EbdApiService $apiService
    ) {}

    // ── Dashboard ──────────────────────────────────────────────

    public function dashboard(): JsonResponse
    {
        return response()->json(['data' => $this->apiService->getDashboardStats()]);
    }

    // ── Classes ────────────────────────────────────────────────

    public function classes(Request $request): JsonResponse
    {
        $activeOnly = $request->boolean('active', true);

        return response()->json(['data' => $this->apiService->getClasses($activeOnly)]);
    }

    public function classDetail(int $classId): JsonResponse
    {
        $class = $this->apiService->getClassDetail($classId);
        if (! $class) {
            return response()->json(['message' => 'Classe não encontrada.'], 404);
        }

        return response()->json(['data' => $class]);
    }

    // ── Lessons ────────────────────────────────────────────────

    public function lessons(Request $request): JsonResponse
    {
        $filters = $request->only(['class_id', 'status', 'from', 'to', 'per_page']);

        return response()->json(['data' => $this->apiService->getLessons($filters)]);
    }

    public function lessonDetail(int $lessonId): JsonResponse
    {
        $lesson = $this->apiService->getLessonDetail($lessonId);
        if (! $lesson) {
            return response()->json(['message' => 'Lição não encontrada.'], 404);
        }

        return response()->json(['data' => $lesson]);
    }

    // ── Student panel ──────────────────────────────────────────

    public function studentDashboard(): JsonResponse
    {
        $user = auth()->user();

        return response()->json(['data' => $this->apiService->getStudentDashboard($user)]);
    }

    // ── Teacher panel ──────────────────────────────────────────

    public function teacherDashboard(): JsonResponse
    {
        $user = auth()->user();

        return response()->json(['data' => $this->apiService->getTeacherDashboard($user)]);
    }

    // ── LMS Progress ───────────────────────────────────────────

    public function markLessonComplete(int $lessonId): JsonResponse
    {
        $user = auth()->user();
        $progress = $this->apiService->markLessonComplete($user, $lessonId);

        return response()->json(['data' => $progress]);
    }

    // ── Leaderboard ────────────────────────────────────────────

    public function leaderboard(Request $request): JsonResponse
    {
        $period = $request->input('period', 'all');
        $limit = min((int) $request->input('limit', 20), 100);

        return response()->json(['data' => $this->apiService->getLeaderboard($period, $limit)]);
    }

    // ── Quiz (existing) ────────────────────────────────────────

    public function quizStatus(string $session): JsonResponse
    {
        try {
            $state = $this->quizService->getState($session);

            return response()->json(['data' => $state]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Sessão não encontrada.'], 404);
        }
    }

    public function quizSubmit(Request $request, string $session): JsonResponse
    {
        $request->validate([
            'answer' => 'required|string',
            'student_id' => 'required|integer',
        ]);

        try {
            $this->quizService->submitAnswer(
                $session,
                (int) $request->student_id,
                $request->answer
            );

            return response()->json(['data' => ['success' => true]]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
