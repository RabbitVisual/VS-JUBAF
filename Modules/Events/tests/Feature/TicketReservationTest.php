<?php

namespace Modules\Events\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventBatch;
use Modules\Events\App\Services\TicketConcurrencyService;
use App\Models\User;
use Illuminate\Support\Str;

class TicketReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_reserve_ticket()
    {
        // Setup
        $user = User::factory()->create();
        $event = Event::create([
            'title' => 'Test Event',
            'start_date' => now()->addDays(10),
            'status' => 'published',
            'slug' => 'test-event-' . Str::random(5),
            'created_by' => $user->id
        ]);
        
        $batch = $event->batches()->create([
            'name' => 'Lote 1',
            'price' => 100,
            'quantity_available' => 5
        ]);

        // Action
        $service = new TicketConcurrencyService();
        $registration = $service->reserveTicket($batch, $user);

        // Assert
        $this->assertDatabaseHas('event_registrations', [
            'id' => $registration->id,
            'status' => 'pending',
            'user_id' => $user->id,
            'batch_id' => $batch->id
        ]);
        
        $this->assertEquals(4, $batch->fresh()->quantity_available);
    }

    public function test_cannot_reserve_sold_out_ticket()
    {
        // Setup
        $user = User::factory()->create();
        $event = Event::create([
            'title' => 'Test Event Sold Out',
            'start_date' => now()->addDays(10),
            'status' => 'published',
            'slug' => 'test-event-so-' . Str::random(5)
        ]);
        
        $batch = $event->batches()->create([
            'name' => 'Lote Sold Out',
            'price' => 100,
            'quantity_available' => 0
        ]);

        // Action & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Este lote está esgotado.'); // Matches the message in Service

        $service = new TicketConcurrencyService();
        $service->reserveTicket($batch, $user);
    }
}
