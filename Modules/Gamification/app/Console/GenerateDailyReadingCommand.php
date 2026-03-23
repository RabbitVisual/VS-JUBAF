<?php

declare(strict_types=1);

namespace Modules\Gamification\App\Console;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Modules\Gamification\App\Services\DailyReadingService;

class GenerateDailyReadingCommand extends Command
{
    protected $signature = 'gamification:generate-daily-reading
                            {--days=1 : Número de dias a gerar (hoje + dias-1 à frente)}';

    protected $description = 'Gera a recomendação de leitura do dia e persiste em gamification_daily_readings';

    public function handle(DailyReadingService $dailyReadingService): int
    {
        $days = (int) $this->option('days');
        if ($days < 1) {
            $days = 1;
        }

        $generated = 0;
        for ($i = 0; $i < $days; $i++) {
            $targetDate = Carbon::today()->addDays($i);
            $result = $dailyReadingService->generateAndStoreForDate($targetDate);
            if ($result) {
                $generated++;
                $this->info("Gerado: {$targetDate->format('Y-m-d')} — {$result['title']}");
            }
        }

        $this->info("Concluído. {$generated} recomendação(ões) gerada(s).");
        return 0;
    }
}
