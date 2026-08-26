<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class ProductStatusToggleTest extends TestCase
{
    use RefreshDatabase;

    public function test_hot_toggle_product_status_returns_json_instantly()
    {
        $admin = User::factory()->create(['role_id' => 1]);
        $category = Category::create(['name' => 'Tech', 'slug' => 'tech', 'status' => 'active']);
        
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Fast Toggle Gadget',
            'slug' => 'fast-toggle-gadget',
            'sku' => 'FTG-001',
            'price' => 29.99,
            'stock_quantity' => 10,
            'status' => 'draft',
            'is_active' => false,
            'fulfillment_type' => 'cj',
            'created_by' => $admin->id
        ]);

        // Toggle Draft -> Active (JSON request)
        $response = $this->actingAs($admin)
            ->patchJson("/admin/catalog/products/{$product->id}/toggle-status");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'active',
                'is_active' => true,
                'label' => 'Live'
            ]);

        $this->assertEquals('active', $product->fresh()->status);
        $this->assertTrue((bool)$product->fresh()->is_active);

        // Toggle Active -> Draft (JSON request)
        $response2 = $this->actingAs($admin)
            ->patchJson("/admin/catalog/products/{$product->id}/toggle-status");

        $response2->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'draft',
                'is_active' => false,
                'label' => 'Draft'
            ]);

        $this->assertEquals('draft', $product->fresh()->status);
        $this->assertFalse((bool)$product->fresh()->is_active);
    }
}
