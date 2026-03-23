<?php

namespace Modules\Events\App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Events\App\Models\EventBatch;
use Modules\Events\App\Models\EventRegistration;

class TicketConcurrencyService
{
    /**
     * Reserve a ticket avoiding race conditions (Anti-Overselling).
     *
     * @param EventBatch $batch
     * @param User $user
     * @return EventRegistration
     * @throws Exception
     */
    public function reserveTicket(EventBatch $batch, User $user): EventRegistration
    {
        return DB::transaction(function () use ($batch, $user) {
            // 1. Lock the batch row
            // We re-query the batch with lockForUpdate to ensure we have the latest state
            // and no one else modifies it while we are reading/writing.
            $lockedBatch = EventBatch::where('id', $batch->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedBatch) {
                throw new Exception('Lote não encontrado.');
            }

            // 2. Check availability
            if ($lockedBatch->quantity_available <= 0) {
                // Future improvement: Handle auto-switch logic here if needed
                throw new Exception('Este lote está esgotado.');
            }

            // 3. Decrement stock
            $lockedBatch->decrement('quantity_available');

            // 4. Create Registration (Pending)
            // Note: ticket_hash is generated only after payment confirmation
            $registration = EventRegistration::create([
                'uuid' => (string) Str::uuid(),
                'event_id' => $lockedBatch->event_id,
                'batch_id' => $lockedBatch->id,
                'user_id' => $user->id,
                'status' => EventRegistration::STATUS_PENDING,
                'total_amount' => $lockedBatch->price,
            ]);

            return $registration;
        });
    }
}
