<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOutboxLocksAndOrderSnapshots extends Migration
{
    public function up()
    {
        // 1. Harden outbox_events with atomic worker locks and error classification
        if (Schema::hasTable('outbox_events')) {
            Schema::table('outbox_events', function (Blueprint $table) {
                if (!Schema::hasColumn('outbox_events', 'claimed_at')) {
                    $table->timestamp('claimed_at')->nullable()->after('status');
                }
                if (!Schema::hasColumn('outbox_events', 'claimed_by')) {
                    $table->string('claimed_by', 128)->nullable()->after('claimed_at');
                }
                if (!Schema::hasColumn('outbox_events', 'last_attempt_at')) {
                    $table->timestamp('last_attempt_at')->nullable()->after('attempts');
                }
                if (!Schema::hasColumn('outbox_events', 'next_attempt_at')) {
                    $table->timestamp('next_attempt_at')->nullable()->after('last_attempt_at');
                }
                if (!Schema::hasColumn('outbox_events', 'last_error_code')) {
                    $table->string('last_error_code', 64)->nullable()->after('error_message');
                }
            });
        }

        // 2. Harden order_items with immutable commercial and wholesale snapshots
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'merchant_sku_snapshot')) {
                    $table->string('merchant_sku_snapshot', 64)->nullable()->after('variant_id');
                }
                if (!Schema::hasColumn('order_items', 'product_name_snapshot')) {
                    $table->string('product_name_snapshot', 255)->nullable()->after('merchant_sku_snapshot');
                }
                if (!Schema::hasColumn('order_items', 'variant_name_snapshot')) {
                    $table->string('variant_name_snapshot', 150)->nullable()->after('product_name_snapshot');
                }
                if (!Schema::hasColumn('order_items', 'supplier_cost_snapshot')) {
                    $table->decimal('supplier_cost_snapshot', 10, 2)->default(0.00)->after('unit_price');
                }
                if (!Schema::hasColumn('order_items', 'freight_cost_snapshot')) {
                    $table->decimal('freight_cost_snapshot', 10, 2)->default(0.00)->after('supplier_cost_snapshot');
                }
                if (!Schema::hasColumn('order_items', 'contribution_margin_snapshot')) {
                    $table->decimal('contribution_margin_snapshot', 10, 2)->default(0.00)->after('freight_cost_snapshot');
                }
            });
        }

        // 3. Ensure inventory_reservations table exists
        if (!Schema::hasTable('inventory_reservations')) {
            Schema::create('inventory_reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable();
                $table->integer('quantity')->default(1);
                $table->string('status', 30)->default('RESERVED'); // RESERVED, CONFIRMED, RELEASED
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['product_id', 'variant_id', 'status', 'expires_at'], 'idx_inv_res_lookup');
            });
        }
    }

    public function down()
    {
        // Non-destructive down in accordance with zero-data-loss policy
    }
}
