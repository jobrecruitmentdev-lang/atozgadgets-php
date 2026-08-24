<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProviderEvent;
use App\Models\CjOrder;
use App\Services\Cj\CjOrderService;
use App\Services\Cj\CjAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ProductionFailureDrillTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;
    protected $product;
    protected $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            "first_name" => "Drill",
            "last_name" => "User",
            "email" => "drill@atozgadgets.test",
            "mobile" => "+15559876543",
            "password" => bcrypt("secret123"),
            "role_id" => 3,
            "is_active" => 1
        ]);

        $this->category = Category::create([
            "name" => "Drill Category",
            "slug" => "drill-category",
            "status" => "active"
        ]);

        $this->product = Product::create([
            "category_id" => $this->category->id,
            "name" => "AtoZ Noise Cancelling Earbuds",
            "slug" => "atoz-earbuds-drill",
            "sku" => "AZG-EAR-001",
            "price" => 59.99,
            "stock_quantity" => 1,
            "status" => "active",
            "is_active" => 1,
            "fulfillment_type" => "cj",
            "created_by" => $this->user->id
        ]);

        $this->variant = ProductVariant::create([
            "product_id" => $this->product->id,
            "name" => "Matte Black",
            "sku" => "AZG-EAR-001-BLK",
            "selling_price" => 59.99,
            "cost_price" => 20.00,
            "stock_quantity" => 1,
            "cj_variant_id" => "CJ-VID-DRIL-991",
            "status" => "active"
        ]);
    }

    /** @test */
    public function drill_test_1_customer_checkout_snapshots_and_stock_decrement()
    {
        $order = DB::transaction(function () {
            $ord = Order::create([
                "order_number" => "ORD-DRILL-001",
                "user_id" => $this->user->id,
                "total_amount" => 59.99,
                "status" => "processing",
                "payment_status" => "paid",
                "shipping_address" => json_encode(["country" => "US", "city" => "Dallas"])
            ]);

            OrderItem::create([
                "order_id" => $ord->id,
                "product_id" => $this->product->id,
                "variant_id" => $this->variant->id,
                "merchant_sku_snapshot" => $this->variant->sku,
                "product_name_snapshot" => $this->product->name,
                "variant_name_snapshot" => $this->variant->name,
                "cj_variant_id" => $this->variant->cj_variant_id,
                "quantity" => 1,
                "unit_price" => 59.99,
                "total_price" => 59.99,
                "status" => "active"
            ]);

            return $ord;
        });

        $item = $order->items->first();
        $this->assertEquals("AZG-EAR-001-BLK", $item->merchant_sku_snapshot);
        $this->assertEquals("CJ-VID-DRIL-991", $item->cj_variant_id);
        $this->assertEquals("Matte Black", $item->variant_name_snapshot);
    }

    /** @test */
    public function drill_test_3_response_loss_preflight_reconciliation_prevents_duplicate_cj_orders()
    {
        $order = Order::create([
            "order_number" => "ORD-DROP-RECOVERY-88",
            "user_id" => $this->user->id,
            "total_amount" => 59.99,
            "status" => "processing",
            "payment_status" => "paid",
            "shipping_address" => json_encode([
                "first_name" => "John",
                "last_name" => "Doe",
                "address1" => "123 Tech Blvd",
                "city" => "Austin",
                "state" => "TX",
                "postal_code" => "78701",
                "country" => "US",
                "phone" => "5551234567"
            ])
        ]);

        OrderItem::create([
            "order_id" => $order->id,
            "product_id" => $this->product->id,
            "variant_id" => $this->variant->id,
            "cj_variant_id" => "CJ-VID-DRIL-991",
            "quantity" => 1,
            "unit_price" => 59.99,
            "total_price" => 59.99,
            "status" => "active"
        ]);

        Http::fake([
            "*/shopping/order/list*" => Http::response([
                "code" => 200,
                "result" => true,
                "data" => [
                    "list" => [
                        ["orderId" => "CJ-RECOVERED-998877", "orderNum" => $order->order_number]
                    ]
                ]
            ], 200)
        ]);

        $result = CjOrderService::placeOrder($order->id);

        $this->assertEquals("CJ-RECOVERED-998877", $result["cjOrderId"]);
        $this->assertDatabaseHas("cj_orders", [
            "internal_order_id" => $order->id,
            "cj_order_id" => "CJ-RECOVERED-998877",
            "status" => "submitted"
        ]);
    }

    /** @test */
    public function drill_test_4_duplicate_paypal_webhook_is_deduplicated_by_unique_event_id()
    {
        $webhookPayload = [
            "id" => "WH-PAYPAL-UNIQUE-EVENT-101",
            "event_type" => "PAYMENT.CAPTURE.COMPLETED",
            "resource" => [
                "id" => "CAPTURE-999",
                "custom_id" => "ORD-TEST-001",
                "amount" => ["value" => "59.99", "currency_code" => "USD"]
            ]
        ];

        $response1 = $this->postJson("/api/webhooks/paypal", $webhookPayload);
        $response1->assertStatus(200);

        $response2 = $this->postJson("/api/webhooks/paypal", $webhookPayload);
        $response2->assertStatus(200);

        $eventCount = ProviderEvent::where("event_id", "WH-PAYPAL-UNIQUE-EVENT-101")->count();
        $this->assertEquals(1, $eventCount);
    }
}
