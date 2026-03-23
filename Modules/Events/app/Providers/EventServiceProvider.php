<?php

namespace Modules\Events\App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        'payment.completed' => [
            \Modules\Events\App\Listeners\ConfirmRegistrationOnPaymentCompleted::class,
        ],
        \Modules\Events\App\Events\RegistrationConfirmed::class => [
            \Modules\Events\App\Listeners\SendTicketEmail::class,
            \Modules\Events\App\Listeners\NotifyAdminsOnEventCapacity::class,
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
