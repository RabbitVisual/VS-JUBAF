<?php

namespace App\Providers;

use App\Helpers\SettingsHelper;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Log1x\LaravelWebfonts\Webfonts;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Preload apenas fontes críticas (first paint). Nomes alinhados a config/webfonts.php;
        // se o manifest (public/build/manifest.json) tiver chaves .woff2 com hashes, atualize
        // config('webfonts.only') com os basenames reais após `npm run build`.
        Webfonts::only(config('webfonts.only', [
            'inter-v20-latin-regular',
            'inter-v20-latin-600',
        ]));

        // Registra o Observer do User
        User::observe(UserObserver::class);

        // Ciclo de vida global: aplica configurações do banco (timezone, locale, formatos de data/hora,
        // mail, recaptcha, 2FA, etc.) no início de cada requisição para consistência em logs, e-mails e UI.
        try {
            SettingsHelper::applyGlobalSettings();
        } catch (\Exception $e) {
            $isTableMissing = str_contains($e->getMessage(), "doesn't exist");
            if (! app()->runningInConsole() || ! $isTableMissing) {
                \Illuminate\Support\Facades\Log::warning('SettingsHelper::applyGlobalSettings failed', [
                    'message' => $e->getMessage(),
                ]);
            }
        }
        // Fix for Laravel db:show on older MariaDB (XAMPP/MariaDB 10.4)
        // We only apply this if we are running the 'db:show' command specifically
        if (app()->runningInConsole() && isset($_SERVER['argv']) && in_array('db:show', $_SERVER['argv'])) {
            try {
                $connection = \Illuminate\Support\Facades\DB::connection();
                if ($connection->getDriverName() === 'mysql') {
                    $connection->setQueryGrammar(new class($connection) extends \Illuminate\Database\Query\Grammars\MySqlGrammar {
                        public function compileThreadCount() {
                            return 'select variable_value as `Value` from information_schema.GLOBAL_STATUS where variable_name = \'threads_connected\'';
                        }
                    });
                }
            } catch (\Exception $e) {}
        }
    }
}
