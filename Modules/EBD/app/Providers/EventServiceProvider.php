<?php

namespace Modules\EBD\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\EBD\App\Events\StudentLeveledUp;
use Modules\EBD\App\Listeners\SendLevelUpNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        StudentLeveledUp::class => [
            SendLevelUpNotification::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;

    protected function configureEmailVerification(): void {}
}
