<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderAddressesAndSupplierOrdersTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('order_addresses')) {
            Schema::create('order_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->string('type', 20)->default('shipping');
                $table->string('first_name', 100)->nullable();
                $table->string('last_name', 100)->nullable();
                $table->string('email', 150)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('address_line1', 255)->nullable();
                $table->string('address_line2', 255)->nullable();
                $table->string('city', 100)->nullable();
                $table->string('state', 100)->nullable();
                $table->string('country', 10)->default('US');
                $table->string('postal_code', 30)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('supplier_orders')) {
            Schema::create('supplier_orders', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->string('supplier', 50)->default('cj'); // 'cj', 'warehouse'
                $table->string('external_order_id', 100)->nullable()->index();
                $table->string('status', 50)->default('pending'); // pending, queued, submitting, submitted, shipped, delivered, failed
                $table->string('currency', 10)->default('USD');
                $table->decimal('product_cost', 10, 2)->default(0.00);
                $table->decimal('shipping_cost', 10, 2)->default(0.00);
                $table->decimal('total_cost', 10, 2)->default(0.00);
                $table->string('tracking_number', 100)->nullable();
                $table->string('carrier_name', 100)->nullable();
                $table->text('failure_message')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('orders') && !Schema::hasColumn('orders', 'fulfillment_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('fulfillment_status', 30)->default('pending')->after('payment_status');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('orders') && Schema::hasColumn('orders', 'fulfillment_status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('fulfillment_status');
            });
        }
        Schema::dropIfExists('supplier_orders');
        Schema::dropIfExists('order_addresses');
    }
}
