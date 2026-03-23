<?php

namespace Modules\Events\App\Listeners;

use Modules\Events\App\Events\RegistrationConfirmed;

/**
 * @deprecated FinancialEntry for RegistrationConfirmed is created only by Treasury\RegistrationConfirmedListener.
 * This listener is a no-op to avoid duplicate entries if still registered via discovery.
 */
class CreateFinancialEntry
{
    public function handle(RegistrationConfirmed $event): void
    {
        // No-op: Treasury\RegistrationConfirmedListener is the single owner.
    }
}
