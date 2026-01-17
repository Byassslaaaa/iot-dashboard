<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lid_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trash_bin_id')->constrained()->onDelete('cascade');
            $table->enum('event_type', ['open', 'close'])->default('open');
            $table->integer('duration_seconds')->nullable(); // berapa lama terbuka
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lid_events');
    }
};
