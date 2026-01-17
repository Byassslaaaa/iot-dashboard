<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trash_bin_id')->constrained()->onDelete('cascade');
            $table->integer('ultrasonic_distance')->nullable(); // dalam cm
            $table->boolean('ir_sensor_triggered')->default(false);
            $table->integer('servo_position')->default(0); // 0 atau 90
            $table->boolean('buzzer_active')->default(false);
            $table->boolean('object_detected')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};
