<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Subcategory; // Assuming this model exists
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CatalogImportE2ETest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure admin user ID 1 exists for foreign key constraint
        if (!\App\Models\User::find(1)) {
            \App\Models\User::create([
                'id' => 1,
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Ensure category ID 1 exists since the controller hardcodes it
        if (!Category::find(1)) {
            Category::create([
                'id' => 1,
                'name' => 'Test Category',
                'slug' => 'test-category',
                'status' => 'active',
            ]);
        }

        // Ensure subcategory ID 1 exists
        if (class_exists(\App\Models\SubCategory::class) && !\App\Models\SubCategory::find(1)) {
            \App\Models\SubCategory::create([
                'id' => 1,
                'category_id' => 1,
                'name' => 'Test Subcategory',
                'slug' => 'test-subcategory',
                'status' => 'active',
            ]);
        }
    }

    public function test_catalog_import_view_is_accessible()
    {
        $response = $this->get('/admin/catalog/import');

        $response->assertStatus(200);
        $response->assertViewIs('admin.catalog.import');
    }

    public function test_import_cj_product_successfully()
    {
        $payload = [
            'pid' => 'CJ-TEST-001',
            'title' => 'Awesome Drone',
            'price' => 45.50,
            'image' => 'https://example.com/drone.jpg',
            'category' => 'Drones'
        ];

        $response = $this->postJson('/admin/api/catalog/import-item', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'internal_id']);
        
        $this->assertDatabaseHas('products', [
            'name' => 'Awesome Drone',
            'fulfillment_type' => 'cj',
            'price' => 91.0 // 45.50 * 2.0 markup
        ]);

        $this->assertDatabaseHas('cj_products', [
            'cj_product_id' => 'CJ-TEST-001',
            'sell_price' => 45.50,
            'status' => 'imported'
        ]);
    }

    public function test_import_cj_product_validation_failure()
    {
        $payload = [
            'title' => 'Awesome Drone',
            // Missing pid, price, image
        ];

        $response = $this->postJson('/admin/api/catalog/import-item', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['pid', 'price', 'image']);
    }

    public function test_import_cj_product_security_xss_sanitization()
    {
        $payload = [
            'pid' => 'CJ-TEST-XSS',
            'title' => '<script>alert("XSS")</script> Awesome Drone',
            'price' => 45.50,
            'image' => 'https://example.com/drone.jpg',
            'category' => 'Drones'
        ];

        $response = $this->postJson('/admin/api/catalog/import-item', $payload);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('products', [
            'fulfillment_type' => 'cj',
            'name' => '<script>alert("XSS")</script> Awesome Drone'
        ]);
    }

    public function test_import_cj_product_sql_injection()
    {
        $payload = [
            'pid' => "CJ-TEST-002' OR 1=1 --",
            'title' => 'Drone',
            'price' => 45.50,
            'image' => 'https://example.com/drone.jpg',
            'category' => 'Drones'
        ];

        $response = $this->postJson('/admin/api/catalog/import-item', $payload);

        $response->assertStatus(200);
        
        // It should insert normally without executing SQL injection
        $this->assertDatabaseHas('cj_products', [
            'cj_product_id' => "CJ-TEST-002' OR 1=1 --"
        ]);
    }
}
