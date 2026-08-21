<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Http;

class CjDropshippingE2ETest extends TestCase
{
    // use RefreshDatabase; // Removed to not wipe dev DB for this quick test

    public function test_full_cj_dropshipping_flow()
    {
        // 1. Setup Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_cj@test.com'], 
            ['first_name' => 'Admin', 'last_name' => 'Test', 'password' => bcrypt('password'), 'role_id' => 1]
        );
        $admin->role_id = 1;
        $admin->save();
        
        $category = Category::firstOrCreate(['slug' => 'test-cat'], ['name' => 'Test Category']);

        // 2. Test Search Endpoint (Live API via Mock or Real)
        // Note: CjAuthService handles token caching and fetching
        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/admin/cj/search?keyword=drone');
                         
        if ($response->status() !== 200) {
            dd($response->json());
        }
        
        $response->assertStatus(200);
        $this->assertArrayHasKey('data', $response->json());
        
        $products = $response->json('data.list') ?? [];
        if (count($products) > 0) {
            $productToImport = $products[0];
            
            // 3. Test Import Product (from Search Results)
            $importResponse = $this->actingAs($admin, 'sanctum')
                                   ->postJson('/admin/api/catalog/import-item', [
                                       'pid' => $productToImport['pid'] ?? 'TEST-PID-123',
                                       'title' => 'E2E Test Drone',
                                       'price' => 10.00,
                                       'image' => 'http://example.com/image.jpg',
                                       'category' => 'Drones',
                                       'categoryId' => $category->id
                                   ]);
            
            $importResponse->assertStatus(200)
                           ->assertJson(['success' => true]);
                           
            // 4. Test Place Order with valid model relations
            $order = \App\Models\Order::create([
                'order_number' => 'ORD-CJ-' . uniqid(),
                'user_id' => $admin->id,
                'total_amount' => 20.00,
                'status' => 'pending',
                'shipping_address' => json_encode([
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'address1' => '123 Fake St',
                    'city' => 'New York',
                    'state' => 'NY',
                    'postal_code' => '10001',
                    'country' => 'US',
                    'phone' => '+1234567890'
                ])
            ]);
            
            $dbProduct = \App\Models\Product::where('fulfillment_type', 'cj')->latest()->first();
            if ($dbProduct) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $dbProduct->id,
                    'quantity' => 1,
                    'unit_price' => 10.0,
                    'total_price' => 10.0,
                ]);
            }
            
            $mockCjId = 'CJ-ORD-MOCK-' . uniqid();
            \Illuminate\Support\Facades\Http::fake([
                '*/shopping/order/createOrderV2*' => \Illuminate\Support\Facades\Http::response([
                    'code' => 200,
                    'result' => true,
                    'data' => ['orderId' => $mockCjId]
                ], 200),
                '*/shopping/order/submitOrder*' => \Illuminate\Support\Facades\Http::response([
                    'code' => 200,
                    'result' => true
                ], 200),
                '*/logistic/freightCalculate*' => \Illuminate\Support\Facades\Http::response([
                    'code' => 200,
                    'data' => [['logisticName' => 'CJPacket Fast Line']]
                ], 200)
            ]);

            $orderResponse = $this->actingAs($admin, 'sanctum')
                                  ->postJson("/api/admin/cj/orders/{$order->id}/place");
            
            $orderResponse->assertStatus(200)
                          ->assertJson(['success' => true]);
        }
    }
}
