<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Services\Seo\SeoMetadataService;

class SeoAeoProductOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create(['role_id' => 1]);
        $watchCat = Category::create(['slug' => 'womens-bracelet-watches', 'name' => "Women's Bracelet Watches", 'status' => 'active']);
        $kitchenCat = Category::create(['slug' => 'kitchen-tools', 'name' => 'Kitchen Tools', 'status' => 'active']);

        // 1. Women's Watch
        Product::create([
            'name' => 'Vintage High-end Waterproof Steel-band Quartz Watch For Women',
            'slug' => 'vintage-high-end-waterproof-steel-band-quartz-watch-for-women-be68eb',
            'sku' => 'CJ-92624c48',
            'price' => 31.52,
            'category_id' => $watchCat->id,
            'created_by' => $user->id,
            'is_active' => true,
            'status' => 'active',
            'description' => '' // Purposely empty to test graceful AEO fallback
        ]);

        // 2. Kitchen Tool
        Product::create([
            'name' => 'Novelty Ceramic Chicken Egg Yolk Separator',
            'slug' => 'novelty-ceramic-chicken-egg-yolk-separator',
            'sku' => 'CJ-KTC-002',
            'price' => 26.99,
            'category_id' => $kitchenCat->id,
            'created_by' => $user->id,
            'is_active' => true,
            'status' => 'active',
            'description' => 'Cute ceramic chicken egg divider for baking.'
        ]);
    }

    public function test_watch_product_generates_unique_seo_aeo_and_specifications(): void
    {
        $watch = Product::where('slug', 'like', '%quartz-watch%')->firstOrFail();

        // 1. Meta Title
        $title = SeoMetadataService::getMetaTitle($watch);
        $this->assertStringContainsString('AtoZGadgets', $title);
        $this->assertStringContainsString('Quartz Watch', $title);

        // 2. Meta Description
        $desc = SeoMetadataService::getMetaDescription($watch);
        $this->assertNotEmpty($desc);
        $this->assertStringContainsString('$31.52', $desc);
        $this->assertStringContainsString('US delivery', $desc);

        // 3. Keywords
        $keywords = SeoMetadataService::getMetaKeywords($watch);
        $this->assertStringContainsString('womens watch', $keywords);
        $this->assertStringContainsString('ladies watch', $keywords);
        $this->assertStringContainsString('quartz watch', $keywords);
        $this->assertStringContainsString('waterproof watch', $keywords);
        $this->assertStringContainsString('AtoZGadgets', $keywords);

        // 4. Dynamic Specifications
        $specs = SeoMetadataService::getDynamicSpecifications($watch);
        $this->assertArrayHasKey('Movement Type', $specs);
        $this->assertArrayHasKey('Water Resistance', $specs);
        $this->assertEquals('Precision Quartz Movement', $specs['Movement Type']);
        $this->assertStringContainsString('3ATM', $specs['Water Resistance']);

        // 5. Storefront Rendered HTML
        $response = $this->get('/product/' . $watch->slug);
        $response->assertStatus(200);
        $response->assertSee('<title>' . e($title) . '</title>', false);
        $response->assertSee('name="description" content="' . e($desc) . '"', false);
        $response->assertSee('Technical Specifications', false);
        $response->assertSee('Precision Quartz Movement', false);
        $response->assertSee('30M / 3ATM', false);
        $response->assertSee('FAQPage', false);
    }

    public function test_kitchen_product_has_distinct_seo_and_specs_from_watch(): void
    {
        $kitchen = Product::where('slug', 'like', '%egg-yolk%')->firstOrFail();

        $keywords = SeoMetadataService::getMetaKeywords($kitchen);
        $this->assertStringContainsString('kitchen gadgets', $keywords);
        $this->assertStringContainsString('home gadgetz', $keywords);
        $this->assertStringNotContainsString('quartz watch', $keywords);

        $specs = SeoMetadataService::getDynamicSpecifications($kitchen);
        $this->assertArrayHasKey('Material Safety', $specs);
        $this->assertArrayHasKey('Cleaning & Care', $specs);
        $this->assertStringContainsString('Dishwasher Safe', $specs['Cleaning & Care']);

        $response = $this->get('/product/' . $kitchen->slug);
        $response->assertStatus(200);
        $response->assertSee('Dishwasher Safe', false);
        $response->assertSee('AtoZGadgets', false);
    }
}
