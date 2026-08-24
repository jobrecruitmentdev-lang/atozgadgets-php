<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Cj\CjProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CjCatalogSearchResilienceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_search_' . uniqid() . '@example.com',
            'mobile' => '1202' . rand(1000000, 9999999),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }

    public function test_catalog_search_returns_valid_results_without_crashing()
    {
        $response = $this->actingAs($this->admin)->getJson('/admin/api/catalog/search?keyword=projector');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'result',
            'message',
            'data' => [
                'list',
                'total'
            ]
        ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data['list']);
    }

    public function test_catalog_search_with_category_and_price_filters()
    {
        $response = $this->actingAs($this->admin)->getJson('/admin/api/catalog/search?minPrice=10&maxPrice=50');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertIsArray($data['list']);
        foreach ($data['list'] as $item) {
            $this->assertGreaterThanOrEqual(10.0, (float)$item['sellPrice']);
            $this->assertLessThanOrEqual(50.0, (float)$item['sellPrice']);
        }
    }

    public function test_catalog_search_handles_rapid_consecutive_requests()
    {
        $queries = ['projector', 'sweatshirt', 'drone', 'watch', 'lamp'];

        foreach ($queries as $kw) {
            $res = $this->actingAs($this->admin)->getJson('/admin/api/catalog/search?keyword=' . $kw);
            $res->assertStatus(200);
            $data = $res->json('data');
            $this->assertIsArray($data['list']);
        }
    }
}
