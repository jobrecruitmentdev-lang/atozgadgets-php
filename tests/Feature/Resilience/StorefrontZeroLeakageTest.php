<?php

namespace Tests\Feature\Resilience;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class StorefrontZeroLeakageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_html_and_json_endpoints_leak_zero_supplier_ids_or_supplier_cdns()
    {
        $user = User::factory()->create();
        $cat = Category::create([
            'name' => 'Audio Gear',
            'slug' => 'audio-gear',
            'status' => 'active',
        ]);

        $product = Product::create([
            'category_id' => $cat->id,
            'name' => 'Noise Cancelling Headphones X',
            'slug' => 'noise-cancelling-headphones-x',
            'sku' => 'AZG-NC-001',
            'price' => 199.99,
            'stock_quantity' => 20,
            'status' => 'active',
            'is_active' => true,
            'thumbnail_image' => 'https://cc-west-usa.oss-us-west-1.aliyuncs.com/12345/abcde.jpg', // Supplier CDN URL
            'fulfillment_type' => 'cj',
            'created_by' => $user->id,
        ]);

        // Public Web Route: /shop
        $shopRes = $this->get('/shop');
        $shopRes->assertStatus(200);
        $shopHtml = $shopRes->getContent();

        // Must not contain direct supplier CDN URL
        $this->assertStringNotContainsString('cc-west-usa.oss-us-west-1.aliyuncs.com', $shopHtml);
        // Must contain media proxy route
        $this->assertStringContainsString("/media/products/{$product->id}/thumbnail", $shopHtml);

        // Public Web Route: /product/{slug}
        $prodRes = $this->get("/product/{$product->slug}");
        $prodRes->assertStatus(200);
        $prodHtml = $prodRes->getContent();
        $this->assertStringNotContainsString('cc-west-usa.oss-us-west-1.aliyuncs.com', $prodHtml);

        // Public JSON API Route: /api/products/{slug}
        $apiRes = $this->getJson("/api/products/{$product->slug}");
        $apiRes->assertStatus(200);
        $apiData = $apiRes->json('data');

        // Must not leak cj_product_id in serialized JSON
        $this->assertArrayNotHasKey('cj_product_id', $apiData);
    }
}
