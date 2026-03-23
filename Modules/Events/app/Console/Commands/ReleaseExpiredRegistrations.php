<?php

namespace Modules\Events\App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Events\App\Models\EventRegistration;
use Modules\Events\App\Models\EventBatch;
use Carbon\Carbon;

class ReleaseExpiredRegistrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:release-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release pending registrations that have expired (> 15 mins).';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expirationTime = now()->subMinutes(15);

        // Find expired pending registrations
        $expiredRegistrations = EventRegistration::where('status', EventRegistration::STATUS_PENDING)
            ->where('created_at', '<', $expirationTime)
            ->get();

        if ($expiredRegistrations->isEmpty()) {
            $this->info('No expired registrations found.');
            return;
        }

        $this->info("Found {$expiredRegistrations->count()} expired registrations.");

        foreach ($expiredRegistrations as $registration) {
            // Restore stock
            if ($registration->batch_id) {
                $batch = EventBatch::find($registration->batch_id);
                if ($batch) {
                    $batch->increment('quantity_available');
                    $this->info("Restored stock for batch {$batch->name} (Reg ID: {$registration->id})");
                }
            }

            // Mark as cancelled (or deleted, depending on policy. Cancelled is safer for history)
            $registration->update([
                'status' => EventRegistration::STATUS_CANCELLED,
                'notes' => 'Cancelado automaticamente por expiração de tempo (15 min).',
            ]);
        }

        $this->info('Expired registrations processed successfully.');
    }
}
