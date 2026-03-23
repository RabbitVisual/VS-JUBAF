<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Modules\EBD\App\Models\EbdGamificationPoint;
use Modules\EBD\App\Models\EbdQuizSession;

class QuizPollingService
{
    protected $gamificationService;

    public function __construct(GamificationService $gamificationService)
    {
        $this->gamificationService = $gamificationService;
    }

    public function getState(string $sessionId): array
    {
        $session = EbdQuizSession::with('class')->where('id', $sessionId)->firstOrFail();

        return [
            'status' => $session->status,
            'current_question_index' => $session->current_question_index,
            'show_answer' => $session->show_answer,
            'updated_at' => $session->updated_at->toIso8601String(),
        ];
    }

    public function submitAnswer(string $sessionId, int $studentId, $answer): void
    {
        $session = EbdQuizSession::with('class')->where('id', $sessionId)->firstOrFail();

        if ($session->status !== 'active' || $session->show_answer) {
            return;
        }

        $class = $session->class;

        $lesson = $class->lessons()
            ->whereDate('lesson_date', now())
            ->first();

        if (! $lesson) {
            return;
        }

        $questions = $lesson->questions()->orderBy('order')->get();
        $question = $questions->get($session->current_question_index);

        if (! $question) {
            return;
        }

        $isCorrect = trim(strtolower($question->correct_answer)) === trim(strtolower($answer));

        if ($isCorrect) {
            $user = User::find($studentId);
            if (! $user) {
                return;
            }

            $description = "Quiz: {$lesson->title} - Q{$session->current_question_index}";
            $alreadyRewarded = EbdGamificationPoint::where('user_id', $user->id)
                ->where('source_type', 'quiz')
                ->where('description', $description)
                ->exists();

            if ($alreadyRewarded) {
                return;
            }

            $startTime = $session->updated_at;
            $elapsed = now()->diffInSeconds($startTime);
            $basePoints = 100;
            $timeBonus = max(0, 50 - $elapsed);
            $totalPoints = $basePoints + $timeBonus;

            $this->gamificationService->addXp($user, $totalPoints, 'quiz', $description);
        }
    }
}
