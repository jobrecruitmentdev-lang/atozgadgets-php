<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_order_confirmation_page_with_accurate_details()
    {
        $user = User::create([
            "first_name" => "Jane",
            "last_name" => "Buyer",
            "email" => "jane@example.com",
            "mobile" => "+14085551234",
            "password" => bcrypt("secret"),
            "role_id" => 1
        ]);

        $category = Category::create(["name" => "Wearables", "slug" => "wearables"]);

        $product = Product::create([
            "category_id" => $category->id,
            "name" => "Smart Band Pro",
            "slug" => "smart-band-pro",
            "sku" => "SKU-BAND-01",
            "price" => 49.99,
            "created_by" => $user->id
        ]);

        $order = Order::create([
            "user_id" => $user->id,
            "order_number" => "ORD-TEST-9988",
            "total_amount" => 49.99,
            "status" => "processing",
            "payment_status" => "paid",
            "shipping_address" => json_encode([
                "first_name" => "Jane",
                "last_name" => "Buyer",
                "address_line1" => "456 Market St",
                "city" => "San Jose",
                "state" => "CA",
                "postal_code" => "95131",
                "country" => "US"
            ])
        ]);

        OrderItem::create([
            "order_id" => $order->id,
            "product_id" => $product->id,
            "quantity" => 1,
            "unit_price" => 49.99,
            "total_price" => 49.99
        ]);

        $response = $this->get(route('store.order_confirmation', ['order_number' => $order->order_number]));

        $response->assertStatus(200);
        $response->assertSee('ORDER #ORD-TEST-9988');
        $response->assertSee('Smart Band Pro');
        $response->assertSee('$49.99');
        $response->assertSee('456 Market St');
        $response->assertSee('7–15 Business Days');
    }
}
