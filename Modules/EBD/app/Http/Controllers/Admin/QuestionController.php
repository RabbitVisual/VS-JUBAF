<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDLessonQuestion;

class QuestionController extends Controller
{
    /**
     * Show the form for creating a new question
     */
    public function create(EBDLesson $lesson): View
    {
        return view('ebd::admin.questions.create', compact('lesson'));
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request, EBDLesson $lesson): RedirectResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'options' => 'required_if:type,multiple_choice|array',
            'options.*' => 'required|string|max:255',
            'correct_answer' => 'nullable|string|max:1000',
            'points' => 'required|integer|min:1|max:100',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['lesson_id'] = $lesson->id;
        $validated['is_required'] = $request->has('is_required');

        // Get max order if not provided
        if (! isset($validated['order'])) {
            $maxOrder = EBDLessonQuestion::where('lesson_id', $lesson->id)->max('order') ?? 0;
            $validated['order'] = $maxOrder + 1;
        }

        EBDLessonQuestion::create($validated);

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Questão adicionada com sucesso!');
    }

    /**
     * Show the form for editing a question
     */
    public function edit(EBDLesson $lesson, EBDLessonQuestion $question): View
    {
        if ($question->lesson_id !== $lesson->id) {
            abort(404);
        }

        return view('ebd::admin.questions.edit', compact('lesson', 'question'));
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, EBDLesson $lesson, EBDLessonQuestion $question): RedirectResponse
    {
        if ($question->lesson_id !== $lesson->id) {
            abort(404);
        }

        $validated = $request->validate([
            'question' => 'required|string|max:1000',
            'type' => 'required|in:multiple_choice,true_false,short_answer,essay',
            'options' => 'required_if:type,multiple_choice|array',
            'options.*' => 'required|string|max:255',
            'correct_answer' => 'nullable|string|max:1000',
            'points' => 'required|integer|min:1|max:100',
            'order' => 'nullable|integer|min:0',
            'is_required' => 'nullable|boolean',
        ]);

        $validated['is_required'] = $request->has('is_required');

        $question->update($validated);

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Questão atualizada com sucesso!');
    }

    /**
     * Remove the specified question
     */
    public function destroy(EBDLesson $lesson, EBDLessonQuestion $question): RedirectResponse
    {
        if ($question->lesson_id !== $lesson->id) {
            abort(404);
        }

        $question->delete();

        return redirect()->route('admin.ebd.lessons.show', $lesson)
            ->with('success', 'Questão removida com sucesso!');
    }
}
