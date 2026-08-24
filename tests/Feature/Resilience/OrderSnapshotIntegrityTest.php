<?php

namespace Tests\Feature\Resilience;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\CheckoutSession;
use App\Services\Order\OrderService;

class OrderSnapshotIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_item_snapshots_exact_purchased_variant_cost_and_is_immune_to_catalog_mutations()
    {
        $user = User::factory()->create();
        $cat = \App\Models\Category::create(['name' => 'Gadgets', 'slug' => 'gadgets', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Original Gadget Alpha',
            'slug' => 'original-gadget-alpha',
            'sku' => 'AZG-ORIG-001',
            'price' => 100.00,
            'stock_quantity' => 10,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $user->id,
        ]);

        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'AZG-VAR-BLK',
            'name' => 'Midnight Black Edition',
            'cost_price' => 25.00,
            'selling_price' => 100.00,
            'stock_quantity' => 5,
        ]);

        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'AZG-VAR-GOLD',
            'name' => '24K Gold Edition',
            'cost_price' => 60.00,
            'selling_price' => 150.00,
            'stock_quantity' => 5,
        ]);

        // Customer buys Variant 2 (Gold Edition)
        $session = CheckoutSession::create([
            'session_token' => 'sess_snap_test_99',
            'user_id' => $user->id,
            'line_items' => [
                [
                    'product_id' => $product->id,
                    'variant_id' => $variant2->id, // Exact Variant 2
                    'quantity' => 1,
                    'unit_price' => 150.00,
                    'total_price' => 150.00,
                ]
            ],
            'subtotal' => 150.00,
            'grand_total' => 150.00,
            'status' => 'active',
        ]);

        $order = OrderService::createPendingOrderFromSession($session, [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'address_line1' => '123 Wall St',
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'US',
            'postal_code' => '10005',
        ]);

        $item = $order->items()->first();
        $this->assertEquals('AZG-VAR-GOLD', $item->merchant_sku_snapshot);
        $this->assertEquals('Original Gadget Alpha', $item->product_name_snapshot);
        $this->assertEquals('24K Gold Edition', $item->variant_name_snapshot);
        $this->assertEquals(60.00, (float)$item->supplier_cost_snapshot);
        $this->assertEquals(90.00, (float)$item->contribution_margin_snapshot);

        // Mutate the catalog product and variant
        $product->update([
            'name' => 'Renamed Gadget Beta (Price Increased)',
            'sku' => 'AZG-MUTATED-999',
            'price' => 299.99,
        ]);
        $variant2->update([
            'name' => 'Renamed Platinum Edition',
            'sku' => 'AZG-VAR-PLAT',
            'cost_price' => 120.00,
        ]);

        // Verify Historical Order Item remains strictly unchanged
        $item->refresh();
        $this->assertEquals('AZG-VAR-GOLD', $item->merchant_sku_snapshot);
        $this->assertEquals('Original Gadget Alpha', $item->product_name_snapshot);
        $this->assertEquals('24K Gold Edition', $item->variant_name_snapshot);
        $this->assertEquals(60.00, (float)$item->supplier_cost_snapshot);
        $this->assertEquals(90.00, (float)$item->contribution_margin_snapshot);
    }
}
