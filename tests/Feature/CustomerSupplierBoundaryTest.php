<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\CjProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Shipment;
use App\Models\SupplierOrder;
use App\Http\Resources\Customer\ProductResource;
use App\Http\Resources\Customer\OrderResource as CustomerOrderResource;
use App\Http\Resources\Admin\AdminOrderResource;
use App\Services\Order\FulfillmentService;
use Illuminate\Support\Facades\Http;

class CustomerSupplierBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_product_resource_strictly_hides_supplier_internals()
    {
        $user = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin_boundary@example.com',
            'mobile' => '9876543211',
            'password' => bcrypt('password'),
        ]);

        $category = Category::create(['name' => 'Audio', 'slug' => 'audio', 'status' => 'active']);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Wireless Earbuds Pro',
            'slug' => 'wireless-earbuds-pro',
            'sku' => 'SKU-EARBUDS-PRO-01',
            'price' => 79.99,
            'discount_price' => 59.99,
            'stock_quantity' => 100,
            'status' => 'active',
            'fulfillment_type' => 'cj',
            'created_by' => $user->id,
        ]);

        CjProduct::create([
            'cj_product_id' => 'CJ-INTERNAL-SECRET-999',
            'internal_product_id' => $product->id,
            'cj_price' => 19.50,
            'status' => 'imported',
        ]);

        $resource = (new ProductResource($product))->toArray(request());

        // Assert customer resource only contains white-label fields
        $this->assertEquals('AtoZ Wireless Earbuds Pro', $resource['name']);
        $this->assertEquals(79.99, $resource['price']);
        $this->assertEquals(59.99, $resource['discount_price']);

        // Assert zero leakage of supplier keys or IDs
        $this->assertArrayNotHasKey('cj_product_id', $resource);
        $this->assertArrayNotHasKey('fulfillment_type', $resource);
        $this->assertArrayNotHasKey('cj_price', $resource);
        $this->assertArrayNotHasKey('supplier', $resource);
        $this->assertStringNotContainsString('CJ-', json_encode($resource));
    }

    public function test_customer_order_resource_uses_white_label_delivery_services()
    {
        $order = Order::create([
            'order_number' => 'AZG-TEST-1001',
            'subtotal' => 59.99,
            'tax_amount' => 0.00,
            'shipping_charge' => 0.00,
            'total_amount' => 59.99,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'carrier' => 'CJPacket Fast Line',
            'tracking_number' => 'TRK9988776655',
            'status' => 'in_transit',
        ]);

        $order->setRelation('shipment', $shipment);

        $resource = (new CustomerOrderResource($order))->toArray(request());

        $this->assertEquals('AZG-TEST-1001', $resource['order_number']);
        $this->assertEquals('Standard Delivery', $resource['shipment']['shipping_method']);
        $this->assertEquals('TRK9988776655', $resource['shipment']['tracking_number']);

        // Assert zero supplier names
        $this->assertStringNotContainsString('CJPacket', json_encode($resource));
        $this->assertStringNotContainsString('CJ-', json_encode($resource));
    }

    public function test_provider_agnostic_fulfillment_service_orchestration()
    {
        $user = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'mobile' => '9876543210',
            'password' => bcrypt('password'),
        ]);

        $category = Category::create(['name' => 'Gadgets', 'slug' => 'gadgets', 'status' => 'active']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'AtoZ Smart Lamp',
            'slug' => 'smart-lamp',
            'sku' => 'SKU-SMART-LAMP-01',
            'price' => 49.99,
            'fulfillment_type' => 'cj',
            'created_by' => $user->id,
        ]);

        CjProduct::create([
            'cj_product_id' => 'CJ-LAMP-123',
            'internal_product_id' => $product->id,
            'status' => 'imported',
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'AZG-TEST-2002',
            'subtotal' => 49.99,
            'total_amount' => 49.99,
            'status' => 'processing',
            'payment_status' => 'paid',
            'shipping_address' => json_encode([
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'address_line1' => '123 Tech Blvd',
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'US',
                'postal_code' => '10001',
            ]),
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'unit_price' => 49.99,
            'total_price' => 49.99,
        ]);

        \App\Models\Setting::set('cj_sandbox_mode', '0', 'cj');
        \App\Models\Setting::set('cj_api_email', 'admin@example.com', 'cj');
        \App\Models\Setting::set('cj_api_key', 'test_key', 'cj');
        \Illuminate\Support\Facades\Cache::put('cj_access_token', 'fake_token', 600);

        Http::fake([
            '*/shopping/order/createOrderV2*' => Http::response([
                'code' => 200,
                'result' => true,
                'data' => ['orderId' => 'CJ-LIVE-EXT-88899']
            ], 200),
            '*/shopping/order/submitOrder*' => Http::response(['code' => 200, 'result' => true], 200),
            '*/logistic/freightCalculate*' => Http::response([
                'code' => 200,
                'data' => [['logisticName' => 'CJPacket Fast Line']]
            ], 200)
        ]);

        $supplierOrder = FulfillmentService::fulfill($order, 'cj');

        $this->assertInstanceOf(SupplierOrder::class, $supplierOrder);
        $this->assertEquals('cj', $supplierOrder->supplier);
        $this->assertEquals('CJ-LIVE-EXT-88899', $supplierOrder->external_order_id);
        $this->assertEquals('submitted', $supplierOrder->status);
    }
}
