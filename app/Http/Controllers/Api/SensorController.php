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
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'distance' => 'required|integer',
            'ir_triggered' => 'required|boolean',
            'servo_position' => 'required|integer',
            'buzzer_active' => 'required|boolean',
        ]);

        $trashBin = TrashBin::first();

        if (!$trashBin) {
            $trashBin = TrashBin::create([
                'name' => 'Smart Trash Bin',
                'status' => 'empty',
            ]);
        }

        // Detect object (jarak < 20cm)
        $objectDetected = $validated['distance'] > 0 && $validated['distance'] < 20;

        // Create sensor reading
        $reading = SensorReading::create([
            'trash_bin_id' => $trashBin->id,
            'ultrasonic_distance' => $validated['distance'],
            'ir_sensor_triggered' => $validated['ir_triggered'],
            'servo_position' => $validated['servo_position'],
            'buzzer_active' => $validated['buzzer_active'],
            'object_detected' => $objectDetected,
        ]);

        // Update trash bin status based on IR sensor
        $previousStatus = $trashBin->status;
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

        // Create lid event if servo changed to 90 (open)
        if ($validated['servo_position'] == 90) {
            $lastLidEvent = LidEvent::where('trash_bin_id', $trashBin->id)
                ->latest()
                ->first();

            if (!$lastLidEvent || $lastLidEvent->event_type == 'close') {
                LidEvent::create([
                    'trash_bin_id' => $trashBin->id,
                    'event_type' => 'open',
                ]);

                // Update daily statistics
                $this->updateDailyStats($trashBin->id, 'lid_open');
            }
        } elseif ($validated['servo_position'] == 0) {
            $lastLidEvent = LidEvent::where('trash_bin_id', $trashBin->id)
                ->latest()
                ->first();

            if ($lastLidEvent && $lastLidEvent->event_type == 'open') {
                $duration = Carbon::now()->diffInSeconds($lastLidEvent->created_at);
                LidEvent::create([
                    'trash_bin_id' => $trashBin->id,
                    'event_type' => 'close',
                    'duration_seconds' => $duration,
                ]);
            }
        }

        // Create alert if status changed to full
        if ($previousStatus != 'full' && $trashBin->status == 'full') {
            Alert::create([
                'trash_bin_id' => $trashBin->id,
                'type' => 'full',
                'message' => 'Tempat sampah sudah penuh! Segera kosongkan.',
            ]);

            SystemLog::create([
                'trash_bin_id' => $trashBin->id,
                'level' => 'warning',
                'action' => 'status_change',
                'description' => 'Trash bin status changed to FULL',
            ]);

            $this->updateDailyStats($trashBin->id, 'full_alert');
        }

        // Update object detect stats
        if ($objectDetected) {
            $this->updateDailyStats($trashBin->id, 'object_detect');
        }

        // Log the reading
        SystemLog::create([
            'trash_bin_id' => $trashBin->id,
            'level' => 'info',
            'action' => 'sensor_reading',
            'description' => "Distance: {$validated['distance']}cm, IR: " . ($validated['ir_triggered'] ? 'ON' : 'OFF'),
            'metadata' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'reading_id' => $reading->id,
                'status' => $trashBin->status,
                'capacity' => $trashBin->capacity_percentage,
            ],
        ]);
    }

    /**
     * Get current status
     */
    public function status()
    {
        $trashBin = TrashBin::with('latestReading')->first();

        if (!$trashBin) {
            return response()->json([
                'success' => false,
                'message' => 'No trash bin found',
            ], 404);
        }

        $today = Carbon::today();

        return response()->json([
            'success' => true,
            'data' => [
                'trash_bin' => $trashBin,
                'latest_reading' => $trashBin->latestReading,
                'stats' => [
                    'lid_open_today' => LidEvent::where('trash_bin_id', $trashBin->id)
                        ->whereDate('created_at', $today)
                        ->where('event_type', 'open')
                        ->count(),
                    'total_lid_open' => LidEvent::where('trash_bin_id', $trashBin->id)
                        ->where('event_type', 'open')
                        ->count(),
                    'object_detect_today' => SensorReading::where('trash_bin_id', $trashBin->id)
                        ->whereDate('created_at', $today)
                        ->where('object_detected', true)
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
