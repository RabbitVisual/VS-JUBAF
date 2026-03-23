<?php

namespace Modules\Gamification\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Gamification\App\Events\BadgeAwarded;
use Modules\Gamification\App\Events\MedalUnlocked;
use Modules\Gamification\App\Listeners\SendBadgeNotification;
use Modules\Gamification\App\Listeners\SendMedalUnlockedNotification;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<string, array<int, string>> */
    protected $listen = [
        BadgeAwarded::class => [
            SendBadgeNotification::class,
        ],
        MedalUnlocked::class => [
            SendMedalUnlockedNotification::class,
        ],
    ];

    protected static $shouldDiscoverEvents = true;
}
