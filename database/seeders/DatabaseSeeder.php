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

        // Create a trash bin dengan status yang realistis
        $trashBin = TrashBin::create([
            'name' => 'Smart Trash Bin',
            'location' => 'Main Lobby',
            'status' => 'normal',
            'capacity_percentage' => 65,
            'is_active' => true,
        ]);

        // Generate daily statistics untuk 14 hari terakhir (termasuk hari ini)
        $today = Carbon::today();
        for ($i = 13; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);

            DailyStatistic::create([
                'trash_bin_id' => $trashBin->id,
                'date' => $date->format('Y-m-d'),
                'lid_open_count' => rand(25, 55),
                'object_detect_count' => rand(20, 50),
                'full_alerts_count' => rand(0, 2),
                'earnings' => rand(200, 500) / 10,
                'costs' => rand(100, 250) / 10,
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }

        // Generate lid events untuk hari ini
        $todayOpenCount = rand(8, 15);
        for ($i = 0; $i < $todayOpenCount; $i++) {
            LidEvent::create([
                'trash_bin_id' => $trashBin->id,
                'event_type' => 'open',
                'created_at' => $today->copy()->addHours(rand(6, 22))->addMinutes(rand(0, 59)),
            ]);
        }

        // Generate recent sensor readings (setiap 5 menit selama 4 jam terakhir)
        $now = Carbon::now();
        for ($i = 48; $i >= 0; $i--) {
            $readingTime = $now->copy()->subMinutes($i * 5);
            $distance = rand(8, 30);
            $irTriggered = $distance < 5 || rand(0, 20) === 0;

            SensorReading::create([
                'trash_bin_id' => $trashBin->id,
                'ultrasonic_distance' => $distance,
                'ir_sensor_triggered' => $irTriggered,
                'servo_position' => $distance < 20 ? 90 : 0,
                'buzzer_active' => $irTriggered,
                'object_detected' => $distance < 20,
                'created_at' => $readingTime,
                'updated_at' => $readingTime,
            ]);
        }

        // Generate alerts
        $alertData = [
            ['type' => 'full', 'message' => 'Tempat sampah sudah penuh! Segera kosongkan.', 'hours_ago' => 2],
            ['type' => 'warning', 'message' => 'Kapasitas sudah mencapai 80%.', 'hours_ago' => 5],
            ['type' => 'full', 'message' => 'Tempat sampah sudah penuh! Segera kosongkan.', 'hours_ago' => 24],
            ['type' => 'maintenance', 'message' => 'Sensor ultrasonik perlu dikalibrasi.', 'hours_ago' => 48],
            ['type' => 'warning', 'message' => 'Koneksi WiFi tidak stabil.', 'hours_ago' => 72],
        ];

        foreach ($alertData as $index => $alert) {
            Alert::create([
                'trash_bin_id' => $trashBin->id,
                'type' => $alert['type'],
                'message' => $alert['message'],
                'is_read' => $index > 1,
                'is_resolved' => $index > 2,
                'created_at' => Carbon::now()->subHours($alert['hours_ago']),
            ]);
        }

        // Generate system logs
        $logData = [
            ['action' => 'sensor_reading', 'description' => 'Data sensor diterima: Jarak 15cm', 'level' => 'info', 'minutes_ago' => 5],
            ['action' => 'lid_opened', 'description' => 'Tutup tempat sampah dibuka', 'level' => 'info', 'minutes_ago' => 10],
            ['action' => 'lid_closed', 'description' => 'Tutup tempat sampah ditutup', 'level' => 'info', 'minutes_ago' => 12],
            ['action' => 'sensor_reading', 'description' => 'Data sensor diterima: Jarak 25cm', 'level' => 'info', 'minutes_ago' => 15],
            ['action' => 'status_change', 'description' => 'Status berubah: empty -> normal', 'level' => 'info', 'minutes_ago' => 30],
            ['action' => 'full_alert', 'description' => 'Tempat sampah penuh terdeteksi!', 'level' => 'warning', 'minutes_ago' => 120],
            ['action' => 'telegram_sent', 'description' => 'Notifikasi Telegram terkirim', 'level' => 'info', 'minutes_ago' => 121],
            ['action' => 'wifi_reconnect', 'description' => 'WiFi reconnected setelah disconnect', 'level' => 'warning', 'minutes_ago' => 180],
            ['action' => 'system_boot', 'description' => 'Sistem ESP32 boot ulang', 'level' => 'info', 'minutes_ago' => 360],
            ['action' => 'sensor_error', 'description' => 'Sensor ultrasonik timeout', 'level' => 'error', 'minutes_ago' => 480],
        ];

        foreach ($logData as $log) {
            SystemLog::create([
                'trash_bin_id' => $trashBin->id,
                'level' => $log['level'],
                'action' => $log['action'],
                'description' => $log['description'],
                'created_at' => Carbon::now()->subMinutes($log['minutes_ago']),
            ]);
        }

        // Tambahkan lebih banyak logs random
        $randomActions = [
            ['action' => 'sensor_reading', 'description' => 'Data sensor diterima', 'level' => 'info'],
            ['action' => 'lid_opened', 'description' => 'Tutup dibuka oleh pengguna', 'level' => 'info'],
            ['action' => 'object_detected', 'description' => 'Objek terdeteksi di depan sensor', 'level' => 'info'],
        ];

        for ($i = 0; $i < 15; $i++) {
            $randomLog = $randomActions[array_rand($randomActions)];
            SystemLog::create([
                'trash_bin_id' => $trashBin->id,
                'level' => $randomLog['level'],
                'action' => $randomLog['action'],
                'description' => $randomLog['description'],
                'created_at' => Carbon::now()->subMinutes(rand(20, 1440)),
            ]);
        }
    }
}
