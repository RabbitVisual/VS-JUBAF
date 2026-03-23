<?php

namespace Modules\ChurchCouncil\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\ChurchCouncil\App\Models\CouncilMeeting;
use Modules\ChurchCouncil\App\Models\MeetingMinutesVersion;

class MinutesPendingSignature
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public CouncilMeeting $meeting,
        public MeetingMinutesVersion $minutesVersion
    ) {}
}
