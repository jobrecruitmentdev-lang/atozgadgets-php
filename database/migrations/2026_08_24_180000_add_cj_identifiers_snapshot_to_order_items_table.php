<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCjIdentifiersSnapshotToOrderItemsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('order_items')) {
            Schema::table('order_items', function (Blueprint $table) {
                if (!Schema::hasColumn('order_items', 'cj_product_id')) {
                    $table->string('cj_product_id', 64)->nullable()->after('variant_name_snapshot');
                }
                if (!Schema::hasColumn('order_items', 'cj_variant_id')) {
                    $table->string('cj_variant_id', 64)->nullable()->after('cj_product_id');
                }
                if (!Schema::hasColumn('order_items', 'cj_variant_sku')) {
                    $table->string('cj_variant_sku', 100)->nullable()->after('cj_variant_id');
                }
            });
        }
    }

    public function down()
    {
        // Non-destructive down
    }
}
