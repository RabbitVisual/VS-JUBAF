<?php

namespace Modules\Gamification\App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Modules\Gamification\App\Services\CbavBotRuleEngineService;
use Modules\Gamification\App\Services\DailyReadingService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class GamificationServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Gamification';

    protected string $nameLower = 'gamification';

    public function boot(): void
    {
        $this->registerViewComposers();
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerViewComposers(): void
    {
        View::composer('memberpanel::components.layouts.master', function ($view) {
            $user = auth()->user();
            $insight = null;
            $tourId = null;
            $dailyReadingRecommendation = null;
            if ($user) {
                // Respeitar preferência do perfil: usuário pode desativar as dicas do bot
                if ($user->cbav_bot_enabled ?? true) {
                    $routeName = request()->route()?->getName();
                    $engine = app(CbavBotRuleEngineService::class);

                    // Parabéns ao completar perfil: forçar insight na próxima página após salvar
                    if (session()->pull('profile_just_completed', false)) {
                        $insight = $engine->getProfileCompleteInsightForUser($user, $routeName);
                    }
                    if ($insight === null) {
                        $insight = $engine->evaluate($user, $routeName);
                    }

                    $tourId = $insight['tour_id'] ?? CbavBotRuleEngineService::getTourIdForRoute($routeName ?? '');
                    // Leitura do dia: 1 recomendação por 24h (livro + capítulo), mesma do dashboard
                    $dailyReadingRecommendation = $insight
                        ? app(DailyReadingService::class)->getDailyReadingForDate(now())
                        : null;
                }
            }
            $view->with('cbavBotInsight', $insight);
            $view->with('cbavBotTourId', $tourId);
            $view->with('cbavBotVerseRecommendation', $dailyReadingRecommendation);
        });

        View::composer('pastoralpanel::components.layouts.master', function ($view) {
            $user = auth()->user();
            $eliasInsight = null;
            if ($user && ($user->cbav_bot_enabled ?? true)) {
                $routeName = request()->route()?->getName();
                $engine = app(CbavBotRuleEngineService::class);
                $eliasInsight = $engine->evaluate($user, $routeName);
            }
            $view->with('eliasInsight', $eliasInsight);
        });
    }

    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Gamification\App\Console\CheckBadgesCommand::class,
            \Modules\Gamification\App\Console\GenerateDailyReadingCommand::class,
        ]);
    }

    protected function registerCommandSchedules(): void
    {
        //
    }

    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));
        if (! is_dir($configPath)) {
            return;
        }
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }
            $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
            $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
            $segments = explode('.', $this->nameLower.'.'.$config_key);
            $normalized = [];
            foreach ($segments as $segment) {
                if (end($normalized) !== $segment) {
                    $normalized[] = $segment;
                }
            }
            $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);
            $this->publishes([$file->getPathname() => config_path($config)], 'config');
            $this->merge_config_from($file->getPathname(), $key);
        }
    }

    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;
        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');
        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);
        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);
        Blade::component('gamification::components.vertex-bot', 'gamification::vertex-bot');
    }

    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }
        return $paths;
    }
}
