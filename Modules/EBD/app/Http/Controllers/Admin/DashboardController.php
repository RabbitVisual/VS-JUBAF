<?php

namespace Modules\EBD\App\Http\Controllers\Admin;

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

        $attendanceRate = $this->calculateAttendanceRate();
        $monthlyAttendanceTrend = $this->calculateMonthlyAttendanceTrend();
        $topClasses = $this->getTopClasses();
        $classPerformance = $this->getClassPerformance();
        $gamificationStats = $this->getGamificationStats();
        $lessonCompletionRate = $this->getLessonCompletionRate();
        $studentsWithAbsences = $this->getStudentsWithAbsences(2, 30);
        $classAverageScores = $this->getClassAverageScores();

        return view('ebd::admin.dashboard', compact(
            'stats',
            'recentLessons',
            'upcomingLessons',
            'classesByAgeGroup',
            'attendanceRate',
            'monthlyAttendanceTrend',
            'topClasses',
            'classPerformance',
            'gamificationStats',
            'lessonCompletionRate',
            'studentsWithAbsences',
            'classAverageScores'
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
            'total_attendance' => EBDAttendance::where('status', 'present')->count(),
            'total_evaluations' => EBDEvaluation::count(),
            'graded_evaluations' => EBDEvaluation::where('status', EBDEvaluation::STATUS_GRADED)->count(),
            'avg_score' => round((float) EBDEvaluation::where('status', EBDEvaluation::STATUS_GRADED)->avg('score'), 1),
            'total_xp_distributed' => (int) EbdGamificationPoint::sum('points'),
        ];
    }

    private function getTopClasses(): \Illuminate\Support\Collection
    {
        return EBDClass::where('is_active', true)
            ->withCount([
                'activeStudents',
                'lessons',
                'lessons as recent_attendance_count' => function ($query) {
                    $query->whereHas('attendance', fn ($q) => $q->where('status', 'present'))
                        ->where('lesson_date', '>=', now()->subDays(30));
                },
            ])
            ->orderByDesc('recent_attendance_count')
            ->limit(5)
            ->get();
    }

    private function getClassPerformance(): \Illuminate\Support\Collection
    {
        return EBDClass::where('is_active', true)->get()->map(function ($class) {
            $totalPossible = EBDAttendance::whereHas('lesson', function ($q) use ($class) {
                $q->where('class_id', $class->id)->where('lesson_date', '>=', now()->subDays(30));
            })->count();

            $present = EBDAttendance::whereHas('lesson', function ($q) use ($class) {
                $q->where('class_id', $class->id)->where('lesson_date', '>=', now()->subDays(30));
            })->where('status', 'present')->count();

            return [
                'name' => $class->name,
                'rate' => $totalPossible > 0 ? round(($present / $totalPossible) * 100, 1) : 0,
                'total_students' => $class->activeStudents()->count(),
            ];
        });
    }

    /**
     * Students with at least $minAbsences absences in the last $days days.
     */
    private function getStudentsWithAbsences(int $minAbsences = 2, int $days = 30): \Illuminate\Support\Collection
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

                return [
                    'student' => $student,
                    'absences' => $absences,
                ];
            })
            ->sortByDesc('absences')
            ->values();
    }

    /**
     * Average evaluation score per class (graded only).
     */
    private function getClassAverageScores(): \Illuminate\Support\Collection
    {
        $evaluations = EBDEvaluation::where('status', EBDEvaluation::STATUS_GRADED)
            ->whereNotNull('score')
            ->with('lesson.ebdClass')
            ->get();

        return $evaluations->groupBy(fn ($e) => $e->lesson->ebdClass->id ?? 0)
            ->map(function ($items, $classId) {
                if ($classId === 0) {
                    return null;
                }
                $class = $items->first()->lesson->ebdClass;
                $avg = $items->avg('score');

                return [
                    'class_name' => $class->name,
                    'avg_score' => round((float) $avg, 1),
                    'count' => $items->count(),
                ];
            })
            ->filter()
            ->values()
            ->sortByDesc('avg_score');
    }

    private function getGamificationStats(): array
    {
        $thisMonth = now()->startOfMonth();

        return [
            'xp_this_month' => (int) EbdGamificationPoint::where('created_at', '>=', $thisMonth)->sum('points'),
            'active_players' => EbdGamificationPoint::where('created_at', '>=', $thisMonth)
                ->distinct('user_id')->count('user_id'),
            'top_earners' => EbdGamificationPoint::select('user_id', DB::raw('SUM(points) as total_xp'))
                ->where('created_at', '>=', $thisMonth)
                ->groupBy('user_id')
                ->orderByDesc('total_xp')
                ->limit(5)
                ->with('user')
                ->get(),
        ];
    }

    private function getLessonCompletionRate(): float
    {
        $total = EBDLesson::where('lesson_date', '<', now())->count();
        $completed = EBDLesson::where('status', 'completed')->where('lesson_date', '<', now())->count();

        return $total > 0 ? round(($completed / $total) * 100, 1) : 0;
    }

    private function calculateMonthlyAttendanceTrend(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M');

            $total = EBDAttendance::whereHas('lesson', function ($query) use ($date) {
                $query->whereYear('lesson_date', $date->year)
                    ->whereMonth('lesson_date', $date->month);
            })->count();

            $present = EBDAttendance::whereHas('lesson', function ($query) use ($date) {
                $query->whereYear('lesson_date', $date->year)
                    ->whereMonth('lesson_date', $date->month);
            })->where('status', 'present')->count();

            $months[] = [
                'month' => $monthName,
                'total' => $total,
                'present' => $present,
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        }

        return $months;
    }

    private function calculateAttendanceRate(): float
    {
        $totalAttendance = EBDAttendance::whereHas('lesson', function ($query) {
            $query->where('lesson_date', '>=', now()->subDays(30));
        })->count();

        $presentCount = EBDAttendance::whereHas('lesson', function ($query) {
            $query->where('lesson_date', '>=', now()->subDays(30));
        })->where('status', 'present')->count();

        return $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 2) : 0;
    }
}
