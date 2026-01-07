<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hostystats_monitors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('hostystats_categories')
                ->cascadeOnDelete();

            $table->string('name', 160);
            $table->text('description')->nullable();

            
            $table->string('type', 20)->default('http');

            
            $table->string('target', 255);

            
            $table->unsignedSmallInteger('expected_http_code')->nullable();
            $table->unsignedInteger('degraded_threshold_ms')->default(800);

            
            $table->unsignedInteger('timeout_ms')->default(3000);
            $table->unsignedInteger('interval_sec')->default(60);

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);

            
            $table->string('forced_status', 20)->nullable();

            
            $table->string('last_status', 20)->nullable();
            $table->unsignedInteger('last_response_time_ms')->nullable();
            $table->unsignedSmallInteger('last_http_code')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamp('last_checked_at')->nullable();

            $table->timestamps();

            $table->index(['category_id', 'position']);
            $table->index(['is_active', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostystats_monitors');
    }
};
