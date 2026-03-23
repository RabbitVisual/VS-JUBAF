<?php

namespace Modules\Worship\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Worship\App\Models\WorshipRoster;

class RosterCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public WorshipRoster $roster
    ) {}
}
