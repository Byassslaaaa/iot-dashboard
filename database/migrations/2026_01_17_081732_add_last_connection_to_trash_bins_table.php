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
        Schema::table('trash_bins', function (Blueprint $table) {
            $table->timestamp('last_connection')->nullable()->after('capacity_percentage');
            $table->boolean('is_connected')->default(false)->after('last_connection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trash_bins', function (Blueprint $table) {
            $table->dropColumn(['last_connection', 'is_connected']);
        });
    }
};
