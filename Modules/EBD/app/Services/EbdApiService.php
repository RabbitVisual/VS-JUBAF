<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\EBD\App\Models\EBDAttendance;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDEvaluation;
use Modules\EBD\App\Models\EbdGamificationPoint;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EbdStudentProgress;
use Modules\EBD\App\Models\EBDTeacher;

class EbdApiService
{
    public function __construct(
        private GamificationService $gamification
    ) {}

    public function getDashboardStats(): array
    {
        return [
            'total_classes' => EBDClass::where('is_active', true)->count(),
            'total_students' => EBDStudent::where('is_active', true)->count(),
            'total_teachers' => EBDTeacher::where('is_active', true)->count(),
            'total_lessons' => EBDLesson::count(),
            'upcoming_lessons' => EBDLesson::where('lesson_date', '>=', now())
                ->where('status', '!=', 'cancelled')->count(),
            'attendance_rate' => $this->getAttendanceRate(),
        ];
    }

    public function getClasses(bool $activeOnly = true): Collection
    {
        $query = EBDClass::withCount(['activeStudents', 'lessons', 'activeTeachers']);
        if ($activeOnly) {
            $query->where('is_active', true);
        }

        return $query->orderBy('order')->orderBy('name')->get();
    }

    public function getClassDetail(int $classId): ?EBDClass
    {
        return EBDClass::with([
            'activeStudents.user',
            'activeTeachers.user',
            'lessons' => fn ($q) => $q->orderByDesc('lesson_date')->limit(10),
        ])->withCount(['activeStudents', 'lessons'])->find($classId);
    }

    public function getLessons(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = EBDLesson::with(['ebdClass', 'creator'])
            ->withCount(['attendance', 'materials', 'questions', 'evaluations']);

        if (! empty($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['from'])) {
            $query->where('lesson_date', '>=', $filters['from']);
        }
        if (! empty($filters['to'])) {
            $query->where('lesson_date', '<=', $filters['to']);
        }

        return $query->orderByDesc('lesson_date')->paginate($filters['per_page'] ?? 15);
    }

    public function getLessonDetail(int $lessonId): ?EBDLesson
    {
        return EBDLesson::with([
            'ebdClass',
            'creator',
            'materials' => fn ($q) => $q->orderBy('order'),
            'questions' => fn ($q) => $q->orderBy('order'),
            'media',
            'attendance.student.user',
            'evaluations.student.user',
        ])->withCount(['attendance', 'materials', 'questions'])->find($lessonId);
    }

    public function getStudentDashboard(User $user): array
    {
        $enrollments = EBDStudent::where('user_id', $user->id)
            ->where('is_active', true)
            ->with(['ebdClass.activeTeachers.user'])
            ->get();

        $classIds = $enrollments->pluck('class_id');
        $studentIds = $enrollments->pluck('id');

        $upcomingLessons = EBDLesson::whereIn('class_id', $classIds)
            ->where('lesson_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with('ebdClass')
            ->orderBy('lesson_date')
            ->limit(5)
            ->get();

        $attendanceCount = EBDAttendance::whereIn('student_id', $studentIds)
            ->where('status', 'present')->count();

        $totalAttendance = EBDAttendance::whereIn('student_id', $studentIds)->count();

        $progress = EbdStudentProgress::where('user_id', $user->id)
            ->where('status', 'completed')->count();

        $totalXp = (int) EbdGamificationPoint::where('user_id', $user->id)->sum('points');

        return [
            'enrollments' => $enrollments,
            'upcoming_lessons' => $upcomingLessons,
            'xp' => $totalXp,
            'level' => (int) ($user->level ?? 1),
            'attendance_rate' => $totalAttendance > 0
                ? round(($attendanceCount / $totalAttendance) * 100, 1) : 0,
            'completed_lessons' => $progress,
            'stats' => [
                'classes' => $enrollments->count(),
                'present' => $attendanceCount,
                'total_attendance' => $totalAttendance,
            ],
        ];
    }

    public function getTeacherDashboard(User $user): array
    {
        $teacherRecords = EBDTeacher::where('user_id', $user->id)
            ->where('is_active', true)
            ->with('ebdClass')
            ->get();

        $classIds = $teacherRecords->pluck('class_id');

        $pendingEvaluations = EBDEvaluation::whereHas('lesson', fn ($q) => $q->whereIn('class_id', $classIds))
            ->whereIn('status', [EBDEvaluation::STATUS_COMPLETED, EBDEvaluation::STATUS_PENDING])
            ->count();

        $upcomingLessons = EBDLesson::whereIn('class_id', $classIds)
            ->where('lesson_date', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with('ebdClass')
            ->orderBy('lesson_date')
            ->limit(5)
            ->get();

        $totalStudents = EBDStudent::whereIn('class_id', $classIds)
            ->where('is_active', true)->count();

        return [
            'classes' => $teacherRecords,
            'upcoming_lessons' => $upcomingLessons,
            'pending_evaluations' => $pendingEvaluations,
            'total_students' => $totalStudents,
        ];
    }

    public function markLessonComplete(User $user, int $lessonId): EbdStudentProgress
    {
        $progress = EbdStudentProgress::updateOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['status' => 'completed', 'completed_at' => now()]
        );

        $alreadyRewarded = EbdGamificationPoint::where('user_id', $user->id)
            ->where('source_type', 'lesson')
            ->where('description', 'like', "%Lesson #{$lessonId}%")
            ->exists();

        if (! $alreadyRewarded) {
            $this->gamification->addXp($user, 50, 'lesson', "Lesson #{$lessonId} completed");
        }

        return $progress;
    }

    public function getLeaderboard(string $period = 'all', int $limit = 20): Collection
    {
        $query = EbdGamificationPoint::select('user_id')
            ->selectRaw('SUM(points) as total_xp')
            ->groupBy('user_id')
            ->orderByDesc('total_xp')
            ->limit($limit);

        if ($period === 'weekly') {
            $query->where('created_at', '>=', now()->startOfWeek());
        } elseif ($period === 'monthly') {
            $query->where('created_at', '>=', now()->startOfMonth());
        }

        return $query->get()->map(function ($row) {
            $user = User::find($row->user_id);

            return [
                'user_id' => $row->user_id,
                'name' => $user?->name ?? 'Usuário',
                'total_xp' => (int) $row->total_xp,
                'level' => (int) ($user?->level ?? 1),
            ];
        });
    }

    private function getAttendanceRate(): float
    {
        $total = EBDAttendance::whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', now()->subDays(30)))->count();
        $present = EBDAttendance::whereHas('lesson', fn ($q) => $q->where('lesson_date', '>=', now()->subDays(30)))
            ->where('status', 'present')->count();

        return $total > 0 ? round(($present / $total) * 100, 1) : 0;
    }
}
