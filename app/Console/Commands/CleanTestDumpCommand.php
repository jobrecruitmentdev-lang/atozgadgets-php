<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\CjProduct;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\SupplierOrder;
use App\Models\OutboxEvent;
use App\Models\PaymentTransaction;
use App\Models\Order;
use App\Models\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CleanTestDumpCommand extends Command
{
    protected $signature = 'db:clean-dump';
    protected $description = 'Purge all test mock products, test categories, and temporary test artifacts from database';

    public function handle()
    {
        // 1. Purge Test Products
        $testPatterns = [
            '%Awesome Drone%', '%Drone%', '%Smart Track Pro%', '%Secure Gadget X%',
            '%<script%', '%Test Gadget%', '%E2E%', '%SKU-SMART-WATCH-TEST%',
            '%CJ-TRACK-%', '%SEC-%', '%DRONE-%', '%CJ-%', '%Test%',
            '%AtoZ Smart Hub Ultra%', '%Smart AI Earbuds Pro%', '%AtoZ Smart Watch Pro v2%',
            '%HUB-%', '%SKU-%'
        ];

        $prodCount = 0;
        foreach ($testPatterns as $pat) {
            $ids = Product::where('name', 'LIKE', $pat)->orWhere('sku', 'LIKE', $pat)->pluck('id');
            if ($ids->isNotEmpty()) {
                CjProduct::whereIn('internal_product_id', $ids)->delete();
                OrderItem::whereIn('product_id', $ids)->delete();
                Cart::whereIn('product_id', $ids)->delete();
                if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
                    \App\Models\ProductVariant::whereIn('product_id', $ids)->delete();
                }
                Product::whereIn('id', $ids)->delete();
                $prodCount += $ids->count();
            }
        }

        CjProduct::whereNotIn('internal_product_id', Product::pluck('id'))->delete();

        // 2. Deep Clean Test Categories (Purge any slug ending in hex/id or test patterns)
        $canonicalSlugs = [
            'tech-gadgets', 'electronics', 'audio-sound', 'drones-toys', 'smart-devices', 'home-kitchen', 'mobile-phones', 'smart-gadgets', 'tech', 'smart-electronics'
        ];

        $allCategories = Category::all();
        $catCount = 0;
        foreach ($allCategories as $cat) {
            $isTest = false;
            if (!in_array($cat->slug, $canonicalSlugs)) {
                if (preg_match('/-[0-9a-f]{6,}$/i', $cat->slug) || 
                    str_starts_with($cat->slug, 'cat-') || 
                    str_starts_with($cat->slug, 'test-') ||
                    str_contains($cat->slug, 'smart-devices-') ||
                    str_contains($cat->slug, 'tech-gadgets-') ||
                    str_contains($cat->slug, 'e2e') ||
                    str_contains($cat->slug, 'test')) {
                    $isTest = true;
                }
            }

            if ($isTest) {
                $prodIds = Product::where('category_id', $cat->id)->pluck('id');
                if ($prodIds->isNotEmpty()) {
                    CjProduct::whereIn('internal_product_id', $prodIds)->delete();
                    OrderItem::whereIn('product_id', $prodIds)->delete();
                    Cart::whereIn('product_id', $prodIds)->delete();
                    if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
                        \App\Models\ProductVariant::whereIn('product_id', $prodIds)->delete();
                    }
                    Product::whereIn('id', $prodIds)->delete();
                }
                $cat->delete();
                $catCount++;
            }
        }

        // Ensure canonical seed categories exist
        $canonicalSeeds = [
            ['name' => 'Smart Devices', 'slug' => 'smart-devices', 'is_active' => true, 'status' => 'active'],
            ['name' => 'Tech Gadgets', 'slug' => 'tech-gadgets', 'is_active' => true, 'status' => 'active'],
            ['name' => 'Audio & Sound', 'slug' => 'audio-sound', 'is_active' => true, 'status' => 'active'],
            ['name' => 'Drones & Toys', 'slug' => 'drones-toys', 'is_active' => true, 'status' => 'active'],
            ['name' => 'Electronics', 'slug' => 'electronics', 'is_active' => true, 'status' => 'active'],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'is_active' => true, 'status' => 'active'],
        ];

        foreach ($canonicalSeeds as $seed) {
            Category::firstOrCreate(['slug' => $seed['slug']], $seed);
        }

        // 3. Purge Test Orders & Orchestration Artifacts
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tablesToClear = [
            'cj_orders', 'shipments', 'checkout_sessions', 'payments', 'supplier_orders',
            'outbox_events', 'payment_transactions', 'order_addresses', 'order_items', 'orders',
            'provider_events', 'payment_attempts', 'idempotency_records', 'risk_assessments',
            'refunds', 'inventory_reservations', 'carts', 'cart_items'
        ];

        foreach ($tablesToClear as $tbl) {
            if (\Illuminate\Support\Facades\Schema::hasTable($tbl)) {
                DB::table($tbl)->truncate();
            }
        }

        // Clean orphaned product variants
        if (\Illuminate\Support\Facades\Schema::hasTable('product_variants')) {
            \App\Models\ProductVariant::whereNotIn('product_id', Product::pluck('id'))->delete();
        }

        // 4. Purge Test Users (Reassign existing product author to real admin first)
        $adminUser = User::where('email', 'admin@atozgadgets.com')->first() ?: User::first();
        if ($adminUser) {
            Product::where('created_by', '!=', $adminUser->id)->update(['created_by' => $adminUser->id]);
        }

        $preservedEmails = ['admin@atozgadgets.com', 'jobrecruitmentdev@gmail.com'];
        $userCount = User::where(function($q) {
            $q->where('email', 'LIKE', '%test%')
              ->orWhere('email', 'LIKE', '%@example.%')
              ->orWhere('email', 'LIKE', '%flow%')
              ->orWhere('email', 'LIKE', '%api_%')
              ->orWhere('email', 'LIKE', '%superadmin_%')
              ->orWhere('email', 'LIKE', '%staff_%')
              ->orWhere('email', 'LIKE', '%arch_v2_%')
              ->orWhere('email', 'LIKE', '%tower_%')
              ->orWhere('email', 'LIKE', '%admin_%');
        })->whereNotIn('email', $preservedEmails)->delete();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("Purged {$prodCount} test products, {$catCount} test categories, and {$userCount} test users. Database is pristine.");
        
        Artisan::call('optimize:clear');
        $this->info("All caches cleared successfully.");
    }
}

