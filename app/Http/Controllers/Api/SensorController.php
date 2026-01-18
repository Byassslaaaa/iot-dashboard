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
use Illuminate\Support\Facades\Cache;

class SensorController extends Controller
{
    /**
     * Receive data from ESP32 - ULTRA LIGHTWEIGHT VERSION
     * Response time target: < 50ms
     */
    public function store(Request $request)
    {
        // Minimal validation (faster)
        $data = $request->only(['distance', 'ir_triggered', 'servo_position', 'buzzer_active']);

        // Get or cache trash bin (avoid repeated queries)
        $trashBin = Cache::remember('trash_bin', 60, function () {
            return TrashBin::first() ?? TrashBin::create([
                'name' => 'Smart Trash Bin',
                'status' => 'empty',
                'capacity_percentage' => 0,
                'is_active' => true,
            ]);
        });

        // Quick calculations
        $distance = (int) $data['distance'];
        $irTriggered = (bool) $data['ir_triggered'];
        $servoPos = (int) $data['servo_position'];
        $buzzerActive = (bool) $data['buzzer_active'];
        $objectDetected = $distance > 0 && $distance < 20;

        // PRIORITY 1: Save sensor reading (paling penting untuk monitoring)
        SensorReading::create([
            'trash_bin_id' => $trashBin->id,
            'ultrasonic_distance' => $distance,
            'ir_sensor_triggered' => $irTriggered,
            'servo_position' => $servoPos,
            'buzzer_active' => $buzzerActive,
            'object_detected' => $objectDetected,
        ]);

        // PRIORITY 2: Update trash bin status (simple logic)
        $prevStatus = $trashBin->status;

        if ($irTriggered) {
            $trashBin->status = 'full';
            $trashBin->capacity_percentage = 100;
        } else {
            $capacity = max(0, min(100, round(((30 - $distance) / 30) * 100)));
            $trashBin->capacity_percentage = $capacity;
            $trashBin->status = $capacity >= 90 ? 'full' : ($capacity >= 50 ? 'normal' : 'empty');
        }

        $trashBin->last_connection = now();
        $trashBin->is_connected = true;
        $trashBin->save();

        // Clear cache setelah update
        Cache::forget('trash_bin');

        // PRIORITY 3: Background tasks (tidak blocking)
        // Hanya jika benar-benar perlu
        if ($prevStatus != 'full' && $trashBin->status == 'full') {
            // Alert hanya saat berubah ke full
            Alert::create([
                'trash_bin_id' => $trashBin->id,
                'type' => 'full',
                'message' => 'Tempat sampah sudah penuh!',
            ]);
        }

        // Response super cepat
        return response()->json(['success' => true], 200);
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
