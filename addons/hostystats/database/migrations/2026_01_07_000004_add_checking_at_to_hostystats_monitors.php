<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hostystats_monitors', function (Blueprint $table) {
            $table->timestamp('checking_at')->nullable()->after('last_checked_at');
            $table->index('checking_at');
        });
    }

    public function down(): void
    {
        Schema::table('hostystats_monitors', function (Blueprint $table) {
            $table->dropIndex(['checking_at']);
            $table->dropColumn('checking_at');
        });
    }
};
