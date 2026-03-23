<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web', 'auth']]);

        // Load main channels file if exists
        $mainChannels = base_path('routes/channels.php');
        if (file_exists($mainChannels)) {
            require $mainChannels;
        }

        // Load module channels
        $modulesPath = base_path('Modules');
        if (is_dir($modulesPath)) {
            foreach (glob($modulesPath . '/*/routes/channels.php') as $channelFile) {
                if (file_exists($channelFile)) {
                    require $channelFile;
                }
            }
        }
    }
}
