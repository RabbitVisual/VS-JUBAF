<?php

namespace Modules\EBD\App\Services;

use App\Models\User;
use Modules\EBD\App\Models\EBDClass;
use Modules\EBD\App\Models\EBDStudent;

class EnrollmentService
{
    public function enroll(User $user, EBDClass $class, array $options = []): EBDStudent
    {
        $enrollment = EBDStudent::firstOrNew([
            'user_id' => $user->id,
            'class_id' => $class->id,
        ]);

        $enrollment->enrollment_date = $options['enrollment_date'] ?? $enrollment->enrollment_date ?? now();
        $enrollment->is_active = $options['is_active'] ?? true;
        if (array_key_exists('notes', $options)) {
            $enrollment->notes = $options['notes'];
        }
        $enrollment->save();

        return $enrollment;
    }

    public function unenroll(EBDStudent $enrollment): bool
    {
        $enrollment->is_active = false;
        $enrollment->graduation_date = $enrollment->graduation_date ?? now();

        return $enrollment->save();
    }

    public function isEnrolled(User $user, EBDClass $class): bool
    {
        return EBDStudent::where('user_id', $user->id)
            ->where('class_id', $class->id)
            ->where('is_active', true)
            ->exists();
    }
}
