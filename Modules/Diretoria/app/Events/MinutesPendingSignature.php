<?php

namespace Modules\Diretoria\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Diretoria\App\Models\diretoriaMeeting;
use Modules\Diretoria\App\Models\MeetingMinutesVersion;

class MinutesPendingSignature
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public diretoriaMeeting $meeting,
        public MeetingMinutesVersion $minutesVersion
    ) {}
}
