<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\CjProduct;
use App\Models\CjVariant;
use App\Services\Catalog\PricingService;
use App\Services\Cj\CjProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CjCommercePhase1ExplorerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_explorer_' . uniqid() . '@example.com',
            'role_id' => 1,
            'is_active' => true,
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'smart-electronics'], [
            'name' => 'Smart Electronics',
            'status' => 'active',
        ]);
    }

    public function test_pricing_service_calculates_tiered_margins_and_psychological_rounding()
    {
        // Tier 1 (< $10): 2.5x + $3.00
        $t1 = PricingService::calculateRetailPrice(8.00);
        $this->assertEquals(8.00, $t1['cost_price']);
        $this->assertEquals(2.5, $t1['multiplier']);
        $this->assertEquals(3.00, $t1['shipping_allowance']);
        $this->assertEquals(23.99, $t1['selling_price']); // (8 * 2.5) + 3 = 23 -> 23.99

        // Tier 2 ($10 - $50): 2.0x + $5.00
        $t2 = PricingService::calculateRetailPrice(20.00);
        $this->assertEquals(2.0, $t2['multiplier']);
        $this->assertEquals(45.99, $t2['selling_price']); // (20 * 2.0) + 5 = 45 -> 45.99

        // Tier 3 (> $50): 1.6x + $8.00
        $t3 = PricingService::calculateRetailPrice(100.00);
        $this->assertEquals(1.6, $t3['multiplier']);
        $this->assertEquals(168.99, $t3['selling_price']); // (100 * 1.6) + 8 = 168 -> 168.99
    }

    public function test_admin_catalog_import_page_loads_with_cj_categories()
    {
        $response = $this->actingAs($this->admin)->get('/admin/catalog/import');
        $response->assertStatus(200);
        $response->assertSee('CJ Supplier Category');
        $response->assertSee('Warehouse / Country');
        $response->assertSee('PID vs VID Auto-Variant Splitting');
    }

    public function test_search_cj_api_returns_filterable_products()
    {
        $response = $this->actingAs($this->admin)->getJson('/admin/api/catalog/search?keyword=projector&countryCode=US');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'message',
            'data' => [
                'list' => [
                    '*' => ['pid', 'productNameEn', 'sellPrice', 'productImage', 'categoryName']
                ]
            ]
        ]);
    }

    public function test_import_cj_product_creates_pid_and_vid_variants_with_pricing_engine()
    {
        $pid = 'CJ-PID-TEST-' . uniqid();
        $payload = [
            'pid' => $pid,
            'title' => 'AtoZ 4K Ultra Wireless Projector',
            'price' => 25.00,
            'image' => 'https://images.unsplash.com/photo-1536440136628-849c177e76a1',
            'category' => 'Smart Electronics',
            'categoryId' => $this->category->id,
            'publish_now' => false,
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/api/catalog/import-item', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'draft']);

        // Assert Commercial Product created as Draft
        $product = Product::where('category_id', $this->category->id)->where('fulfillment_type', 'cj')->latest('id')->first();
        $this->assertNotNull($product);
        $this->assertEquals('draft', $product->status);
        $this->assertEquals(55.99, $product->price); // (25 * 2.0) + 5 = 55 -> 55.99

        // Assert Supplier CJ Product Pointer
        $cjProduct = CjProduct::where('cj_product_id', $pid)->first();
        $this->assertNotNull($cjProduct);
        $this->assertEquals($product->id, $cjProduct->internal_product_id);

        // Assert Product Variants and Supplier Variants created (PID vs VID split)
        $variants = ProductVariant::where('product_id', $product->id)->get();
        $this->assertNotEmpty($variants);

        $cjVariants = CjVariant::where('cj_product_id', $pid)->get();
        $this->assertNotEmpty($cjVariants);
        $this->assertEquals($variants->count(), $cjVariants->count());
    }

    public function test_import_cj_product_with_publish_now_creates_active_product()
    {
        $pid = 'CJ-PID-PUB-' . uniqid();
        $payload = [
            'pid' => $pid,
            'title' => 'AtoZ Smart Drone Pro Edition',
            'price' => 40.00,
            'image' => 'https://images.unsplash.com/photo-1527977966376-1c8408f9f108',
            'category' => 'Smart Electronics',
            'categoryId' => $this->category->id,
            'publish_now' => true,
        ];

        $response = $this->actingAs($this->admin)->postJson('/admin/api/catalog/import-item', $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'status' => 'active']);

        $product = Product::where('slug', 'like', 'atoz-smart-drone-pro-edition%')->first();
        $this->assertNotNull($product);
        $this->assertEquals('active', $product->status);
        $this->assertTrue((bool)$product->is_active);
    }
}
