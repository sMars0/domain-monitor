<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('domain_checks', function (Blueprint $table) {
            // Composite index for fast paginated history queries per domain.
            $table->index(['domain_id', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domain_checks', function (Blueprint $table) {
            $table->dropIndex(['domain_id', 'checked_at']);
        });
    }
};
