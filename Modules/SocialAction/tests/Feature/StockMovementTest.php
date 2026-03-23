<?php

namespace Modules\SocialAction\Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\SocialAction\App\Models\SocialPantryItem;
use Modules\SocialAction\App\Models\SocialKit;
use Modules\SocialAction\App\Services\StockManagerService;
use App\Models\User;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_kit_delivery_deducts_stock()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create Items
        $rice = SocialPantryItem::create(['name' => 'Rice', 'current_quantity' => 10]);
        $beans = SocialPantryItem::create(['name' => 'Beans', 'current_quantity' => 10]);

        // 2. Create Kit
        $kit = SocialKit::create(['name' => 'Basic Basket']);
        $kit->items()->attach([
            $rice->id => ['quantity' => 2], // 2 units of rice
            $beans->id => ['quantity' => 1], // 1 unit of beans
        ]);

        // 3. Deduct Kit
        $service = new StockManagerService();
        $service->deductKit($kit, 1, $user->id);

        // 4. Verify Stock
        $this->assertEquals(8, $rice->fresh()->current_quantity);
        $this->assertEquals(9, $beans->fresh()->current_quantity);

        // 5. Verify Movement Log
        $this->assertDatabaseHas('social_stock_movements', [
            'pantry_item_id' => $rice->id,
            'type' => 'out',
            'quantity' => 2,
        ]);
    }
}
