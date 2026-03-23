<?php

namespace Modules\Intercessor\App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Intercessor\App\Models\PrayerCommitment;

class PrayerCommitmentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commitment;

    public function __construct(PrayerCommitment $commitment)
    {
        $this->commitment = $commitment;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('intercessor-global'),
        ];
    }
}
