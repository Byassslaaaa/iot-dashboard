<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrashBin extends Model
{
    protected $fillable = [
        'name',
        'location',
        'status',
        'capacity_percentage',
        'is_active',
        'last_connection',
        'is_connected',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_connected' => 'boolean',
        'last_connection' => 'datetime',
    ];

    public function sensorReadings(): HasMany
    {
        return $this->hasMany(SensorReading::class);
    }

    public function lidEvents(): HasMany
    {
        return $this->hasMany(LidEvent::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class);
    }

    public function systemLogs(): HasMany
    {
        return $this->hasMany(SystemLog::class);
    }

    public function dailyStatistics(): HasMany
    {
        return $this->hasMany(DailyStatistic::class);
    }

    public function latestReading()
    {
        return $this->hasOne(SensorReading::class)->latestOfMany();
    }

    /**
     * Check if device is currently connected
     * Device is considered disconnected if no data received in last 30 seconds
     */
    public function getIsConnectedAttribute($value)
    {
        if (!$this->last_connection) {
            return false;
        }

        // Check if last connection was within 30 seconds
        return $this->last_connection->diffInSeconds(now()) < 30;
    }

    /**
     * Get connection status text
     */
    public function getConnectionStatusAttribute()
    {
        return $this->is_connected ? 'Connected' : 'Disconnected';
    }
}
