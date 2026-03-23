<?php

namespace App\Observers;

use App\Models\User;
use Modules\Gamification\App\Services\GamificationService;

class UserObserver
{
    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Verifica se algum campo relevante para gamificação foi alterado
        $relevantFields = [
            'is_baptized',
            'time_congregating_months',
            'first_name',
            'last_name',
            'cpf',
            'date_of_birth',
            'gender',
            'marital_status',
            'phone',
            'cellphone',
            'address',
            'city',
            'state',
            'zip_code',
            'profession',
            'education_level',
            'workplace',
            'emergency_contact_name',
            'emergency_contact_phone',
        ];

        $wasChanged = false;
        foreach ($relevantFields as $field) {
            if ($user->wasChanged($field)) {
                $wasChanged = true;
                break;
            }
        }

        // Se algum campo relevante foi alterado, verifica badges
        if ($wasChanged) {
            $gamificationService = app(\Modules\Gamification\App\Services\GamificationService::class);
            $gamificationService->checkAndAwardBadges($user);
        }
    }
}
