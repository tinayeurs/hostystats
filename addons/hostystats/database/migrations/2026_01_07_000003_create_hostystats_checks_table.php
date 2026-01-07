<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hostystats_checks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitor_id')
                ->constrained('hostystats_monitors')
                ->cascadeOnDelete();

            $table->string('status', 20);
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->unsignedSmallInteger('http_code')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('checked_at');

            $table->timestamps();

            $table->index(['monitor_id', 'checked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostystats_checks');
    }
};
