<?php

namespace Modules\Worship\App\Services;

use App\Models\User;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyEnrollment;
use Modules\Worship\App\Models\AcademyLesson;
use Modules\Worship\App\Models\AcademyProgress;

/**
 * Lógica de progressão e conclusão da Worship Academy.
 * Única fonte para: quem pode acessar qual lição, percentual de curso e marcação de conclusão.
 */
class WorshipCourseService
{
    /**
     * Verifica se o usuário pode acessar a lição (primeira ou anterior concluída).
     */
    public function canAccessLesson(User $user, AcademyLesson $lesson): bool
    {
        $module = $lesson->module;
        if (! $module) {
            return false;
        }

        $course = $module->course;
        $allLessons = $course->modules()->orderBy('order')->with(['lessons' => fn ($q) => $q->orderBy('order')])->get()
            ->pluck('lessons')->flatten()->pluck('id')->values();

        $index = $allLessons->search($lesson->id);
        if ($index === false) {
            return false;
        }
        if ($index === 0) {
            return true;
        }

        $completedIds = AcademyProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $allLessons->toArray())
            ->pluck('lesson_id')
            ->flip()
            ->all();

        $previousId = $allLessons->get($index - 1);

        return isset($completedIds[$previousId]);
    }

    /**
     * Retorna progresso do usuário no curso: enrollment, percentual e IDs das lições concluídas.
     *
     * @return array{enrollment: AcademyEnrollment, progress_percent: int, completed_lesson_ids: array<int>}
     */
    public function getProgressForUser(User $user, AcademyCourse $course): array
    {
        $enrollment = AcademyEnrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['progress_percent' => 0]
        );

        $lessonIds = $course->lessons()->pluck('worship_academy_lessons.id');
        $completedLessonIds = AcademyProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->pluck('lesson_id')
            ->toArray();

        $total = $lessonIds->count();
        $progressPercent = $total > 0 ? (int) round((count($completedLessonIds) / $total) * 100) : 0;

        if ($enrollment->progress_percent !== $progressPercent) {
            $enrollment->update([
                'progress_percent' => $progressPercent,
                'completed_at' => $progressPercent === 100 ? now() : $enrollment->completed_at,
            ]);
        }

        return [
            'enrollment' => $enrollment->fresh(),
            'progress_percent' => $progressPercent,
            'completed_lesson_ids' => $completedLessonIds,
        ];
    }

    /**
     * Marca lição como concluída, atualiza enrollment e retorna dados para resposta/XP.
     *
     * @return array{progress_percent: int, was_first_completion: bool}
     */
    public function markLessonComplete(int $lessonId, User $user): array
    {
        $lesson = AcademyLesson::with('module.course')->findOrFail($lessonId);
        $course = $lesson->module->course;

        $progress = AcademyProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lessonId],
            ['completed_at' => now(), 'score' => 10]
        );

        $lessonIds = $course->lessons()->pluck('worship_academy_lessons.id');
        $completedCount = AcademyProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->count();
        $total = $lessonIds->count();
        $progressPercent = $total > 0 ? (int) round(($completedCount / $total) * 100) : 0;

        $enrollment = AcademyEnrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            $enrollment->update([
                'progress_percent' => $progressPercent,
                'completed_at' => $progressPercent === 100 ? now() : null,
            ]);
        }

        return [
            'progress_percent' => $progressPercent,
            'was_first_completion' => $progress->wasRecentlyCreated,
        ];
    }
}
