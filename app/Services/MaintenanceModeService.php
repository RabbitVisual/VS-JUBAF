<?php

namespace App\Services;

use Illuminate\Support\Str;

class MaintenanceModeService
{
    protected string $maintenancePath;

    public function __construct()
    {
        $this->maintenancePath = storage_path('framework/maintenance.php');
    }

    public function isActive(): bool
    {
        return file_exists($this->maintenancePath);
    }

    /**
     * Activate maintenance mode with a generated secret. Writes custom maintenance.php
     * that bootstraps the app so our middleware can allow except paths and bypass cookie.
     */
    public function activate(): string
    {
        $secret = Str::random(32);
        $stub = $this->getStub($secret);
        file_put_contents($this->maintenancePath, $stub);

        return $secret;
    }

    /**
     * Deactivate maintenance mode (removes the file, same as artisan up).
     */
    public function deactivate(): bool
    {
        if (! $this->isActive()) {
            return false;
        }

        return @unlink($this->maintenancePath);
    }

    protected function getStub(string $secret): string
    {
        $autoload = base_path('vendor/autoload.php');
        $bootstrap = base_path('bootstrap/app.php');

        return <<<PHP
<?php

define('LARAVEL_MAINTENANCE_SECRET', '{$secret}');

require '{$autoload}';

\$app = require_once '{$bootstrap}';

\$app->handleRequest(\Illuminate\Http\Request::capture());
exit;

PHP;
    }
}
