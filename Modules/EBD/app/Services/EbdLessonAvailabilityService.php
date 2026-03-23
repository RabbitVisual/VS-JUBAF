<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EbdStudentProgress;

class EbdLessonAvailabilityService
{
    /**
     * Get availability state for a single lesson and user.
     * Uses course lessons (by order) when course_id is set, else class lessons by date/time.
     */
    public function forLesson(User $user, EBDLesson $lesson): array
    {
        if ($lesson->course_id) {
            $classLessons = EBDLesson::where('course_id', $lesson->course_id)->orderBy('order')->orderBy('lesson_date')->get();
        } else {
            $classLessons = EBDLesson::where('class_id', $lesson->class_id)
                ->orderBy('lesson_date')
                ->orderBy('lesson_time')
                ->get();
        }

        $map = $this->forClassLessons($user, $classLessons);

        return $map[$lesson->id] ?? $this->defaultState();
    }

    /**
     * Get availability states for an ordered collection of lessons from the same class.
     * Returns a map lesson_id => state array.
     */
    public function forClassLessons(User $user, Collection $classLessonsOrdered): array
    {
        $result = [];
        $previousCompleted = true; // first lesson has no previous

        foreach ($classLessonsOrdered as $lesson) {
            $lessonDateTime = $this->lessonDateTime($lesson);
            $now = now();
            $availableByDate = $lessonDateTime->lte($now);
            $unlockedByProgression = $previousCompleted;
            $isCompleted = $this->isLessonCompletedByUser($lesson->id, $user->id);

            $result[$lesson->id] = [
                'available_by_date' => $availableByDate,
                'unlocked_by_progression' => $unlockedByProgression,
                'can_access' => $availableByDate && $unlockedByProgression,
                'is_completed' => $isCompleted,
                'is_pending' => $availableByDate && ! $isCompleted,
                'is_current' => false, // caller can set for the active lesson
            ];

            $previousCompleted = $isCompleted;
        }

        return $result;
    }

    /**
     * Check if user can access the lesson (date passed and previous lesson completed).
     */
    public function canAccessLesson(User $user, EBDLesson $lesson): bool
    {
        $state = $this->forLesson($user, $lesson);

        return $state['can_access'];
    }

    /**
     * Get the previous lesson (by course order or class date/time), or null.
     */
    public function getPreviousLesson(EBDLesson $lesson): ?EBDLesson
    {
        if ($lesson->course_id) {
            return EBDLesson::where('course_id', $lesson->course_id)
                ->whereRaw('COALESCE(`order`, 999) < ?', [$lesson->order ?? 999])
                ->orderByDesc('order')
                ->first();
        }

        return EBDLesson::where('class_id', $lesson->class_id)
            ->orderBy('lesson_date')
            ->orderBy('lesson_time')
            ->where(function ($q) use ($lesson) {
                $dt = $this->lessonDateTime($lesson);
                $q->where('lesson_date', '<', $dt->toDateString())
                    ->orWhere(function ($q2) use ($lesson, $dt) {
                        $q2->where('lesson_date', $dt->toDateString())
                            ->where('lesson_time', '<', $lesson->lesson_time);
                    });
            })
            ->orderByDesc('lesson_date')
            ->orderByDesc('lesson_time')
            ->first();
    }

    private function lessonDateTime(EBDLesson $lesson): \Carbon\Carbon
    {
        $lessonTime = \Carbon\Carbon::parse($lesson->lesson_time);

        return $lesson->lesson_date->copy()->setTime($lessonTime->hour, $lessonTime->minute);
    }

    private function isLessonCompletedByUser(int $lessonId, int $userId): bool
    {
        return EbdStudentProgress::where('lesson_id', $lessonId)
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->exists();
    }

    private function defaultState(): array
    {
        return [
            'available_by_date' => false,
            'unlocked_by_progression' => false,
            'can_access' => false,
            'is_completed' => false,
            'is_pending' => false,
            'is_current' => false,
        ];
    }
}
