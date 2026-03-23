<?php

namespace Modules\EBD\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EBD\App\Services\QuizPollingService;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizPollingService $quizService)
    {
        $this->quizService = $quizService;
    }

    /**
     * Show the quiz client.
     */
    public function showClient($sessionId)
    {
        return view('ebd::memberpanel.quiz.client', compact('sessionId'));
    }

    /**
     * API: Get Quiz Status
     */
    public function status($sessionId)
    {
        try {
            $state = $this->quizService->getState($sessionId);
            return response()->json($state);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Session not found'], 404);
        }
    }

    /**
     * API: Submit Answer
     */
    public function submitAnswer(Request $request, $sessionId)
    {
        $request->validate([
            'answer' => 'required|string',
            'student_id' => 'required|integer'
        ]);

        try {
            $this->quizService->submitAnswer(
                $sessionId,
                $request->student_id,
                $request->answer
            );
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
