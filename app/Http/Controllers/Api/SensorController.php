<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\DailyStatistic;
use App\Models\LidEvent;
use App\Models\SensorReading;
use App\Models\SystemLog;
use App\Models\TrashBin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SensorController extends Controller
{
    /**
     * Receive data from ESP32
     * Optimized for high-frequency real-time updates
     */
    public function store(Request $request)
    {
        // Fast validation
        $validated = $request->validate([
            'distance' => 'required|integer|min:0|max:400',
            'ir_triggered' => 'required|boolean',
            'servo_position' => 'required|integer|in:0,90',
            'buzzer_active' => 'required|boolean',
        ]);

        // Cache trash bin untuk mengurangi query
        $trashBin = TrashBin::first();

        if (!$trashBin) {
            $trashBin = TrashBin::create([
                'name' => 'Smart Trash Bin',
                'status' => 'empty',
                'capacity_percentage' => 0,
                'is_active' => true,
            ]);
        }

        // Detect object (jarak < 20cm)
        $objectDetected = $validated['distance'] > 0 && $validated['distance'] < 20;

        // Store previous values untuk comparison
        $previousStatus = $trashBin->status;
        $previousServoPos = $trashBin->latestReading?->servo_position ?? 0;

        // Create sensor reading (PALING PRIORITAS untuk realtime monitoring)
        $reading = SensorReading::create([
            'trash_bin_id' => $trashBin->id,
            'ultrasonic_distance' => $validated['distance'],
            'ir_sensor_triggered' => $validated['ir_triggered'],
            'servo_position' => $validated['servo_position'],
            'buzzer_active' => $validated['buzzer_active'],
            'object_detected' => $objectDetected,
        ]);

        // Update trash bin status based on IR sensor
        if ($validated['ir_triggered']) {
            $trashBin->status = 'full';
            $trashBin->capacity_percentage = 100;
        } else {
            // Calculate capacity based on distance (assume max distance 30cm)
            $maxDistance = 30;
            $capacityPercentage = max(0, min(100, (($maxDistance - $validated['distance']) / $maxDistance) * 100));
            $trashBin->capacity_percentage = round($capacityPercentage);
            $trashBin->status = $capacityPercentage >= 90 ? 'full' : ($capacityPercentage >= 50 ? 'normal' : 'empty');
        }

        // Update connection status
        $trashBin->last_connection = Carbon::now();
        $trashBin->is_connected = true;
        $trashBin->save();

        // OPTIMASI: Hanya proses lid event jika ada perubahan servo position
        if ($validated['servo_position'] != $previousServoPos) {
            if ($validated['servo_position'] == 90) {
                // Lid opened
                LidEvent::create([
                    'trash_bin_id' => $trashBin->id,
                    'event_type' => 'open',
                ]);

                // Update daily statistics (async untuk performa)
                $this->updateDailyStats($trashBin->id, 'lid_open');
            } elseif ($validated['servo_position'] == 0 && $previousServoPos == 90) {
                // Lid closed - cari last open event
                $lastOpenEvent = LidEvent::where('trash_bin_id', $trashBin->id)
                    ->where('event_type', 'open')
                    ->latest()
                    ->first();

                if ($lastOpenEvent) {
                    $duration = Carbon::now()->diffInSeconds($lastOpenEvent->created_at);
                    LidEvent::create([
                        'trash_bin_id' => $trashBin->id,
                        'event_type' => 'close',
                        'duration_seconds' => $duration,
                    ]);
                }
            }
        }

        // OPTIMASI: Hanya create alert jika status berubah ke full
        if ($previousStatus != 'full' && $trashBin->status == 'full') {
            Alert::create([
                'trash_bin_id' => $trashBin->id,
                'type' => 'full',
                'message' => 'Tempat sampah sudah penuh! Segera kosongkan.',
            ]);

            // Log lebih ringan - tanpa metadata lengkap
            SystemLog::create([
                'trash_bin_id' => $trashBin->id,
                'level' => 'warning',
                'action' => 'status_change',
                'description' => 'Trash bin status changed to FULL',
            ]);

            $this->updateDailyStats($trashBin->id, 'full_alert');
        }

        // OPTIMASI: Hanya update object detect stats jika benar-benar terdeteksi
        if ($objectDetected && $previousServoPos == 0 && $validated['servo_position'] == 90) {
            $this->updateDailyStats($trashBin->id, 'object_detect');
        }

        // HAPUS SystemLog untuk setiap reading - terlalu banyak write ke DB
        // Log hanya untuk event penting (sudah ada di atas)

        // Response minimal dan cepat
        return response()->json([
            'success' => true,
            'data' => [
                'reading_id' => $reading->id,
                'status' => $trashBin->status,
                'capacity' => $trashBin->capacity_percentage,
                'timestamp' => $reading->created_at->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Get current status
     * Optimized untuk fast polling dari frontend
     */
    public function status()
    {
        // Eager load untuk mengurangi query
        $trashBin = TrashBin::with(['latestReading' => function($query) {
            $query->select('id', 'trash_bin_id', 'ultrasonic_distance', 'ir_sensor_triggered',
                          'servo_position', 'buzzer_active', 'object_detected', 'created_at');
        }])->first();

        if (!$trashBin) {
            return response()->json([
                'success' => false,
                'message' => 'No trash bin found',
            ], 404);
        }

        // OPTIMASI: Hitung stats hanya jika diperlukan (bisa di-cache)
        $today = Carbon::today();

        // Combine queries untuk efisiensi
        $todayStats = DailyStatistic::where('trash_bin_id', $trashBin->id)
            ->where('date', $today->toDateString())
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'trash_bin' => [
                    'id' => $trashBin->id,
                    'name' => $trashBin->name,
                    'status' => $trashBin->status,
                    'capacity_percentage' => $trashBin->capacity_percentage,
                    'is_connected' => $trashBin->is_connected,
                    'last_connection' => $trashBin->last_connection?->toIso8601String(),
                ],
                'latest_reading' => $trashBin->latestReading,
                'stats' => [
                    'lid_open_today' => $todayStats?->lid_open_count ?? 0,
                    'object_detect_today' => $todayStats?->object_detect_count ?? 0,
                    // Cache total stats atau ambil dari aggregated table
                    'total_lid_open' => LidEvent::where('trash_bin_id', $trashBin->id)
                        ->where('event_type', 'open')
                        ->count(),
                    'total_object_detect' => SensorReading::where('trash_bin_id', $trashBin->id)
                        ->where('object_detected', true)
                        ->count(),
                ],
            ],
        ]);
    }

    /**
     * Get readings for chart
     */
    public function readings(Request $request)
    {
        $trashBin = TrashBin::first();
        $days = $request->get('days', 14);

        $stats = DailyStatistic::where('trash_bin_id', $trashBin->id)
            ->where('date', '>=', Carbon::today()->subDays($days)->toDateString())
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    private function updateDailyStats($trashBinId, $type)
    {
        $today = Carbon::today();

        $stat = DailyStatistic::firstOrCreate(
            ['trash_bin_id' => $trashBinId, 'date' => $today],
            ['lid_open_count' => 0, 'object_detect_count' => 0, 'full_alerts_count' => 0]
        );

        switch ($type) {
            case 'lid_open':
                $stat->increment('lid_open_count');
                // Simulate earnings per usage
                $stat->increment('earnings', rand(10, 50) / 10);
                break;
            case 'object_detect':
                $stat->increment('object_detect_count');
                break;
            case 'full_alert':
                $stat->increment('full_alerts_count');
                // Simulate costs for pickup
                $stat->increment('costs', rand(20, 100) / 10);
                break;
        }
    }
}
