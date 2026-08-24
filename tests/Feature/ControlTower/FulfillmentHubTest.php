<?php

namespace Tests\Feature\ControlTower;

use Tests\TestCase;
use App\Models\Order;
use App\Models\User;
use App\Models\Fulfillment;
use App\Models\FulfillmentException;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FulfillmentHubTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'fulfillment_hub_admin_' . uniqid() . '@atozgadgets.com',
            'mobile' => '1202' . rand(1000000, 9999999),
            'role_id' => 1,
            'is_active' => true,
        ]);
    }

    public function test_fulfillment_overview_renders()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fulfillment.overview'));
        $response->assertStatus(200);
        $response->assertSeeText('Fulfillment Overview');
        $response->assertSeeText('Pending Dispatch');
        $response->assertSeeText('Open Exceptions');
    }

    public function test_fulfillment_queue_renders_and_filters()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fulfillment.queue'));
        $response->assertStatus(200);
        $response->assertSeeText('Pending Fulfillment Queue');

        $staleResponse = $this->actingAs($this->admin)->get(route('admin.fulfillment.queue', ['filter' => 'stale']));
        $staleResponse->assertStatus(200);
    }

    public function test_fulfillment_shipments_renders()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.fulfillment.shipments'));
        $response->assertStatus(200);
        $response->assertSee('Shipments');
        $response->assertSee('Logistics');
    }

    public function test_fulfillment_exceptions_hub_renders_and_resolves()
    {
        $order = Order::create([
            'order_number' => 'ORD-EXC-' . uniqid(),
            'total_amount' => 75.00,
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        $fulfillment = Fulfillment::create([
            'order_id' => $order->id,
            'fulfillment_status' => 'EXCEPTION',
        ]);

        $exception = FulfillmentException::create([
            'fulfillment_id' => $fulfillment->id,
            'error_code' => 'ADDRESS_UNDELIVERABLE',
            'error_message' => 'Street name unverified by carrier API',
            'resolution_status' => 'OPEN',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.fulfillment.exceptions'));
        $response->assertStatus(200);
        $response->assertSeeText('Fulfillment Exceptions Hub');
        $response->assertSeeText('ADDRESS_UNDELIVERABLE');

        // Test resolving exception
        $resolveResponse = $this->actingAs($this->admin)->post(route('admin.fulfillment.resolve_exception', $exception->id));
        $resolveResponse->assertRedirect();

        $this->assertEquals('RESOLVED', $exception->fresh()->resolution_status);
    }
}
