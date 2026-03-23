<?php

namespace Modules\Ministries\App\Console;

use Illuminate\Console\Command;
use Modules\Ministries\App\Models\Ministry;
use Modules\Ministries\App\Models\MinistryReport;

class RemindMinistryReportCommand extends Command
{
    protected $signature = 'ministries:remind-reports';

    protected $description = 'Envia lembretes a líderes de ministério para envio do relatório mensal (3 dias antes do fim do mês).';

    public function handle(): int
    {
        $today = now();
        $remindDate = $today->copy()->endOfMonth()->subDays(2);
        if ($today->format('Y-m-d') !== $remindDate->format('Y-m-d')) {
            return self::SUCCESS;
        }

        $year = (int) $today->format('Y');
        $month = (int) $today->format('n');
        $ministries = Ministry::where(function ($q) {
            $q->whereNotNull('leader_id')->orWhereNotNull('co_leader_id');
        })->with(['leader', 'coLeader'])->get();

        $sent = 0;
        foreach ($ministries as $ministry) {
            $hasReport = MinistryReport::where('ministry_id', $ministry->id)
                ->where('report_year', $year)
                ->where('report_month', $month)
                ->where('status', MinistryReport::STATUS_SUBMITTED)
                ->exists();

            if ($hasReport) {
                continue;
            }

            $users = collect([$ministry->leader, $ministry->coLeader])->filter()->unique('id');
            if ($users->isEmpty()) {
                continue;
            }

            if (! class_exists(\Modules\Notifications\App\Services\InAppNotificationService::class)) {
                continue;
            }

            $url = route('memberpanel.ministries.show', $ministry);
            app(\Modules\Notifications\App\Services\InAppNotificationService::class)->sendToUsers(
                $users,
                'Relatório mensal do ministério',
                "Lembrete: envie o relatório de {$today->translatedFormat('F/Y')} do ministério \"{$ministry->name}\" até o fim do mês.",
                [
                    'type' => 'warning',
                    'priority' => 'normal',
                    'action_url' => $url,
                    'action_text' => 'Abrir ministério',
                ]
            );
            $sent++;
        }

        if ($sent > 0) {
            $this->info("Enviados {$sent} lembretes de relatório mensal.");
        }

        return self::SUCCESS;
    }
}
