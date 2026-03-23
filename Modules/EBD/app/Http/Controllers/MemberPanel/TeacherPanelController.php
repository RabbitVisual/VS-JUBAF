<?php

namespace Modules\EBD\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDAttendance;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDEvaluation;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EBDTeacher;
use Modules\EBD\App\Models\EbdStudentProgress;
use Modules\EBD\App\Services\AttendanceService;

class TeacherPanelController extends Controller
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}
    /**
     * Teacher Dashboard
     */
    public function index(): View
    {
        $user = auth()->user();

        // Get classes where user is teacher
        $teacherClasses = EBDTeacher::where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['ebdClass.activeStudents'])
            ->get();

        $classIds = $teacherClasses->pluck('class_id');

        // Get upcoming lessons
        $upcomingLessons = EBDLesson::whereIn('class_id', $classIds)
            ->where('lesson_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['ebdClass'])
            ->orderBy('lesson_date', 'asc')
            ->limit(5)
            ->get();

        // Get recent lessons
        $recentLessons = EBDLesson::whereIn('class_id', $classIds)
            ->where('lesson_date', '<', now())
            ->with(['ebdClass', 'attendance'])
            ->orderBy('lesson_date', 'desc')
            ->limit(5)
            ->get();

        // Get pending evaluations to grade
        $pendingEvaluations = EBDEvaluation::whereHas('lesson', function ($query) use ($classIds) {
            $query->whereIn('class_id', $classIds);
        })
            ->whereIn('status', ['completed', 'pending'])
            ->with(['student.user', 'lesson.ebdClass'])
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        // Statistics
        $stats = [
            'total_classes' => $teacherClasses->count(),
            'total_students' => EBDStudent::whereIn('class_id', $classIds)
                ->where('is_active', true)
                ->count(),
            'upcoming_lessons' => $upcomingLessons->count(),
            'pending_evaluations' => $pendingEvaluations->where('status', 'completed')->count(),
        ];

        return view('ebd::memberpanel.teacher.dashboard', compact('teacherClasses', 'upcomingLessons', 'recentLessons', 'pendingEvaluations', 'stats'));
    }

    /**
     * List teacher's classes
     */
    public function myClasses(): View
    {
        $user = auth()->user();

        $teacherClasses = EBDTeacher::where('user_id', $user->id)
            ->with(['ebdClass.activeStudents.user', 'ebdClass.teachers.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ebd::memberpanel.teacher.classes.index', compact('teacherClasses'));
    }

    /**
     * Show class details with students
     */
    public function classStudents(EBDClass $class): View
    {
        $user = auth()->user();

        // Verify teacher belongs to this class
        $isTeacher = EBDTeacher::where('user_id', $user->id)
            ->where('class_id', $class->id)
            ->where('is_active', true)
            ->exists();

        if (! $isTeacher) {
            abort(403, 'Você não é professor desta classe.');
        }

        $class->load([
            'activeStudents.user',
            'activeStudents.attendance',
            'activeStudents.evaluations',
            'teachers.user',
        ]);

        // Get class statistics
        $totalLessons = EBDLesson::where('class_id', $class->id)->count();

        return view('ebd::memberpanel.teacher.classes.show', compact('class', 'totalLessons'));
    }

    /**
     * List lessons for teacher's classes
     */
    public function lessons(Request $request): View
    {
        $user = auth()->user();

        $classIds = EBDTeacher::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('class_id');

        $query = EBDLesson::whereIn('class_id', $classIds)
            ->with(['ebdClass', 'attendance', 'materials']);

        // Filter by class
        if ($request->has('class_id') && ! empty($request->class_id)) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $now = now();

        $lessons = $query
            ->orderByRaw(
                "CASE WHEN lesson_date < ? OR (lesson_date = ? AND lesson_time <= ?) THEN 0 ELSE 1 END ASC",
                [
                    $now->toDateString(),
                    $now->toDateString(),
                    $now->format('H:i:s'),
                ]
            )
            ->orderBy('lesson_date', 'asc')
            ->orderBy('lesson_time', 'asc')
            ->paginate(15);

        $classes = EBDClass::whereIn('id', $classIds)->orderBy('name')->get();

        // Completion stats per lesson (X/Y alunos concluíram)
        $completionStats = [];
        if ($lessons->count() > 0) {
            $lessonIds = $lessons->pluck('id')->all();

            // Active students by class
            $activeStudentsByClass = EBDStudent::whereIn('class_id', $classIds)
                ->where('is_active', true)
                ->get()
                ->groupBy('class_id');

            // Progress records for these lessons
            $completedProgressByLesson = EbdStudentProgress::whereIn('lesson_id', $lessonIds)
                ->where('status', 'completed')
                ->get()
                ->groupBy('lesson_id');

            foreach ($lessons as $lesson) {
                $totalStudents = isset($activeStudentsByClass[$lesson->class_id])
                    ? $activeStudentsByClass[$lesson->class_id]->count()
                    : 0;

                $completed = isset($completedProgressByLesson[$lesson->id])
                    ? $completedProgressByLesson[$lesson->id]->count()
                    : 0;

                $completionStats[$lesson->id] = [
                    'completed' => $completed,
                    'total' => $totalStudents,
                ];
            }
        }

        return view('ebd::memberpanel.teacher.lessons.index', compact('lessons', 'classes', 'completionStats'));
    }

    /**
     * Show lesson details for teacher
     */
    public function showLesson(EBDLesson $lesson): View
    {
        $user = auth()->user();

        // Verify teacher belongs to this lesson's class
        $isTeacher = EBDTeacher::where('user_id', $user->id)
            ->where('class_id', $lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isTeacher) {
            abort(403, 'Você não é professor desta classe.');
        }

        $lesson->load([
            'ebdClass.activeStudents.user',
            'materials',
            'questions',
            'attendance.student.user',
            'evaluations.student.user',
        ]);

        // Get Bible content if available
        $bibleService = app(\Modules\EBD\App\Services\BibleService::class);
        $bibleContent = null;
        if ($lesson->bible_book && $lesson->bible_chapter) {
            $bibleContent = collect($bibleService->getVerses(
                $lesson->bible_book,
                $lesson->bible_chapter,
                $lesson->bible_version ?? 'nvi',
                $lesson->bible_verses
            ));
        }

        return view('ebd::memberpanel.teacher.lessons.show', compact('lesson', 'bibleContent'));
    }

    /**
     * Show attendance management form
     */
    public function manageAttendance(EBDLesson $lesson): View
    {
        $user = auth()->user();

        // Verify teacher belongs to this lesson's class
        $isTeacher = EBDTeacher::where('user_id', $user->id)
            ->where('class_id', $lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isTeacher) {
            abort(403, 'Você não é professor desta classe.');
        }

        $lesson->load([
            'ebdClass.activeStudents.user',
            'attendance.student.user',
        ]);

        // Get existing attendance indexed by student_id
        $existingAttendance = $lesson->attendance->keyBy('student_id');

        return view('ebd::memberpanel.teacher.attendance.manage', compact('lesson', 'existingAttendance'));
    }

    /**
     * Store or update attendance
     */
    public function storeAttendance(Request $request, EBDLesson $lesson): RedirectResponse
    {
        $user = auth()->user();

        // Verify teacher belongs to this lesson's class
        $isTeacher = EBDTeacher::where('user_id', $user->id)
            ->where('class_id', $lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isTeacher) {
            abort(403, 'Você não é professor desta classe.');
        }

        $validated = $request->validate([
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:ebd_students,id',
            'attendance.*.status' => 'required|in:present,absent,late,excused',
            'attendance.*.arrival_time' => 'nullable|date_format:H:i',
            'attendance.*.notes' => 'nullable|string|max:500',
        ]);

        foreach ($validated['attendance'] as $attendanceData) {
            $student = EBDStudent::find($attendanceData['student_id']);
            if ($student) {
                $extra = array_filter([
                    'arrival_time' => ! empty($attendanceData['arrival_time'])
                        ? $lesson->lesson_date->format('Y-m-d').' '.$attendanceData['arrival_time'].':00'
                        : null,
                    'notes' => $attendanceData['notes'] ?? null,
                ]);
                $this->attendanceService->record(
                    $lesson,
                    $student,
                    $attendanceData['status'],
                    $user,
                    $extra
                );
            }
        }

        return redirect()->route('memberpanel.ebd.teacher.lessons.show', $lesson)
            ->with('success', 'Presença registrada com sucesso!');
    }

    /**
     * List evaluations to grade
     */
    public function evaluations(): View
    {
        $user = auth()->user();

        $classIds = EBDTeacher::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('class_id');

        $evaluations = EBDEvaluation::whereHas('lesson', function ($query) use ($classIds) {
            $query->whereIn('class_id', $classIds);
        })
            ->with(['student.user', 'lesson.ebdClass'])
            ->orderByRaw("CASE
            WHEN status = 'completed' THEN 1
            WHEN status = 'pending' THEN 2
            WHEN status = 'graded' THEN 3
            ELSE 4
        END")
            ->orderBy('completed_at', 'desc')
            ->paginate(20);

        return view('ebd::memberpanel.teacher.evaluations.index', compact('evaluations'));
    }

    /**
     * Show evaluation grading form
     */
    public function gradeEvaluation(EBDEvaluation $evaluation): View
    {
        $user = auth()->user();

        // Verify teacher belongs to this evaluation's class
        $isTeacher = EBDTeacher::where('user_id', $user->id)
            ->where('class_id', $evaluation->lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isTeacher) {
            abort(403, 'Você não tem permissão para corrigir esta avaliação.');
        }

        $evaluation->load(['student.user', 'lesson.ebdClass', 'lesson.questions']);

        return view('ebd::memberpanel.teacher.evaluations.grade', compact('evaluation'));
    }

    /**
     * Store evaluation grade
     */
    public function storeGrade(Request $request, EBDEvaluation $evaluation): RedirectResponse
    {
        $user = auth()->user();

        // Verify teacher belongs to this evaluation's class
        $isTeacher = EBDTeacher::where('user_id', $user->id)
            ->where('class_id', $evaluation->lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isTeacher) {
            abort(403, 'Você não tem permissão para corrigir esta avaliação.');
        }

        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $evaluation->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => EBDEvaluation::STATUS_GRADED,
            'graded_at' => now(),
            'graded_by' => $user->id,
        ]);

        return redirect()->route('memberpanel.ebd.teacher.evaluations')
            ->with('success', 'Avaliação corrigida com sucesso!');
    }
}
