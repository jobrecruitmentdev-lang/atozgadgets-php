<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAdversarialE2ETest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $staffUser;
    protected User $customerUser;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // 1 = SuperAdmin, 2 = Staff, 3 = Customer
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin_' . uniqid() . '@atozgadgets.com',
            'role_id' => 1,
            'is_active' => true,
        ]);

        $this->staffUser = User::factory()->create([
            'email' => 'staff_' . uniqid() . '@atozgadgets.com',
            'role_id' => 2,
            'is_active' => true,
        ]);

        $this->customerUser = User::factory()->create([
            'email' => 'customer_' . uniqid() . '@example.com',
            'role_id' => 3,
            'is_active' => true,
        ]);

        $this->category = Category::firstOrCreate(['slug' => 'smart-gadgets'], [
            'name' => 'Smart Gadgets',
            'status' => 'active',
        ]);
    }

    public function test_unauthenticated_users_are_redirected_or_blocked_from_admin()
    {
        $adminRoutes = [
            '/admin',
            '/admin/orders',
            '/admin/customers',
            '/admin/settings',
            '/admin/reports',
            '/admin/catalog/products',
            '/admin/catalog/categories',
            '/admin/catalog/brands',
            '/admin/catalog/import',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_customer_role_is_strictly_forbidden_from_admin_panel()
    {
        $this->actingAs($this->customerUser);

        $adminRoutes = [
            '/admin',
            '/admin/orders',
            '/admin/customers',
            '/admin/settings',
            '/admin/reports',
            '/admin/catalog/products',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(403);
        }
    }

    public function test_staff_cannot_escalate_privileges_to_superadmin()
    {
        $this->actingAs($this->staffUser);

        $targetUser = User::factory()->create(['role_id' => 3]);

        $response = $this->put(route('admin.customers.update', $targetUser->id), [
            'first_name' => 'Attacker',
            'last_name' => 'User',
            'email' => $targetUser->email,
            'role_id' => 1,
            'is_active' => 1,
        ]);

        $response->assertSessionHas('error');
        $targetUser->refresh();
        $this->assertEquals(3, $targetUser->role_id, 'Role must not be elevated to SuperAdmin by Staff');
    }

    public function test_superadmin_can_update_user_and_role()
    {
        $this->actingAs($this->superAdmin);

        $targetUser = User::factory()->create(['role_id' => 3]);

        $response = $this->put(route('admin.customers.update', $targetUser->id), [
            'first_name' => 'Promoted',
            'last_name' => 'User',
            'email' => $targetUser->email,
            'role_id' => 2,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.customers'));
        $targetUser->refresh();
        $this->assertEquals(2, $targetUser->role_id);
        $this->assertEquals('Promoted', $targetUser->first_name);
    }

    public function test_dashboard_renders_metrics_and_sales_trend_without_errors()
    {
        $this->actingAs($this->superAdmin);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'AtoZ Drone 4K',
            'slug' => 'atoz-drone-4k-' . uniqid(),
            'sku' => 'DRONE-' . strtoupper(uniqid()),
            'price' => 299.99,
            'stock_quantity' => 2,
            'status' => 'active',
            'is_active' => true,
            'created_by' => $this->superAdmin->id,
        ]);

        $order = Order::create([
            'user_id' => $this->customerUser->id,
            'order_number' => 'ORD-' . time(),
            'subtotal' => 299.99,
            'total_amount' => 299.99,
            'status' => 'processing',
            'payment_status' => 'paid',
        ]);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('recentOrders');
    }

    public function test_unpaid_order_is_blocked_from_cj_fulfillment_dispatch()
    {
        $this->actingAs($this->superAdmin);

        $unpaidOrder = Order::create([
            'user_id' => $this->customerUser->id,
            'order_number' => 'ORD-UNPAID-' . uniqid() . '-' . mt_rand(1000, 9999),
            'subtotal' => 150.00,
            'total_amount' => 150.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->post(route('admin.orders.fulfill_cj', $unpaidOrder->id));
        $response->assertSessionHas('error');
        
        $unpaidOrder->refresh();
        $this->assertNotEquals('processing', $unpaidOrder->status);
    }

    public function test_order_destroy_safely_cancels_instead_of_hard_deleting()
    {
        $this->actingAs($this->superAdmin);

        $order = Order::create([
            'user_id' => $this->customerUser->id,
            'order_number' => 'ORD-CANCEL-' . time(),
            'subtotal' => 99.00,
            'total_amount' => 99.00,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        $response = $this->delete(route('admin.orders.destroy', $order->id));
        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_product_crud_flow_and_validation()
    {
        $this->actingAs($this->superAdmin);

        $productSlug = 'smart-watch-pro-' . uniqid();
        $response = $this->post(route('admin.catalog.products.store'), [
            'name' => 'AtoZ Smart Watch Pro',
            'slug' => $productSlug,
            'description' => 'Flagship smartwatch with OLED display',
            'price' => 149.99,
            'stock_quantity' => 50,
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('admin.catalog.products'));
        $this->assertDatabaseHas('products', [
            'slug' => $productSlug,
            'price' => 149.99,
            'stock_quantity' => 50,
            'fulfillment_type' => 'own',
        ]);

        $product = Product::where('slug', $productSlug)->firstOrFail();

        $response = $this->put(route('admin.catalog.products.update', $product->id), [
            'name' => 'AtoZ Smart Watch Pro v2',
            'slug' => $productSlug,
            'description' => 'Updated flagship smartwatch',
            'price' => 179.99,
            'stock_quantity' => 75,
            'category_id' => $this->category->id,
        ]);

        $response->assertRedirect(route('admin.catalog.products'));
        $product->refresh();
        $this->assertEquals(179.99, (float)$product->price);
        $this->assertEquals(75, $product->stock_quantity);
    }

    public function test_settings_blocks_arbitrary_keys_and_updates_allowed_settings()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->post(route('admin.settings.update'), [
            'store_name' => 'AtoZGadgets Official Store',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'malicious_key_injection' => 'exploit_value',
            '__proto__' => 'polluted',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('AtoZGadgets Official Store', Setting::get('store_name'));
        $this->assertNull(Setting::get('malicious_key_injection'));
        $this->assertNull(Setting::get('__proto__'));
    }

    public function test_reports_csv_exports_stream_valid_csv_for_all_types()
    {
        $this->actingAs($this->superAdmin);

        $types = ['orders', 'inventory', 'customers'];

        foreach ($types as $type) {
            $response = $this->get(route('admin.reports.export', $type));
            $response->assertStatus(200);
            $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
            $this->assertStringContainsString("attachment; filename=\"atozgadgets_{$type}_report_", $response->headers->get('Content-Disposition'));
        }
    }
}
