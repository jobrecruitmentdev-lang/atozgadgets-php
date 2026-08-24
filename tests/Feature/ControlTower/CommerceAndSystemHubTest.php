<?php

namespace Tests\Feature\ControlTower;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\PaymentTransaction;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommerceAndSystemHubTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'commerce_system_admin_' . uniqid() . '@atozgadgets.com',
            'mobile' => '1202' . rand(1000000, 9999999),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }

    public function test_payments_ledger_renders()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.commerce.payments'));
        $response->assertStatus(200);
        $response->assertSeeText('Commerce Payment Ledger');
        $response->assertSeeText('Total Captured Volume');
    }

    public function test_reviews_moderation_queue_and_status_update()
    {
        $category = Category::firstOrCreate(['slug' => 'gadgets'], [
            'name' => 'Gadgets',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Review Gadget',
            'slug' => 'review-gadget-' . uniqid(),
            'sku' => 'REV-GADGET-' . uniqid(),
            'price' => 29.99,
            'stock_quantity' => 10,
            'created_by' => $this->admin->id,
        ]);

        $review = ProductReview::create([
            'product_id' => $product->id,
            'user_id' => $this->admin->id,
            'rating' => 5,
            'title' => 'Terrific build quality',
            'review' => 'Exceeded my expectations on all fronts.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.commerce.reviews'));
        $response->assertStatus(200);
        $response->assertSeeText('Customer Reviews Moderation');
        $response->assertSeeText('Terrific build quality');

        // Approve review
        $updateResponse = $this->actingAs($this->admin)->post(route('admin.commerce.reviews.update_status', $review->id), [
            'status' => 'approved',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals('approved', $review->fresh()->status);
    }

    public function test_analytics_endpoints_render()
    {
        $salesResp = $this->actingAs($this->admin)->get(route('admin.analytics.sales'));
        $salesResp->assertStatus(200);
        $salesResp->assertSee('Revenue Analytics');

        $prodResp = $this->actingAs($this->admin)->get(route('admin.analytics.products'));
        $prodResp->assertStatus(200);
        $prodResp->assertSeeText('Top Performing Products');

        $profitResp = $this->actingAs($this->admin)->get(route('admin.analytics.profitability'));
        $profitResp->assertStatus(200);
        $profitResp->assertSee('Unit Economics');
    }

    public function test_system_health_and_audit_logs_render()
    {
        $healthResp = $this->actingAs($this->admin)->get(route('admin.system.health'));
        $healthResp->assertStatus(200);
        $healthResp->assertSee('Live Probes');

        $auditResp = $this->actingAs($this->admin)->get(route('admin.system.audit_logs'));
        $auditResp->assertStatus(200);
        $auditResp->assertSee('Audit Trail');
    }
}
