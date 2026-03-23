<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDEvaluation;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;

class EvaluationController extends Controller
{
    /**
     * Display a listing of evaluations
     */
    public function index(Request $request): View
    {
        $query = EBDEvaluation::with(['lesson.ebdClass', 'student.user', 'gradedBy']);

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->input('lesson_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $evaluations = $query->orderBy('created_at', 'desc')->paginate(15);
        $lessons = EBDLesson::with('ebdClass')->orderBy('lesson_date', 'desc')->limit(50)->get();

        return view('ebd::admin.evaluations.index', compact('evaluations', 'lessons'));
    }

    /**
     * Show evaluation details
     */
    public function show(EBDEvaluation $evaluation): View
    {
        $evaluation->load(['lesson.ebdClass', 'student.user', 'gradedBy', 'lesson.questions']);

        return view('ebd::admin.evaluations.show', compact('evaluation'));
    }

    /**
     * Grade an evaluation
     */
    public function grade(Request $request, EBDEvaluation $evaluation): RedirectResponse
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $validated['status'] = EBDEvaluation::STATUS_GRADED;
        $validated['graded_by'] = auth()->id();
        $validated['graded_at'] = now();

        $evaluation->update($validated);

        return redirect()->route('admin.ebd.evaluations.show', $evaluation)
            ->with('success', 'Avaliação corrigida com sucesso!');
    }

    /**
     * Create evaluations from lesson questions for all students
     */
    public function createFromLesson(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:ebd_lessons,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:ebd_students,id',
        ]);

        $lesson = EBDLesson::with('questions')->findOrFail($validated['lesson_id']);

        // Check if lesson has questions
        if ($lesson->questions->count() === 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Esta lição não possui questões. Adicione questões antes de criar avaliações.']);
        }

        // Get students - either specific ones or all students in the class
        if (! empty($validated['student_ids'])) {
            $students = EBDStudent::where('class_id', $lesson->class_id)
                ->whereIn('id', $validated['student_ids'])
                ->where('is_active', true)
                ->get();
        } else {
            $students = EBDStudent::where('class_id', $lesson->class_id)
                ->where('is_active', true)
                ->get();
        }

        $created = 0;
        foreach ($students as $student) {
            // Check if evaluation already exists
            $exists = EBDEvaluation::where('lesson_id', $lesson->id)
                ->where('student_id', $student->id)
                ->exists();

            if (! $exists) {
                EBDEvaluation::create([
                    'lesson_id' => $lesson->id,
                    'student_id' => $student->id,
                    'status' => EBDEvaluation::STATUS_PENDING,
                ]);
                $created++;
            }
        }

        if ($created > 0) {
            return redirect()->route('admin.ebd.lessons.show', $lesson)
                ->with('success', "{$created} avaliação(ões) criada(s) com sucesso!");
        } else {
            return redirect()->route('admin.ebd.lessons.show', $lesson)
                ->with('info', 'Todas as avaliações já foram criadas para os alunos selecionados.');
        }
    }

    /**
     * Remove the specified evaluation
     */
    public function destroy(EBDEvaluation $evaluation): RedirectResponse
    {
        $evaluation->delete();

        return redirect()->route('admin.ebd.evaluations.index')
            ->with('success', 'Avaliação removida com sucesso!');
    }
}
