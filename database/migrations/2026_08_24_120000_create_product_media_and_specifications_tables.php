<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('product_media')) {
            Schema::create('product_media', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variant_id')->nullable()->index();
                $table->string('type', 30)->default('image'); // image, gallery, video
                $table->text('url');
                $table->string('storage_path')->nullable();
                $table->string('alt_text')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_primary')->default(false);
                $table->string('mime_type', 50)->nullable();
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('product_specifications')) {
            Schema::create('product_specifications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->string('name', 150);
                $table->text('value');
                $table->integer('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('product_reviews')) {
            Schema::table('product_reviews', function (Blueprint $table) {
                if (!Schema::hasColumn('product_reviews', 'title')) {
                    $table->string('title')->nullable()->after('rating');
                }
                if (!Schema::hasColumn('product_reviews', 'status')) {
                    $table->string('status', 30)->default('approved')->after('review');
                }
                if (!Schema::hasColumn('product_reviews', 'verified_purchase')) {
                    $table->boolean('verified_purchase')->default(false)->after('status');
                }
                if (!Schema::hasColumn('product_reviews', 'admin_reply')) {
                    $table->text('admin_reply')->nullable()->after('verified_purchase');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_media');
        Schema::dropIfExists('product_specifications');
    }
};
