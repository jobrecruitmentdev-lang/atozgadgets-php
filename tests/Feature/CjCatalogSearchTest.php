<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use App\Services\Cj\CjProductService;
use App\Services\Cj\CjAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CjCatalogSearchTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_search_' . uniqid() . '@atozgadgets.com',
            'role_id' => 1,
            'is_active' => true,
        ]);
    }

    public function test_categories_service_returns_structured_categories()
    {
        $categories = CjProductService::getCategories();
        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);
        $this->assertArrayHasKey('id', $categories[0]);
        $this->assertArrayHasKey('name', $categories[0]);
    }

    public function test_search_api_endpoint_processes_filters_and_returns_products()
    {
        // Force Sandbox/Mock mode for deterministic assertion
        Setting::set('cj_sandbox_mode', '1', 'cj');
        $this->assertTrue(CjAuthService::isSandboxMode());

        $response = $this->actingAs($this->admin)
                         ->getJson('/admin/api/catalog/search?keyword=drone&minPrice=10&maxPrice=100&countryCode=US');

        $response->assertStatus(200);
        $response->assertJson([
            'result' => true,
            'message' => 'Success',
        ]);
        $data = $response->json('data');
        $this->assertArrayHasKey('list', $data);
        $this->assertNotEmpty($data['list']);
        $this->assertArrayHasKey('pid', $data['list'][0]);
        $this->assertArrayHasKey('sellPrice', $data['list'][0]);
    }

    public function test_admin_catalog_import_page_loads_with_categories()
    {
        $response = $this->actingAs($this->admin)->get('/admin/catalog/import');
        $response->assertStatus(200);
        $response->assertSee('Product Import Pipeline');
        $response->assertSee('Price Range ($)');
    }
}
