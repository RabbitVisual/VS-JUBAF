<?php

namespace Modules\EBD\App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\EBD\App\Models\EBDLesson;

class NewLessonAvailable
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public EBDLesson $lesson
    ) {}
}
