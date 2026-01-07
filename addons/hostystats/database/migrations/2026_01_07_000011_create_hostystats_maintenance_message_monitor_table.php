<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('hostystats_maintenance_message_monitor')) {
            return;
        }

        Schema::create('hostystats_maintenance_message_monitor', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maintenance_message_id');
            $table->unsignedBigInteger('monitor_id');

            $table->foreign('maintenance_message_id')
                ->references('id')->on('hostystats_maintenance_messages')
                ->onDelete('cascade');

            $table->foreign('monitor_id')
                ->references('id')->on('hostystats_monitors')
                ->onDelete('cascade');

            $table->unique(['maintenance_message_id', 'monitor_id'], 'hostystats_maint_msg_monitor_unique');
        });
    }

    public function down(): void
    {
        
        if (Schema::hasTable('hostystats_maintenance_message_monitor')) {
            Schema::drop('hostystats_maintenance_message_monitor');
        }
    }
};
