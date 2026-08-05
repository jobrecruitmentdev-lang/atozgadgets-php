<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MigratePostgresToMysql extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:pg2mysql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates data from legacy PostgreSQL to new MySQL database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting database migration from PostgreSQL to MySQL...');
        
        // Note: You must define a 'pgsql' connection in config/database.php
        // matching the old Node.js backend credentials.
        try {
            DB::connection('pgsql')->getPdo();
        } catch (\Exception $e) {
            $this->error('Could not connect to PostgreSQL. Please configure the "pgsql" connection in config/database.php.');
            return 1;
        }

        // 1. Migrate Users
        $this->info('Migrating Users...');
        DB::connection('pgsql')->table('User')->orderBy('id')->chunk(500, function ($pgUsers) {
            foreach ($pgUsers as $u) {
                DB::table('users')->updateOrInsert(
                    ['id' => $u->id],
                    [
                        'first_name' => $u->first_name,
                        'last_name' => $u->last_name,
                        'email' => $u->email,
                        'mobile' => $u->mobile,
                        'password' => $u->password_hash,
                        'role_id' => $u->role_id,
                        'created_at' => $u->created_at,
                        'updated_at' => $u->updated_at,
                    ]
                );
            }
        });

        // 2. Migrate Categories
        $this->info('Migrating Categories...');
        DB::connection('pgsql')->table('Category')->orderBy('id')->chunk(500, function ($pgCategories) {
            foreach ($pgCategories as $c) {
                DB::table('categories')->updateOrInsert(
                    ['id' => $c->id],
                    [
                        'name' => $c->name,
                        'slug' => $c->slug,
                        'status' => $c->status,
                        'created_at' => $c->created_at,
                        'updated_at' => $c->updated_at,
                    ]
                );
            }
        });

        // 3. Migrate Products
        $this->info('Migrating Products...');
        DB::connection('pgsql')->table('Product')->orderBy('id')->chunk(500, function ($pgProducts) {
            foreach ($pgProducts as $p) {
                DB::table('products')->updateOrInsert(
                    ['id' => $p->id],
                    [
                        'category_id' => $p->category_id,
                        'sub_category_id' => $p->sub_category_id,
                        'name' => $p->name,
                        'slug' => $p->slug,
                        'description' => $p->description,
                        'price' => $p->price,
                        'discount_price' => $p->discount_price,
                        'fulfillment_type' => $p->fulfillment_type,
                        'thumbnail_image' => $p->thumbnail_image,
                        'created_at' => clone $p->created_at,
                        'updated_at' => clone $p->updated_at,
                    ]
                );
            }
        });
        
        // 4. Migrate CJ Products
        $this->info('Migrating CJ Products...');
        DB::connection('pgsql')->table('CjProduct')->orderBy('id')->chunk(500, function ($pgCjProducts) {
            foreach ($pgCjProducts as $cj) {
                DB::table('cj_products')->updateOrInsert(
                    ['id' => $cj->id],
                    [
                        'product_id' => $cj->product_id,
                        'cj_pid' => $cj->cj_pid,
                        'cj_vid' => $cj->cj_vid,
                        'cj_sku' => $cj->cj_sku,
                        'original_price' => $cj->original_price,
                        'inventory_quantity' => $cj->inventory_quantity,
                    ]
                );
            }
        });
        
        $this->info('Migration completed successfully!');
        return 0;
    }
}
