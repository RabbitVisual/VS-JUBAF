<?php

namespace Modules\Gamification\App\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Modules\Gamification\App\Services\GamificationService;

class CheckBadgesCommand extends Command
{
    protected $signature = 'gamification:check-badges
                            {--user= : ID do usuário específico para verificar}
                            {--all : Verificar todos os usuários ativos}';

    protected $description = 'Verifica e atribui badges automaticamente aos usuários';

    public function handle(GamificationService $gamificationService): int
    {
        $this->info('Verificando badges...');

        if ($this->option('user')) {
            $user = User::find($this->option('user'));
            if (! $user) {
                $this->error("Usuário com ID {$this->option('user')} não encontrado.");
                return 1;
            }
            $this->info("Verificando badges para: {$user->name}");
            $gamificationService->checkAndAwardBadges($user);
            $this->info("Verificação concluída para {$user->name}!");
            return 0;
        }

        if ($this->option('all')) {
            $users = User::where('is_active', true)->get();
            $total = $users->count();
            $this->info("Verificando badges para {$total} usuários ativos...");
            $bar = $this->output->createProgressBar($total);
            $bar->start();
            $awarded = 0;
            foreach ($users as $user) {
                $beforeCount = $user->badges()->count();
                $gamificationService->checkAndAwardBadges($user);
                $afterCount = $user->badges()->count();
                if ($afterCount > $beforeCount) {
                    $awarded += ($afterCount - $beforeCount);
                }
                $bar->advance();
            }
            $bar->finish();
            $this->newLine();
            $this->info('Verificação concluída!');
            $this->info("Total de badges atribuídos: {$awarded}");
            return 0;
        }

        $this->error('Por favor, especifique --user=ID ou --all');
        $this->info('Exemplos:');
        $this->info('  php artisan gamification:check-badges --user=1');
        $this->info('  php artisan gamification:check-badges --all');
        return 1;
    }
}
