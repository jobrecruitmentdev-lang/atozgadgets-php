<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommerceCoreAndPaymentOrchestrationTables extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('checkout_sessions')) {
            Schema::create('checkout_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_token')->unique();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('currency', 3)->default('USD');
                $table->string('country', 2)->default('US');
                $table->json('line_items');
                $table->decimal('subtotal', 10, 2)->default(0.00);
                $table->decimal('discount', 10, 2)->default(0.00);
                $table->decimal('shipping', 10, 2)->default(0.00);
                $table->decimal('tax', 10, 2)->default(0.00);
                $table->decimal('grand_total', 10, 2)->default(0.00);
                $table->string('status', 30)->default('active'); // active, converted, expired
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_provider_accounts')) {
            Schema::create('payment_provider_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('provider', 30)->index(); // paypal, stripe
                $table->string('environment', 20)->default('sandbox'); // sandbox, live
                $table->string('display_name', 100);
                $table->text('client_id')->nullable();
                $table->text('client_secret_encrypted')->nullable();
                $table->text('webhook_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_default')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_attempts')) {
            Schema::create('payment_attempts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('payment_id')->nullable()->index();
                $table->string('provider', 30);
                $table->string('provider_order_id')->nullable()->index();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->string('status', 30)->default('initiated'); // initiated, pending, success, failed
                $table->string('failure_code')->nullable();
                $table->text('failure_message')->nullable();
                $table->string('idempotency_key')->nullable()->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_transactions')) {
            Schema::create('payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payment_id')->nullable()->index();
                $table->unsignedBigInteger('order_id')->index();
                $table->string('type', 30); // PAYMENT, AUTHORIZATION, CAPTURE, REFUND
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->string('provider', 30);
                $table->string('provider_transaction_id')->unique();
                $table->string('status', 30)->default('completed');
                $table->json('metadata')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('provider_events')) {
            Schema::create('provider_events', function (Blueprint $table) {
                $table->id();
                $table->string('provider', 30)->index();
                $table->string('event_id')->unique();
                $table->string('event_type', 100);
                $table->json('payload');
                $table->boolean('signature_verified')->default(false);
                $table->string('processing_status', 30)->default('RECEIVED'); // RECEIVED, PROCESSED, FAILED, DEAD_LETTER
                $table->integer('attempts')->default(0);
                $table->timestamp('processed_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('idempotency_records')) {
            Schema::create('idempotency_records', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->string('operation', 50);
                $table->string('resource_type', 50)->nullable();
                $table->unsignedBigInteger('resource_id')->nullable();
                $table->string('request_hash', 64)->nullable();
                $table->integer('response_status')->nullable();
                $table->json('response_body')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('risk_assessments')) {
            Schema::create('risk_assessments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('payment_id')->nullable()->index();
                $table->integer('risk_score')->default(0); // 0-100
                $table->string('risk_level', 20)->default('LOW'); // LOW, MEDIUM, HIGH
                $table->json('signals')->nullable();
                $table->string('decision', 20)->default('APPROVE'); // APPROVE, REVIEW, REJECT
                $table->timestamp('created_at')->useCurrent();
            });
        }

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
            });
        }

        if (!Schema::hasTable('outbox_events')) {
            Schema::create('outbox_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_name', 100)->index();
                $table->string('aggregate_type', 50);
                $table->unsignedBigInteger('aggregate_id')->index();
                $table->json('payload');
                $table->string('status', 30)->default('PENDING'); // PENDING, PROCESSED, FAILED
                $table->integer('attempts')->default(0);
                $table->text('error_message')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('outbox_events', 'processed_at')) {
                Schema::table('outbox_events', function (Blueprint $table) {
                    $table->timestamp('processed_at')->nullable();
                });
            }
            if (!Schema::hasColumn('outbox_events', 'error_message')) {
                Schema::table('outbox_events', function (Blueprint $table) {
                    $table->text('error_message')->nullable();
                });
            }
        }

        if (!Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id')->index();
                $table->unsignedBigInteger('payment_transaction_id')->nullable()->index();
                $table->string('refund_id')->nullable()->unique();
                $table->decimal('amount', 10, 2);
                $table->string('currency', 3)->default('USD');
                $table->text('reason')->nullable();
                $table->string('status', 30)->default('completed');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('outbox_events');
        Schema::dropIfExists('inventory_reservations');
        Schema::dropIfExists('risk_assessments');
        Schema::dropIfExists('idempotency_records');
        Schema::dropIfExists('provider_events');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_attempts');
        Schema::dropIfExists('payment_provider_accounts');
        Schema::dropIfExists('checkout_sessions');
    }
}
