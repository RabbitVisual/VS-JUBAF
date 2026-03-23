<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Modules\EBD\App\Models\EBDLesson;
use Modules\EBD\App\Models\EBDStudent;
use Modules\EBD\App\Models\EBDAttendance;

class AttendanceService
{
    public function record(
        EBDLesson $lesson,
        EBDStudent $student,
        string $status,
        ?User $registeredBy = null,
        array $extra = []
    ): EBDAttendance {
        return EBDAttendance::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'student_id' => $student->id,
            ],
            array_merge([
                'status' => $status,
                'registered_by' => $registeredBy?->id,
            ], $extra)
        );
    }

    /**
     * @param  array<int, string>  $studentIdToStatus  [student_id => status]
     */
    public function bulkRecord(EBDLesson $lesson, array $studentIdToStatus, ?User $registeredBy = null): void
    {
        foreach ($studentIdToStatus as $studentId => $status) {
            $student = EBDStudent::find($studentId);
            if ($student) {
                $this->record($lesson, $student, $status, $registeredBy);
            }
        }
    }

    /**
     * @param  array<int, array{student_id: int, status: string, arrival_time?: string, notes?: string}>  $rows
     */
    public function bulkRecordFromRows(EBDLesson $lesson, array $rows, ?User $registeredBy = null): void
    {
        EBDAttendance::where('lesson_id', $lesson->id)->delete();

        foreach ($rows as $row) {
            $student = EBDStudent::find($row['student_id'] ?? null);
            if (! $student) {
                continue;
            }
            $extra = [];
            if (! empty($row['arrival_time'])) {
                $extra['arrival_time'] = $lesson->lesson_date->format('Y-m-d').' '.$row['arrival_time'].':00';
            }
            if (array_key_exists('notes', $row)) {
                $extra['notes'] = $row['notes'];
            }
            $this->record($lesson, $student, $row['status'], $registeredBy, $extra);
        }
    }
}
