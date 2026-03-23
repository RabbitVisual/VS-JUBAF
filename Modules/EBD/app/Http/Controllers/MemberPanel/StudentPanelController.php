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
use Modules\EBD\App\Models\EbdStudentProgress;
use Modules\EBD\App\Services\EbdLessonAvailabilityService;
use Modules\EBD\App\Services\GamificationService;

class StudentPanelController extends Controller
{
    public function __construct(
        private EbdLessonAvailabilityService $availabilityService,
        private GamificationService $gamification
    ) {}

    /**
     * EAD Christian Academy - Unified Dashboard
     */
    public function index(): View
    {
        $user = auth()->user();

        // 1. Enrolled Classes & Upcoming Lessons
        $studentEnrollments = EBDStudent::where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['ebdClass', 'ebdClass.course', 'ebdClass.teachers.user'])
            ->get();

        $upcomingLessons = EBDLesson::whereIn('class_id', $studentEnrollments->pluck('class_id'))
            ->where('lesson_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['ebdClass', 'materials'])
            ->orderBy('lesson_date', 'asc')
            ->limit(5)
            ->get();

        $recentAttendance = EBDAttendance::whereHas('student', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['lesson.ebdClass'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $completedLessons = EbdStudentProgress::where('user_id', $user->id)
            ->where('status', 'completed')->count();

        $courseProgressPercent = 0;
        $courseProgressName = null;
        $firstEnrollment = $studentEnrollments->first();
        if ($firstEnrollment && $firstEnrollment->ebdClass && $firstEnrollment->ebdClass->course_id) {
            $course = $firstEnrollment->ebdClass->course;
            if ($course) {
                $courseProgressName = $course->name;
                $courseLessonIds = EBDLesson::where('course_id', $course->id)->pluck('id');
                if ($courseLessonIds->isNotEmpty()) {
                    $completedInCourse = EbdStudentProgress::where('user_id', $user->id)
                        ->whereIn('lesson_id', $courseLessonIds)
                        ->where('status', 'completed')
                        ->count();
                    $courseProgressPercent = (int) round(($completedInCourse / $courseLessonIds->count()) * 100);
                }
            }
        }

        // 2. Gamification & XP from unified system
        $currentXp = (int) ($user->xp ?? 0);
        $sumFromHistory = (int) \Modules\EBD\App\Models\EbdGamificationPoint::where('user_id', $user->id)->sum('points');
        if ($currentXp === 0 && $sumFromHistory > 0) {
            $user->xp = $sumFromHistory;
            $user->save();
            $currentXp = $sumFromHistory;
        }

        $levelService = app(\Modules\EBD\App\Services\EbdLevelService::class);
        $levelInfo = $levelService->getLevelInfo($currentXp);
        $currentLevel = $levelInfo['currentLevel'];
        $nextLevel = $levelInfo['nextLevel'];
        $xpInCurrentLevel = $levelInfo['xpInCurrentLevel'];
        $xpToNextLevel = $levelInfo['xpToNextLevel'];
        $levelTier = $currentLevel->tier ?? 'bronze';

        $achievements = \Modules\EBD\App\Models\EbdAchievement::where('is_active', true)->where('is_hidden', false)->orderBy('order')->get();
        $userAchievementIds = \Modules\EBD\App\Models\EbdUserAchievement::where('user_id', $user->id)->pluck('achievement_id')->toArray();

        // 3. Last Lesson (Continue Studying)
        $lastProgress = EbdStudentProgress::where('user_id', $user->id)
            ->where('status', 'started')
            ->latest('updated_at')
            ->with('lesson.ebdClass')
            ->first();

        $lastLesson = $lastProgress ? $lastProgress->lesson : null;

        $progressToNext = $xpToNextLevel > 0 ? round(($xpInCurrentLevel / $xpToNextLevel) * 100, 1) : 0;
        $stats = [
            'total_classes' => $studentEnrollments->count(),
            'upcoming_lessons' => $upcomingLessons->count(),
            'total_attendance' => EBDAttendance::whereHas('student', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'present')->count(),
            'completed_evaluations' => EBDEvaluation::whereHas('student', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->where('status', 'completed')->count(),
            'completed_lessons' => $completedLessons,
            'xp' => $currentXp,
            'level' => $currentLevel ? $currentLevel->level_number : 1,
            'level_name' => $currentLevel ? $currentLevel->name : 'Iniciante',
            'progress_to_next' => $progressToNext,
        ];

        return view('ebd::memberpanel.student.dashboard', compact(
            'studentEnrollments', 'upcomingLessons', 'recentAttendance', 'stats',
            'currentXp', 'currentLevel', 'nextLevel', 'xpInCurrentLevel', 'xpToNextLevel',
            'achievements', 'userAchievementIds', 'levelTier', 'lastLesson',
            'courseProgressPercent', 'courseProgressName'
        ));
    }

    /**
     * List student's classes
     */
    public function myClasses(): View
    {
        $user = auth()->user();

        $studentEnrollments = EBDStudent::where('user_id', $user->id)
            ->with(['ebdClass.teachers.user', 'ebdClass.activeStudents'])
            ->orderBy('enrollment_date', 'desc')
            ->get();

        return view('ebd::memberpanel.student.classes.my-classes', compact('studentEnrollments'));
    }

    /**
     * Display lessons for student's classes
     */
    public function lessons(Request $request): View
    {
        $user = auth()->user();

        $classIds = EBDStudent::where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('class_id');

        if ($classIds->isEmpty()) {
            $lessons = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $classes = collect();
            $lessonStates = [];
            $selectedClassId = null;

            return view('ebd::memberpanel.student.lesson.lessons', compact('lessons', 'classes', 'selectedClassId', 'lessonStates'));
        }

        $query = EBDLesson::whereIn('class_id', $classIds)
            ->with(['ebdClass', 'materials' => function ($q) {
                $q->where('is_public', true)->orderBy('order');
            }]);

        // Filtro por classe: apenas classes do aluno; se tiver só uma, usar como padrão
        $defaultClassId = $classIds->count() === 1 ? $classIds->first() : null;
        $classId = $request->filled('class_id') ? $request->class_id : $defaultClassId;
        if ($classId && in_array((int) $classId, $classIds->toArray(), true)) {
            $query->where('class_id', $classId);
        }

        // Filter by status
        if ($request->has('status') && ! empty($request->status)) {
            $query->where('status', $request->status);
        }

        $now = now();

        // Ordenar priorizando lições já disponíveis (data/hora <= agora) e, em seguida, por data/hora crescente
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
        $selectedClassId = $classId ? (int) $classId : null;

        // Build availability states per lesson (by class) for list UI
        $lessonStates = [];
        foreach ($lessons->unique('class_id') as $lesson) {
            $classLessonsOrdered = EBDLesson::where('class_id', $lesson->class_id)
                ->orderBy('lesson_date')
                ->orderBy('lesson_time')
                ->get();
            $map = $this->availabilityService->forClassLessons($user, $classLessonsOrdered);
            foreach ($map as $lid => $state) {
                $lessonStates[$lid] = $state;
            }
        }

        return view('ebd::memberpanel.student.lesson.lessons', compact('lessons', 'classes', 'selectedClassId', 'lessonStates'));
    }

    /**
     * Legacy lesson route — redirect to LMS player after validations.
     */
    public function showLesson(EBDLesson $lesson): View|RedirectResponse
    {
        $user = auth()->user();

        // Check if user is enrolled in this class
        $isEnrolled = EBDStudent::where('user_id', $user->id)
            ->where('class_id', $lesson->class_id)
            ->where('is_active', true)
            ->exists();

        if (! $isEnrolled) {
            abort(403, 'Você não está matriculado nesta classe.');
        }

        // Check if lesson is available (only allow lessons that have already started)
        $lessonTime = \Carbon\Carbon::parse($lesson->lesson_time);
        $lessonDateTime = $lesson->lesson_date->copy()->setTime($lessonTime->hour, $lessonTime->minute);
        $now = now();
        $isAvailable = $lessonDateTime->lte($now);

        if (! $isAvailable) {
            return redirect()->route('memberpanel.ebd.student.lessons')
                ->with('error', 'Esta lição ainda não está disponível. Ela estará liberada em '.$lesson->lesson_date->format('d/m/Y').' às '.$lessonTime->format('H:i').'.');
        }

        // All checks passed — redirect to LMS player
        return redirect()->route('memberpanel.ebd.student.classroom.player', $lesson->id);
    }

    /**
     * Submit evaluation answers (student completes evaluation).
     */
    public function completeEvaluation(Request $request, EBDEvaluation $evaluation): RedirectResponse
    {
        $user = auth()->user();

        $student = EBDStudent::where('user_id', $user->id)
            ->where('id', $evaluation->student_id)
            ->where('is_active', true)
            ->firstOrFail();

        if ($evaluation->student_id !== $student->id) {
            abort(403, 'Esta avaliação não é sua.');
        }

        if ($evaluation->status !== 'pending') {
            return redirect()
                ->route('memberpanel.ebd.student.lessons.show', $evaluation->lesson)
                ->with('info', 'Esta avaliação já foi enviada.');
        }

        $lesson = $evaluation->lesson;
        $questionIds = $lesson->questions()->pluck('id')->all();
        $rules = [];
        foreach ($questionIds as $qid) {
            $rules['answers.'.$qid] = ['nullable', 'string', 'max:5000'];
        }
        $request->validate($rules);

        $answers = $request->input('answers', []);
        $evaluation->update([
            'answers' => $answers,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('memberpanel.ebd.student.lessons.show', $lesson)
            ->with('success', 'Avaliação enviada com sucesso! Aguarde a correção do professor.');
    }

    /**
     * Display student progress
     */
    public function myProgress(): View
    {
        $user = auth()->user();

        // Get all attendance records
        $attendance = EBDAttendance::whereHas('student', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['lesson.ebdClass'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all evaluations
        $evaluations = EBDEvaluation::whereHas('student', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['lesson.ebdClass'])
            ->orderBy('completed_at', 'desc')
            ->get();

        // Calculate statistics
        $presentCount = $attendance->where('status', 'present')->count();
        $absentCount = $attendance->where('status', 'absent')->count();
        $lateCount = $attendance->where('status', 'late')->count();
        $excusedCount = $attendance->where('status', 'excused')->count();
        $totalAttendance = $attendance->count();

        $gradedEvaluations = $evaluations->where('status', 'graded')->filter(function ($eval) {
            return $eval->score !== null;
        });

        $stats = [
            'total_lessons' => $totalAttendance,
            'present_count' => $presentCount,
            'absent_count' => $absentCount,
            'late_count' => $lateCount,
            'excused_count' => $excusedCount,
            'attendance_rate' => $totalAttendance > 0
                ? round(($presentCount / $totalAttendance) * 100, 2)
                : 0,
            'average_score' => $gradedEvaluations->count() > 0
                ? round($gradedEvaluations->avg('score'), 2)
                : 0,
            'completed_evaluations' => $evaluations->whereIn('status', ['completed', 'graded'])->count(),
            'graded_evaluations' => $gradedEvaluations->count(),
        ];

        return view('ebd::memberpanel.student.classes.my-progress', compact('attendance', 'evaluations', 'stats'));
    }
}
