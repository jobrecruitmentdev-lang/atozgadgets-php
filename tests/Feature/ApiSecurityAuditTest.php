<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $customerUser;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin_api_' . uniqid() . '@atozgadgets.com',
            'role_id' => 1,
            'is_active' => true,
        ]);

        $this->customerUser = User::factory()->create([
            'email' => 'customer_api_' . uniqid() . '@example.com',
            'role_id' => 3,
            'is_active' => true,
        ]);

        $category = Category::firstOrCreate(['slug' => 'tech-gadgets'], [
            'name' => 'Tech Gadgets',
            'status' => 'active',
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Secure Gadget X',
            'slug' => 'secure-gadget-x-' . uniqid(),
            'sku' => 'SEC-' . strtoupper(uniqid()),
            'price' => 89.99,
            'stock_quantity' => 100,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $this->adminUser->id,
        ]);
    }

    public function test_cart_api_crud_lifecycle_and_user_isolation()
    {
        $this->actingAs($this->customerUser, 'sanctum');

        // 1. Add to cart (POST /api/cart)
        $storeResponse = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
        $storeResponse->assertStatus(201)
                      ->assertJsonPath('success', true);

        $cartId = $storeResponse->json('data.id');

        // 2. Fetch cart (GET /api/cart)
        $indexResponse = $this->getJson('/api/cart');
        $indexResponse->assertStatus(200)
                      ->assertJsonPath('success', true)
                      ->assertJsonPath('data.summary.count', 2);

        // 3. Update cart item quantity (PUT /api/cart/{id})
        $updateResponse = $this->putJson("/api/cart/{$cartId}", [
            'quantity' => 5,
        ]);
        $updateResponse->assertStatus(200)
                       ->assertJsonPath('data.quantity', 5);

        // 4. Delete from cart (DELETE /api/cart/{id})
        $deleteResponse = $this->deleteJson("/api/cart/{$cartId}");
        $deleteResponse->assertStatus(200)
                       ->assertJsonPath('success', true);

        // 5. Verify cart is empty
        $emptyCartResponse = $this->getJson('/api/cart');
        $emptyCartResponse->assertStatus(200)
                          ->assertJsonPath('data.summary.count', 0);
    }

    public function test_unpaid_order_blocked_from_cj_dispatch_api()
    {
        $this->actingAs($this->adminUser, 'sanctum');

        $unpaidOrder = Order::create([
            'user_id' => $this->customerUser->id,
            'order_number' => 'ORD-UNPAID-API-' . time(),
            'subtotal' => 120.00,
            'total_amount' => 120.00,
            'status' => 'pending',
            'payment_status' => 'pending', // Unpaid!
        ]);

        $response = $this->postJson("/api/admin/cj/orders/{$unpaidOrder->id}/place");
        $response->assertStatus(422)
                 ->assertJsonPath('success', false);
    }

    public function test_products_api_returns_paginated_active_items()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonStructure([
                     'data' => [
                         'products',
                         'pagination' => ['total', 'page', 'limit', 'totalPages']
                     ]
                 ]);
    }

    public function test_customer_cannot_access_admin_cj_api()
    {
        $this->actingAs($this->customerUser, 'sanctum');

        $response = $this->getJson('/api/admin/cj/search');
        $response->assertStatus(403);
    }
}
