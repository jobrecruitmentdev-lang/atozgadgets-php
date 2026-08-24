<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFulfillmentControlTowerTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fulfillment_providers')) {
            Schema::create('fulfillment_providers', function (Blueprint $table) {
                $table->id();
                $table->string('code', 64)->unique();
                $table->string('name', 128);
                $table->string('type', 64)->default('supplier');
                $table->boolean('enabled')->default(true);
                $table->json('configuration')->nullable();
                $table->timestamps();
            });

            // Seed default providers
            DB::table('fulfillment_providers')->insert([
                [
                    'code' => 'cj',
                    'name' => 'CJ Dropshipping',
                    'type' => 'supplier',
                    'enabled' => true,
                    'configuration' => json_encode(['sandbox' => true]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'manual',
                    'name' => 'Manual Dispatch',
                    'type' => 'in_house',
                    'enabled' => true,
                    'configuration' => json_encode([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (!Schema::hasTable('shipment_carriers')) {
            Schema::create('shipment_carriers', function (Blueprint $table) {
                $table->id();
                $table->string('internal_code', 64)->unique();
                $table->string('customer_name', 128);
                $table->string('tracking_url_template', 255)->nullable();
                $table->boolean('enabled')->default(true);
                $table->timestamps();
            });

            // Seed default white-labeled carriers
            DB::table('shipment_carriers')->insert([
                [
                    'internal_code' => 'standard',
                    'customer_name' => 'Standard Delivery',
                    'tracking_url_template' => 'https://track.aftership.com/{tracking_number}',
                    'enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'internal_code' => 'express',
                    'customer_name' => 'Express Priority',
                    'tracking_url_template' => 'https://track.aftership.com/{tracking_number}',
                    'enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (!Schema::hasTable('fulfillments')) {
            Schema::create('fulfillments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('provider_id')->nullable()->index();
                $table->string('fulfillment_status', 64)->default('PENDING')->index();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('fulfillment_items')) {
            Schema::create('fulfillment_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fulfillment_id')->index();
                $table->unsignedBigInteger('order_item_id')->index();
                $table->integer('quantity')->default(1);
                $table->string('status', 64)->default('PENDING');
                $table->timestamps();

                $table->foreign('fulfillment_id')->references('id')->on('fulfillments')->onDelete('cascade');
                $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('fulfillment_attempts')) {
            Schema::create('fulfillment_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fulfillment_id')->index();
                $table->string('idempotency_key', 128)->unique();
                $table->integer('attempt_number')->default(1);
                $table->string('status', 64)->default('IN_PROGRESS'); // IN_PROGRESS, SUCCESS, FAILED
                $table->string('request_hash', 64)->nullable();
                $table->text('response_payload')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->foreign('fulfillment_id')->references('id')->on('fulfillments')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('fulfillment_exceptions')) {
            Schema::create('fulfillment_exceptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fulfillment_id')->index();
                $table->string('error_code', 64)->default('UNKNOWN');
                $table->text('error_message');
                $table->json('context_payload')->nullable();
                $table->string('resolution_status', 64)->default('OPEN')->index(); // OPEN, RESOLVED, IGNORED
                $table->unsignedBigInteger('resolved_by')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->foreign('fulfillment_id')->references('id')->on('fulfillments')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('product_histories')) {
            Schema::create('product_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action_type', 64); // PRICE_CHANGE, INVENTORY_SYNC, STATUS_CHANGE, CONTENT_UPDATE, MARGIN_WARNING
                $table->json('before_state')->nullable();
                $table->json('after_state')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 128);
                $table->text('details')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->timestamps();
            });
        }

        // Ensure shipments table has fulfillment and white-label carrier columns
        if (Schema::hasTable('shipments')) {
            Schema::table('shipments', function (Blueprint $table) {
                if (!Schema::hasColumn('shipments', 'fulfillment_id')) {
                    $table->unsignedBigInteger('fulfillment_id')->nullable()->index();
                }
                if (!Schema::hasColumn('shipments', 'carrier_id')) {
                    $table->unsignedBigInteger('carrier_id')->nullable()->index();
                }
                if (!Schema::hasColumn('shipments', 'customer_carrier_name')) {
                    $table->string('customer_carrier_name', 128)->nullable();
                }
            });
        }

        // Check if product_reviews has moderation, verified, and token columns
        if (Schema::hasTable('product_reviews')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('product_reviews', 'title')) {
                    $table->string('title', 255)->nullable();
                }
                if (!Schema::hasColumn('product_reviews', 'status')) {
                    $table->string('status', 32)->default('pending')->index();
                }
                if (!Schema::hasColumn('product_reviews', 'verified_purchase')) {
                    $table->boolean('verified_purchase')->default(false)->index();
                }
                if (!Schema::hasColumn('product_reviews', 'review_token')) {
                    $table->string('review_token', 64)->nullable()->index();
                }
                if (!Schema::hasColumn('product_reviews', 'order_id')) {
                    $table->unsignedBigInteger('order_id')->nullable()->index();
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_histories');
        Schema::dropIfExists('fulfillment_exceptions');
        Schema::dropIfExists('fulfillment_attempts');
        Schema::dropIfExists('fulfillment_items');
        Schema::dropIfExists('fulfillments');
        Schema::dropIfExists('shipment_carriers');
        Schema::dropIfExists('fulfillment_providers');
    }
}
