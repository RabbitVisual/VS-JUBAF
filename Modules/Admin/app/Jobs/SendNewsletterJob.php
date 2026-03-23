<?php

namespace Modules\Admin\App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\HomePage\App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewsletterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $subject;
    public $content;

    /**
     * Create a new job instance.
     */
    public function __construct($subject, $content)
    {
        $this->subject = $subject;
        $this->content = $content;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        NewsletterSubscriber::active()->chunk(100, function ($subscribers) {
            foreach ($subscribers as $subscriber) {
                try {
                    // Note: We are queuing the mail here.
                    // This puts another job on the queue for the mailer.
                    // This is fine and standard Laravel practice.
                    Mail::to($subscriber->email)->queue(new \App\Mail\NewsletterSubscriptionMail($subscriber));
                } catch (\Exception $e) {
                    Log::error("Erro ao enviar campanha para {$subscriber->email}: " . $e->getMessage());
                }
            }
        });
    }
}
