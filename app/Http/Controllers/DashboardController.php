<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\DailyStatistic;
use App\Models\LidEvent;
use App\Models\SensorReading;
use App\Models\SystemLog;
use App\Models\TrashBin;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $trashBin = TrashBin::with('latestReading')->first();

        if (!$trashBin) {
            $trashBin = TrashBin::create([
                'name' => 'Smart Trash Bin',
                'location' => 'Main Location',
                'status' => 'empty',
                'capacity_percentage' => 0,
            ]);
        }

        // Hitung statistik
        $today = Carbon::today();
        $lidOpenToday = LidEvent::where('trash_bin_id', $trashBin->id)
            ->whereDate('created_at', $today)
            ->where('event_type', 'open')
            ->count();

        $totalLidOpen = LidEvent::where('trash_bin_id', $trashBin->id)
            ->where('event_type', 'open')
            ->count();

        $objectDetectToday = SensorReading::where('trash_bin_id', $trashBin->id)
            ->whereDate('created_at', $today)
            ->where('object_detected', true)
            ->count();

        $totalObjectDetect = SensorReading::where('trash_bin_id', $trashBin->id)
            ->where('object_detected', true)
            ->count();

        // Daily statistics untuk chart (14 hari terakhir)
        $dailyStats = DailyStatistic::where('trash_bin_id', $trashBin->id)
            ->where('date', '>=', Carbon::now()->subDays(14))
            ->orderBy('date')
            ->get();

        // Unread alerts count
        $unreadAlerts = Alert::where('is_read', false)->count();

        // System logs count
        $systemLogsCount = SystemLog::where('trash_bin_id', $trashBin->id)->count();

        return view('dashboard.index', compact(
            'trashBin',
            'lidOpenToday',
            'totalLidOpen',
            'objectDetectToday',
            'totalObjectDetect',
            'dailyStats',
            'unreadAlerts',
            'systemLogsCount'
        ));
    }

    public function liveMonitoring()
    {
        $trashBin = TrashBin::with('latestReading')->first();
        $recentReadings = SensorReading::where('trash_bin_id', $trashBin->id)
            ->latest()
            ->take(50)
            ->get();

        return view('dashboard.live-monitoring', compact('trashBin', 'recentReadings'));
    }

    public function systemLogs()
    {
        $logs = SystemLog::with('trashBin')
            ->latest()
            ->paginate(20);

        return view('dashboard.system-logs', compact('logs'));
    }

    public function alerts()
    {
        $alerts = Alert::with('trashBin')
            ->latest()
            ->paginate(20);

        return view('dashboard.alerts', compact('alerts'));
    }

    public function settings()
    {
        $trashBin = TrashBin::first();
        return view('dashboard.settings', compact('trashBin'));
    }

    public function updateSettings(Request $request)
    {
        $trashBin = TrashBin::first();
        $trashBin->update($request->only(['name', 'location']));

        return redirect()->route('settings')->with('success', 'Settings updated successfully');
    }

    public function aboutDevice()
    {
        $trashBin = TrashBin::first();
        return view('dashboard.about-device', compact('trashBin'));
    }
}
