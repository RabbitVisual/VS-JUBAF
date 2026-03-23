<?php

namespace Modules\EBD\App\Http\Controllers\Pastoral;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\EBD\App\Models\EBDAttendance;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDEvaluation;
use Modules\EBD\App\Models\EbdGamificationPoint;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EBDTeacher;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = $this->buildStats();
        $attendanceRate = $this->calculateAttendanceRate();
        $recentLessons = EBDLesson::with(['ebdClass', 'creator'])
            ->orderBy('lesson_date', 'desc')
            ->limit(5)
            ->get();
        $upcomingLessons = EBDLesson::with(['ebdClass', 'creator'])
            ->where('lesson_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('lesson_date', 'asc')
            ->limit(5)
            ->get();
        $classesByAgeGroup = EBDClass::select('age_group', DB::raw('count(*) as total'))
            ->where('is_active', true)
            ->groupBy('age_group')
            ->get()
            ->pluck('total', 'age_group');
        $classPerformance = $this->getClassPerformance();
        $studentsWithAbsences = $this->getStudentsWithAbsences(2, 30);

        return view('ebd::pastoralpanel.dashboard', compact(
            'stats',
            'attendanceRate',
            'recentLessons',
            'upcomingLessons',
            'classesByAgeGroup',
            'classPerformance',
            'studentsWithAbsences'
        ));
    }

    private function buildStats(): array
    {
        return [
            'total_classes' => EBDClass::count(),
            'active_classes' => EBDClass::where('is_active', true)->count(),
            'total_students' => EBDStudent::where('is_active', true)->count(),
            'total_teachers' => EBDTeacher::where('is_active', true)->count(),
            'total_lessons' => EBDLesson::count(),
            'upcoming_lessons' => EBDLesson::where('lesson_date', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->count(),
            'completed_lessons' => EBDLesson::where('status', 'completed')->count(),
            'total_evaluations' => EBDEvaluation::count(),
            'graded_evaluations' => EBDEvaluation::where('status', EBDEvaluation::STATUS_GRADED)->count(),
            'avg_score' => round((float) EBDEvaluation::where('status', EBDEvaluation::STATUS_GRADED)->avg('score'), 1),
            'total_xp_distributed' => (int) EbdGamificationPoint::sum('points'),
        ];
    }

    private function calculateAttendanceRate(): float
    {
        $totalAttendance = EBDAttendance::whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', now()->subDays(30)))->count();
        $presentCount = EBDAttendance::whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', now()->subDays(30)))
            ->where('status', 'present')->count();

        return $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;
    }

    private function getClassPerformance(): \Illuminate\Support\Collection
    {
        return EBDClass::where('is_active', true)->get()->map(function ($class) {
            $totalPossible = EBDAttendance::whereHas('lesson', fn ($q) => $q->where('class_id', $class->id)->where('lesson_date', '>=', now()->subDays(30)))->count();
            $present = EBDAttendance::whereHas('lesson', fn ($q) => $q->where('class_id', $class->id)->where('lesson_date', '>=', now()->subDays(30)))
                ->where('status', 'present')->count();

            return [
                'name' => $class->name,
                'rate' => $totalPossible > 0 ? round(($present / $totalPossible) * 100, 1) : 0,
                'total_students' => $class->activeStudents()->count(),
            ];
        });
    }

    private function getStudentsWithAbsences(int $minAbsences, int $days): \Illuminate\Support\Collection
    {
        $studentIds = EBDAttendance::where('status', 'absent')
            ->whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', now()->subDays($days)))
            ->select('student_id')
            ->groupBy('student_id')
            ->havingRaw('COUNT(*) >= ?', [$minAbsences])
            ->pluck('student_id');

        return EBDStudent::whereIn('id', $studentIds)
            ->with(['user', 'ebdClass'])
            ->get()
            ->map(function ($student) use ($days) {
                $absences = EBDAttendance::where('student_id', $student->id)
                    ->where('status', 'absent')
                    ->whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', now()->subDays($days)))
                    ->count();

                return ['student' => $student, 'absences' => $absences];
            })
            ->sortByDesc('absences')
            ->values();
    }
}
