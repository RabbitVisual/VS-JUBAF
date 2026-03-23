<?php

namespace Modules\Notifications\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Events\App\Events\RegistrationConfirmed;
use Modules\Notifications\App\Listeners\SendEventRegistrationNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        RegistrationConfirmed::class => [
            SendEventRegistrationNotification::class,
        ],
        'payment.completed' => [
            \Modules\Notifications\App\Listeners\SendPaymentCompletedNotification::class,
        ],
        \Modules\EBD\App\Events\StudentLeveledUp::class => [
            \Modules\Notifications\App\Listeners\SendStudentLeveledUpNotification::class,
        ],
        \Modules\Diretoria\App\Events\MinutesPendingSignature::class => [
            \Modules\Notifications\App\Listeners\NotifydiretoriaMembersMinutesPendingSignature::class,
        ],
        \Modules\EBD\App\Events\NewLessonAvailable::class => [
            \Modules\Notifications\App\Listeners\NotifyNewEbdLessonAvailable::class,
        ],
        \Modules\Worship\App\Events\RosterCreated::class => [
            \Modules\Notifications\App\Listeners\NotifyWorshipRosterCreated::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void {}
}
