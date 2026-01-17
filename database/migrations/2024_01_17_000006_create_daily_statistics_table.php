<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trash_bin_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('lid_open_count')->default(0);
            $table->integer('object_detect_count')->default(0);
            $table->integer('full_alerts_count')->default(0);
            $table->decimal('earnings', 10, 2)->default(0);
            $table->decimal('costs', 10, 2)->default(0);
            $table->timestamps();

            $table->unique(['trash_bin_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_statistics');
    }
};
