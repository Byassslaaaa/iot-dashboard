<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorReading extends Model
{
    protected $fillable = [
        'trash_bin_id',
        'ultrasonic_distance',
        'ir_sensor_triggered',
        'servo_position',
        'buzzer_active',
        'object_detected',
    ];

    protected $casts = [
        'ir_sensor_triggered' => 'boolean',
        'buzzer_active' => 'boolean',
        'object_detected' => 'boolean',
    ];

    public function trashBin(): BelongsTo
    {
        return $this->belongsTo(TrashBin::class);
    }
}
