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
            'email' => 'admin@example.com',
        ]);

        // Create a trash bin
        $trashBin = TrashBin::create([
            'name' => 'Smart Trash Bin',
            'location' => 'Main Lobby',
            'status' => 'normal',
            'capacity_percentage' => 45,
            'is_active' => true,
        ]);

        // Generate daily statistics for the last 14 days
        for ($i = 14; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            DailyStatistic::create([
                'trash_bin_id' => $trashBin->id,
                'date' => $date->format('Y-m-d'),
                'lid_open_count' => rand(20, 60),
                'object_detect_count' => rand(15, 55),
                'full_alerts_count' => rand(0, 3),
                'earnings' => rand(200, 600) / 10,
                'costs' => rand(100, 300) / 10,
            ]);
        }

        // Generate some lid events for today
        for ($i = 0; $i < 12; $i++) {
            LidEvent::create([
                'trash_bin_id' => $trashBin->id,
                'event_type' => 'open',
                'created_at' => Carbon::today()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
            ]);
        }

        // Generate recent sensor readings
        for ($i = 0; $i < 50; $i++) {
            $distance = rand(5, 35);
            $irTriggered = rand(0, 10) > 8;

            SensorReading::create([
                'trash_bin_id' => $trashBin->id,
                'ultrasonic_distance' => $distance,
                'ir_sensor_triggered' => $irTriggered,
                'servo_position' => $distance < 20 ? 90 : 0,
                'buzzer_active' => $irTriggered,
                'object_detected' => $distance < 20,
                'created_at' => Carbon::now()->subMinutes($i * 5),
            ]);
        }

        // Generate some alerts
        $alertTypes = ['full', 'warning', 'maintenance'];
        $alertMessages = [
            'full' => 'Tempat sampah sudah penuh! Segera kosongkan.',
            'warning' => 'Kapasitas hampir mencapai 80%.',
            'maintenance' => 'Jadwal pemeliharaan rutin.',
        ];

        for ($i = 0; $i < 5; $i++) {
            $type = $alertTypes[array_rand($alertTypes)];
            Alert::create([
                'trash_bin_id' => $trashBin->id,
                'type' => $type,
                'message' => $alertMessages[$type],
                'is_read' => rand(0, 1),
                'is_resolved' => rand(0, 1),
                'created_at' => Carbon::now()->subHours(rand(1, 72)),
            ]);
        }

        // Generate system logs
        $logActions = [
            ['action' => 'sensor_reading', 'description' => 'Sensor data received', 'level' => 'info'],
            ['action' => 'status_change', 'description' => 'Status changed to normal', 'level' => 'info'],
            ['action' => 'lid_opened', 'description' => 'Lid opened by user', 'level' => 'info'],
            ['action' => 'full_alert', 'description' => 'Trash bin is full', 'level' => 'warning'],
            ['action' => 'connection_lost', 'description' => 'WiFi connection lost', 'level' => 'error'],
        ];

        for ($i = 0; $i < 20; $i++) {
            $log = $logActions[array_rand($logActions)];
            SystemLog::create([
                'trash_bin_id' => $trashBin->id,
                'level' => $log['level'],
                'action' => $log['action'],
                'description' => $log['description'],
                'created_at' => Carbon::now()->subMinutes(rand(1, 1440)),
            ]);
        }
    }
}
