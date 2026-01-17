<?php

namespace Database\Seeders;

use App\Models\Alert;
use App\Models\DailyStatistic;
use App\Models\LidEvent;
use App\Models\SensorReading;
use App\Models\SystemLog;
use App\Models\TrashBin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@smarttrash.com',
        ]);

        // Create trash bin - akan diisi dengan data real dari ESP32
        TrashBin::create([
            'name' => 'Smart Trash Bin',
            'location' => 'Main Location',
            'status' => 'empty',
            'capacity_percentage' => 0,
            'is_active' => true,
            'last_connection' => null,
            'is_connected' => false,
        ]);
    }
}
