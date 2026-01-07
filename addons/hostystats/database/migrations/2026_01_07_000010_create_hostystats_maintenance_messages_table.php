<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hostystats_maintenance_messages', function (Blueprint $table) {
            $table->id();

            $table->boolean('is_active')->default(false);
            $table->boolean('show_on_client')->default(true);
            $table->boolean('show_on_admin')->default(true);

            $table->string('severity', 20)->default('yellow');
            $table->string('title', 160);
            $table->text('description')->nullable();

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hostystats_maintenance_messages');
    }
};
