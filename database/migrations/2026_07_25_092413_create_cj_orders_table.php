<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCjOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('cj_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internal_order_id')->constrained('orders')->onDelete('cascade');
            $table->string('cj_order_id', 100)->nullable()->unique();
            $table->string('status', 50)->default('pending');
            $table->string('tracking_number', 150)->nullable();
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->decimal('cj_total_amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cj_orders');
    }
}
