<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Http;

class CjDropshippingE2ETest extends TestCase
{
    use RefreshDatabase;

    public function test_full_cj_dropshipping_flow()
    {
        // 1. Setup Admin
        $admin = User::create([
            'email' => 'admin_cj_' . uniqid() . '@test.com',
            'mobile' => '99' . mt_rand(10000000, 99999999),
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'is_active' => true,
        ]);
        
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
            $pid = !empty($productToImport['pid']) ? $productToImport['pid'] : (!empty($productToImport['id']) ? $productToImport['id'] : 'TEST-PID-' . uniqid());
            
            // 3. Test Import Product (from Search Results)
            $importResponse = $this->actingAs($admin, 'sanctum')
                                   ->postJson('/api/admin/cj/import', [
                                       'pid' => $pid,
                                       'title' => 'E2E Test Drone',
                                       'price' => 10.00,
                                       'image' => 'http://example.com/image.jpg',
                                       'category' => 'Drones',
                                       'categoryId' => $category->id
                                   ]);
            
            $importResponse->assertStatus(200)
                           ->assertJson(['success' => true]);
                           
            $orderUser = \App\Models\User::find($admin->id) ?: (\App\Models\User::first() ?: \App\Models\User::create([
                'email' => 'admin_order_' . uniqid() . '@test.com',
                'first_name' => 'Admin',
                'last_name' => 'Order',
                'password' => bcrypt('password'),
                'role_id' => 1,
                'is_active' => true,
            ]));

            $order = \App\Models\Order::create([
                'order_number' => 'ORD-CJ-' . uniqid(),
                'user_id' => $orderUser->id,
                'total_amount' => 20.00,
                'status' => 'pending',
                'payment_status' => 'paid',
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
            
            $importedProductId = $importResponse->json('internal_id') ?? (\App\Models\Product::where('fulfillment_type', 'cj')->latest()->value('id'));
            if ($importedProductId) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $importedProductId,
                    'quantity' => 1,
                    'unit_price' => 10.0,
                    'total_price' => 10.0,
                ]);
            }
            
            $mockCjId = 'CJ-ORD-MOCK-' . uniqid();
            \App\Models\Setting::set('cj_sandbox_mode', '0');
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
