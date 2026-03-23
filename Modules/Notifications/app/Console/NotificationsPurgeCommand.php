<?php

namespace Modules\Notifications\App\Console;

use Illuminate\Console\Command;
use Modules\Notifications\App\Models\UserNotification;

class NotificationsPurgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:purge
                            {--days=90 : Remove notificações lidas mais antigas que N dias}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove notificações lidas com mais de 90 dias para manter o banco leve.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $days = max(1, min(365, $days));
        $cutoff = now()->subDays($days);

        $deleted = UserNotification::where('is_read', true)
            ->whereNotNull('read_at')
            ->where('read_at', '<', $cutoff)
            ->delete();

        $this->info("Removidas {$deleted} notificação(ões) lidas com mais de {$days} dias.");
        return self::SUCCESS;
    }
}
