<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDEvaluation;
use Modules\EBD\App\Models\EBDLesson;

class EvaluationController extends Controller
{
    public function index(Request $request): View
    {
        $query = EBDEvaluation::with(['lesson.ebdClass', 'student.user', 'gradedBy']);

        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $evaluations = $query->orderBy('created_at', 'desc')->paginate(15);
        $lessons = EBDLesson::with('ebdClass')->orderBy('lesson_date', 'desc')->limit(50)->get();

        return view('ebd::pastoralpanel.evaluations.index', compact('evaluations', 'lessons'));
    }

    public function show(EBDEvaluation $evaluation): View
    {
        $evaluation->load(['lesson.ebdClass', 'student.user', 'gradedBy', 'lesson.questions']);

        return view('ebd::pastoralpanel.evaluations.show', compact('evaluation'));
    }
}
