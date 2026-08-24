<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessedAtToOutboxEventsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable("outbox_events")) {
            Schema::table("outbox_events", function (Blueprint $table) {
                if (!Schema::hasColumn("outbox_events", "processed_at")) {
                    $table->timestamp("processed_at")->nullable()->after("attempts");
                }
                if (!Schema::hasColumn("outbox_events", "error_message")) {
                    $table->text("error_message")->nullable()->after("processed_at");
                }
            });
        }
    }

    public function down()
    {
    }
}
