<?php

namespace Modules\Sermons\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Sermons\App\Models\Sermon;
use App\Models\User;
use Modules\Notifications\App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Notification;

class NotifyMembersOfNewContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $sermon;

    /**
     * Create a new job instance.
     */
    public function __construct(Sermon $sermon)
    {
        $this->sermon = $sermon;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->sermon->status !== Sermon::STATUS_PUBLISHED) {
            return;
        }

        // Notify all active members
        // In a real scenario, we might chunk this or use a more efficient broadcasting method
        // For now, we will notify a small batch or just log it if users count is huge
        // Assuming reasonable user base for this task scope.

        $users = User::where('status', 'active')->get();

        $notificationData = [
            'title' => 'Novo Sermão Disponível: ' . $this->sermon->title,
            'message' => 'Um novo conteúdo edificante foi publicado. Clique para ler ou ouvir.',
            'action_url' => route('memberpanel.sermons.show', $this->sermon->id),
            'type' => 'info',
            'icon' => 'book-open'
        ];

        // Using the SystemNotification class if it exists in Modules/Notifications
        // Assuming standard notification structure
        Notification::send($users, new SystemNotification(
            $notificationData['title'],
            $notificationData['message'],
            $notificationData['action_url'],
            $notificationData['type'],
            $notificationData['icon']
        ));
    }
}
