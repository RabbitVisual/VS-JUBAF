<?php

namespace Modules\Events\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Events\App\Models\EventRegistration;

class RegistrationConfirmed
{
    use Dispatchable, SerializesModels;

    public EventRegistration $registration;

    /**
     * Create a new event instance.
     */
    public function __construct(EventRegistration $registration)
    {
        $this->registration = $registration;
    }
}
