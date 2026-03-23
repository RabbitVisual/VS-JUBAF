<?php

namespace Modules\EBD\App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\EBD\App\Models\EbdGamificationLevel;

class StudentLeveledUp
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $newLevel;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, EbdGamificationLevel $newLevel)
    {
        $this->user = $user;
        $this->newLevel = $newLevel;
    }
}
