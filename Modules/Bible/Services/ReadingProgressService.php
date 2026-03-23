<?php

namespace Modules\Bible\Services;

use App\Models\User;
use Carbon\Carbon;
use Modules\Bible\App\Models\BiblePlanSubscription;

class ReadingProgressService
{
    /**
     * Calcula o progresso detalhado de uma inscrição.
     */
    public function getProgress(User $user, $planId)
    {
        $subscription = BiblePlanSubscription::where('user_id', $user->id)
            ->where('plan_id', $planId)
            ->first();

        if (! $subscription) {
            return null;
        }

        $totalDays = $subscription->plan->days()->count();
        $completedDays = $subscription->progress()->count(); // Assumindo relação hasMany 'progress'

        // Cálculo de atraso
        $startDate = Carbon::parse($subscription->start_date);
        $daysSinceStart = $startDate->diffInDays(now()) + 1; // +1 porque dia 1 conta

        // Se começou hoje, daysSinceStart = 1. Se completou 0, está atrasado 1 (tecnicamente) se já for noite, mas vamos simplificar.
        // Atraso = Dias que deveriam ter sido lidos - Dias lidos
        $delay = max(0, $daysSinceStart - $completedDays);
        if ($delay > 0 && $completedDays >= $totalDays) {
            $delay = 0;
        } // Se terminou, não tem atraso

        // Próximo dia a ler
        $nextDayNumber = $completedDays + 1;
        $nextDay = $subscription->plan->days()->where('day_number', $nextDayNumber)->first();

        return [
            'subscription' => $subscription,
            'percentage' => $totalDays > 0 ? round(($completedDays / $totalDays) * 100) : 0,
            'completed_days' => $completedDays,
            'total_days' => $totalDays,
            'delay_days' => $delay,
            'is_behind' => $delay > 2, // Considera atrasado se > 2 dias
            'next_day' => $nextDay,
            'days_remaining' => $totalDays - $completedDays,
        ];
    }
}
