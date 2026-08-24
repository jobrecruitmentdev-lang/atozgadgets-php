<?php

namespace Tests\Feature\Resilience;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\User;
use App\Services\Inventory\InventoryService;

class InventoryConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_competing_reservations_for_single_stock_unit_allow_only_one_success()
    {
        $user = User::factory()->create();
        $cat = \App\Models\Category::create(['name' => 'Audio', 'slug' => 'audio', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Limited Edition Wireless Earbuds',
            'slug' => 'limited-wireless-earbuds',
            'sku' => 'AZG-EAR-001',
            'price' => 49.99,
            'stock_quantity' => 1, // Only 1 unit in stock
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $items = [
            ['product_id' => $product->id, 'quantity' => 1]
        ];

        // Request 1: Reserves the 1 available unit
        $res1 = InventoryService::reserve(orderId: 101, items: $items, ttlMinutes: 15);
        $this->assertTrue($res1, 'First reservation must succeed');

        // Request 2: Competes for the same unit while Request 1's reservation is active
        $res2 = InventoryService::reserve(orderId: 102, items: $items, ttlMinutes: 15);
        $this->assertFalse($res2, 'Second reservation must be rejected due to active reservation lock');

        // Check availability projection reflects out of stock
        $availability = InventoryService::getAvailability($product);
        $this->assertEquals(InventoryService::STATUS_LOW_STOCK, $availability['status']); // stock is 1 but ATS is 0

        // Release reservation 1 (simulating abandonment/timeout)
        InventoryService::release(orderId: 101);

        // Request 3: Can now successfully acquire the released unit
        $res3 = InventoryService::reserve(orderId: 103, items: $items, ttlMinutes: 15);
        $this->assertTrue($res3, 'Reservation must succeed after prior reservation release');
    }
}
