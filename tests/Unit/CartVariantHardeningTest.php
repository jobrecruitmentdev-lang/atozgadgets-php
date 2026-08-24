<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartVariantHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected $category;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            "first_name" => "Test",
            "last_name" => "User",
            "email" => "test@atozgadgets.com",
            "mobile" => "+1234567890",
            "password" => bcrypt("secret"),
            "role_id" => 1
        ]);
        $this->category = Category::create(["name" => "Tech", "slug" => "tech"]);
    }

    /** @test */
    public function it_rejects_variant_belonging_to_another_product()
    {
        $prod1 = Product::create([
            "category_id" => $this->category->id,
            "name" => "Product 1",
            "slug" => "prod-1",
            "sku" => "SKU-P1",
            "price" => 50.00,
            "created_by" => $this->user->id
        ]);
        $prod2 = Product::create([
            "category_id" => $this->category->id,
            "name" => "Product 2",
            "slug" => "prod-2",
            "sku" => "SKU-P2",
            "price" => 100.00,
            "created_by" => $this->user->id
        ]);

        $variant2 = ProductVariant::create([
            "product_id" => $prod2->id,
            "name" => "Variant for Product 2",
            "sku" => "SKU-PROD2-V1",
            "selling_price" => 99.00,
            "stock_quantity" => 10,
        ]);

        $response = $this->post(route("store.cart.add"), [
            "product_id" => $prod1->id,
            "variant_id" => $variant2->id,
            "quantity" => 1,
        ]);

        $response->assertSessionHas("error");
        $this->assertEmpty(session("cart"));
    }

    /** @test */
    public function it_uses_authoritative_database_price_and_ignores_manipulation()
    {
        $prod = Product::create([
            "category_id" => $this->category->id,
            "name" => "Product Test",
            "slug" => "prod-test",
            "sku" => "SKU-TEST",
            "price" => 50.00,
            "created_by" => $this->user->id
        ]);

        $variant = ProductVariant::create([
            "product_id" => $prod->id,
            "name" => "Midnight Black",
            "sku" => "SKU-BLACK-01",
            "selling_price" => 45.00,
            "stock_quantity" => 10,
            "cj_variant_id" => "CJ-VID-9988",
        ]);

        $response = $this->post(route("store.cart.add"), [
            "product_id" => $prod->id,
            "variant_id" => $variant->id,
            "quantity" => 2,
            "price" => 0.01,
        ]);

        $response->assertSessionHas("success");
        $cart = session("cart");
        $cartKey = "{$prod->id}_{$variant->id}";

        $this->assertArrayHasKey($cartKey, $cart);
        $this->assertEquals(45.00, $cart[$cartKey]["price"]);
        $this->assertEquals(2, $cart[$cartKey]["quantity"]);
        $this->assertEquals("CJ-VID-9988", $cart[$cartKey]["cj_variant_id"]);
    }

    /** @test */
    public function multiple_variants_of_same_product_coexist_in_cart()
    {
        $prod = Product::create([
            "category_id" => $this->category->id,
            "name" => "Product Scarf",
            "slug" => "prod-scarf",
            "sku" => "SKU-SCARF",
            "price" => 50.00,
            "created_by" => $this->user->id
        ]);

        $variantA = ProductVariant::create([
            "product_id" => $prod->id,
            "name" => "Grey / 70x70",
            "sku" => "SKU-GREY",
            "selling_price" => 40.00,
            "stock_quantity" => 10
        ]);

        $variantB = ProductVariant::create([
            "product_id" => $prod->id,
            "name" => "Black / 70x70",
            "sku" => "SKU-BLACK",
            "selling_price" => 45.00,
            "stock_quantity" => 10
        ]);

        $this->post(route("store.cart.add"), [
            "product_id" => $prod->id,
            "variant_id" => $variantA->id,
            "quantity" => 1,
        ]);

        $this->post(route("store.cart.add"), [
            "product_id" => $prod->id,
            "variant_id" => $variantB->id,
            "quantity" => 2,
        ]);

        $cart = session("cart");
        $this->assertCount(2, $cart);
        $this->assertArrayHasKey("{$prod->id}_{$variantA->id}", $cart);
        $this->assertArrayHasKey("{$prod->id}_{$variantB->id}", $cart);
    }
}
