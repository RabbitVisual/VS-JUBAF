<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\EBD\App\Models\Game;
use Modules\EBD\App\Models\GameQuestion;
use Modules\EBD\App\Models\GameAnswer;
use Illuminate\Support\Facades\DB;

class GameQuestionController extends Controller
{
    public function store(Request $request, Game $game)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'correct_answer_index' => 'required|integer|min:0',
        ]);

        DB::transaction(function() use ($game, $validated) {
            $question = $game->questions()->create([
                'question_text' => $validated['question_text'],
                'difficulty' => $validated['difficulty'],
            ]);

            foreach ($validated['answers'] as $index => $answerData) {
                $question->answers()->create([
                    'answer_text' => $answerData['text'],
                    'is_correct' => $index == $validated['correct_answer_index'],
                ]);
            }
        });

        return back()->with('success', 'Pergunta adicionada com sucesso.');
    }

    public function update(Request $request, GameQuestion $question)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'difficulty' => 'required|in:easy,medium,hard',
            'answers' => 'required|array|min:2',
            'answers.*.id' => 'nullable|exists:ebd_game_answers,id',
            'answers.*.text' => 'required|string',
            'correct_answer_index' => 'required|integer',
        ]);

        DB::transaction(function() use ($question, $validated) {
            $question->update([
                'question_text' => $validated['question_text'],
                'difficulty' => $validated['difficulty'],
            ]);

            // Sync answers strategy:
            // 1. Update existing ones if ID present
            // 2. Create new ones if no ID
            // 3. Delete ones not in request (handled by UI deleting them, or simplistic approach: delete all recreate)
            // Simpler approach for "Repeater" robustness: Delete all answers and recreate.
            // BUT this changes IDs. If we don't track answer stats, it's fine.
            // Let's use delete-recreate for simplicity and stability of the logic.

            $question->answers()->delete();

            foreach ($validated['answers'] as $index => $answerData) {
                $question->answers()->create([
                    'answer_text' => $answerData['text'],
                    'is_correct' => $index == $validated['correct_answer_index'],
                ]);
            }
        });

        return back()->with('success', 'Pergunta atualizada com sucesso.');
    }

    public function destroy(GameQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Pergunta removida com sucesso.');
    }
}
