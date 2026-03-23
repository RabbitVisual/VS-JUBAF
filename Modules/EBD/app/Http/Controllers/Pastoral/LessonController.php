<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDCourse;
use Modules\EBD\App\Models\EBDLesson;

class LessonController extends Controller
{
    public function index(Request $request): View
    {
        $query = EBDLesson::with(['ebdClass', 'course', 'creator']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('lesson_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('lesson_date', '<=', $request->date_to);
        }

        $lessons = $query->orderBy('lesson_date', 'desc')->paginate(15);
        $classes = EBDClass::where('is_active', true)->orderBy('name')->get();
        $courses = EBDCourse::orderBy('name')->get();

        return view('ebd::pastoralpanel.lessons.index', compact('lessons', 'classes', 'courses'));
    }

    public function show(EBDLesson $lesson): View
    {
        $lesson->load(['ebdClass', 'course', 'creator', 'materials', 'attendance.student.user', 'evaluations.student.user']);

        $bibleContent = null;
        if (class_exists(\Modules\EBD\App\Services\BibleService::class) && $lesson->bible_book && $lesson->bible_chapter) {
            $bibleService = app(\Modules\EBD\App\Services\BibleService::class);
            $bibleContent = collect($bibleService->getVerses(
                $lesson->bible_book,
                $lesson->bible_chapter,
                $lesson->bible_version ?? 'nvi',
                $lesson->bible_verses
            ));
        }

        return view('ebd::pastoralpanel.lessons.show', compact('lesson', 'bibleContent'));
    }
}
