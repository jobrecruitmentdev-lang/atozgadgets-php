<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Services\Catalog\CategoryResolverService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryHierarchyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_creates_core_parent_and_subcategories_with_taxonomy()
    {
        $resolver = app(CategoryResolverService::class);
        $resolver->ensureCoreTaxonomy();

        // Verify root categories exist
        $this->assertDatabaseHas('categories', [
            'slug' => 'electronics-gadgets',
            'parent_id' => null
        ]);

        $this->assertDatabaseHas('categories', [
            'slug' => 'smart-wearables',
            'parent_id' => null
        ]);

        $this->assertDatabaseHas('categories', [
            'slug' => 'home-kitchen',
            'parent_id' => null
        ]);

        // Verify subcategories exist under their parent
        $wearablesParent = Category::where('slug', 'smart-wearables')->first();
        $this->assertNotNull($wearablesParent);

        $smartWatchSub = Category::where('slug', 'smart-watches')->first();
        $this->assertNotNull($smartWatchSub);
        $this->assertEquals($wearablesParent->id, $smartWatchSub->parent_id);
    }

    /** @test */
    public function it_resolves_category_from_cj_hierarchical_string()
    {
        $resolver = app(CategoryResolverService::class);
        $resolver->ensureCoreTaxonomy();

        $category = $resolver->resolveOrCreateCategory(
            'Consumer Electronics > Smart Electronics > Smart Watches',
            'S9 Ultra Smart Watch Waterproof'
        );

        $this->assertNotNull($category);
        $this->assertEquals('smart-watches', $category->slug);
        $this->assertNotNull($category->parent_id);
    }

    /** @test */
    public function it_auto_creates_new_subcategory_if_unknown_category_comes_from_cj()
    {
        $resolver = app(CategoryResolverService::class);
        $resolver->ensureCoreTaxonomy();

        // Completely new category
        $category = $resolver->resolveOrCreateCategory(
            'Underwater Robotic Fish',
            'Autonomous Swimming Robotic Fish Toy'
        );

        $this->assertNotNull($category);
        $this->assertEquals('underwater-robotic-fish', $category->slug);
        $this->assertNotNull($category->parent_id); // attached to a parent

        // Verify it was persisted to database
        $this->assertDatabaseHas('categories', [
            'slug' => 'underwater-robotic-fish',
            'name' => 'Underwater Robotic Fish'
        ]);
    }

    /** @test */
    public function it_resolves_by_title_keywords_when_category_is_generic()
    {
        $resolver = app(CategoryResolverService::class);
        $resolver->ensureCoreTaxonomy();

        $category = $resolver->resolveOrCreateCategory(
            'General',
            'Professional 4K Dual Camera GPS Drone with Optical Flow'
        );

        $this->assertNotNull($category);
        $this->assertEquals('drones-rc', $category->slug);
    }

    /** @test */
    public function it_filters_shop_page_by_parent_category_retrieving_all_subcategories()
    {
        $resolver = app(CategoryResolverService::class);
        $resolver->ensureCoreTaxonomy();

        $brand = Brand::create(['name' => 'AtoZ', 'slug' => 'atoz', 'status' => 'active']);

        $parent = Category::where('slug', 'electronics-gadgets')->first();
        $droneSub = Category::where('slug', 'drones-rc')->first();
        $cameraSub = Category::where('slug', 'action-security-cameras')->first();

        $user = \App\Models\User::create([
            'first_name' => 'Admin',
            'last_name' => 'Tester',
            'email' => 'admin@test.com',
            'password' => bcrypt('secret123'),
            'role' => 'admin'
        ]);

        // Product in Drones subcategory
        $droneProduct = Product::create([
            'name' => 'AtoZ FPV Drone Pro',
            'sku' => 'SKU-DRONE-001',
            'slug' => 'atoz-fpv-drone-pro',
            'price' => 199.99,
            'category_id' => $droneSub->id,
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'stock_quantity' => 50,
            'description' => 'High speed FPV drone'
        ]);

        // Product in Cameras subcategory
        $cameraProduct = Product::create([
            'name' => 'AtoZ 4K Action Cam',
            'sku' => 'SKU-CAM-001',
            'slug' => 'atoz-4k-action-cam',
            'price' => 89.99,
            'category_id' => $cameraSub->id,
            'brand_id' => $brand->id,
            'created_by' => $user->id,
            'status' => 'active',
            'is_active' => true,
            'fulfillment_type' => 'cj',
            'stock_quantity' => 50,
            'description' => 'Waterproof 4K Action Camera'
        ]);

        // Query shop with Parent Category slug
        $response = $this->get('/shop?category=electronics-gadgets');
        $response->assertStatus(200);
        $response->assertSee('AtoZ FPV Drone Pro');
        $response->assertSee('AtoZ 4K Action Cam');

        // Query shop with Subcategory slug
        $responseSub = $this->get('/shop?category=drones-rc');
        $responseSub->assertStatus(200);
        $responseSub->assertSee('AtoZ FPV Drone Pro');
        $responseSub->assertDontSee('AtoZ 4K Action Cam');
    }
}
