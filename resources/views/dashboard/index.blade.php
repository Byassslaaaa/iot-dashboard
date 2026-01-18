@extends('layouts.dashboard')

@section('title', 'Dashboard - Smart Trash Monitoring')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Trash Status Card -->
        <div class="p-6 rounded-xl shadow cursor-pointer hover:scale-[1.02] transition-transform text-white"
             style="background: linear-gradient(135deg, {{ $trashBin->status === 'full' ? '#ef4444, #dc2626' : ($trashBin->status === 'normal' ? '#22c55e, #16a34a' : '#6b7280, #4b5563') }})">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <span class="font-medium">Trash Status</span>
                </div>
                <svg class="w-5 h-5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <div class="mt-4">
                <h2 class="text-2xl font-bold">
                    STATUS: {{ strtoupper($trashBin->status) }}
                </h2>
                <p class="text-white/80 mt-1">Capacity: {{ $trashBin->capacity_percentage }}%</p>
            </div>
        </div>

        <!-- Total Lid Card -->
        <div class="card p-6 bg-white cursor-pointer hover:scale-[1.02] transition-transform">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Total Lid</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <div class="mt-4">
                <h2 class="text-3xl font-bold text-gray-800">OPEN COUNT</h2>
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        +{{ $lidOpenToday }} today
                    </span>
                    <span>{{ $totalLidOpen }} Times</span>
                </div>
            </div>
        </div>

        <!-- Object Detect Card -->
        <div class="card p-6 bg-white cursor-pointer hover:scale-[1.02] transition-transform">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">OBJECT DETECT</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
            <div class="mt-4">
                <h2 class="text-3xl font-bold text-gray-800">{{ $totalObjectDetect }}</h2>
                <div class="flex items-center gap-1 mt-2 text-sm text-gray-500">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span>Active Today</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Daily Activity Chart -->
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Daily Trash Activity</h3>
                <select id="chartPeriod" class="px-4 py-2 bg-gray-100 rounded-lg text-sm focus:outline-none">
                    <option value="7" selected>Last 7 Days</option>
                    <option value="14">Last 14 Days</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>

        <!-- Live Monitoring Widget -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Live Monitoring</h3>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>

            <!-- Progress Ring -->
            <div class="flex justify-center mb-6">
                <div class="relative">
                    <svg class="progress-ring w-32 h-32">
                        <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="56" cx="64" cy="64"/>
                        <circle id="progressCircle"
                                class="progress-ring__circle text-gray-800"
                                stroke-width="10"
                                stroke-linecap="round"
                                stroke="currentColor"
                                fill="transparent"
                                r="56"
                                cx="64"
                                cy="64"
                                stroke-dasharray="351.86"
                                stroke-dashoffset="{{ 351.86 - (351.86 * $trashBin->capacity_percentage / 100) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="capacityValue" class="text-2xl font-bold text-gray-800">{{ $trashBin->capacity_percentage }}%</span>
                    </div>
                </div>
            </div>

            <!-- Sensor Status List -->
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-800"></div>
                        <span class="text-sm text-gray-600">Ultrasonic Distance</span>
                    </div>
                    <span id="ultrasonicValue" class="text-sm font-medium">{{ $trashBin->latestReading?->ultrasonic_distance ?? 0 }} cm</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-400"></div>
                        <span class="text-sm text-gray-600">IR Sensor</span>
                    </div>
                    <span id="irValue" class="text-sm font-medium">{{ $trashBin->latestReading?->ir_sensor_triggered ? 'TRIGGERED' : 'NORMAL' }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-300"></div>
                        <span class="text-sm text-gray-600">Servo Position</span>
                    </div>
                    <span id="servoValue" class="text-sm font-medium">{{ $trashBin->latestReading?->servo_position ?? 0 }}°</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-gray-200"></div>
                        <span class="text-sm text-gray-600">Buzzer</span>
                    </div>
                    <span id="buzzerValue" class="text-sm font-medium">{{ $trashBin->latestReading?->buzzer_active ? 'ON' : 'OFF' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart data
    const dailyStats = @json($dailyStats);

    // Initialize Chart
    const ctx = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dailyStats.map(s => {
                const date = new Date(s.date);
                return date.toLocaleDateString('en-US', { day: '2-digit', weekday: 'short' });
            }),
            datasets: [
                {
                    label: 'Lid Opens',
                    data: dailyStats.map(s => s.lid_open_count),
                    borderColor: '#1f2937',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#1f2937'
                },
                {
                    label: 'Object Detects',
                    data: dailyStats.map(s => s.object_detect_count),
                    borderColor: '#9ca3af',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#9ca3af'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Real-time updates
    function updateDashboard() {
        fetch('/api/sensor/status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const trashBin = data.data.trash_bin;
                    const reading = data.data.latest_reading;

                    // Update capacity ring
                    const capacity = trashBin.capacity_percentage;
                    const circumference = 351.86;
                    const offset = circumference - (circumference * capacity / 100);
                    document.getElementById('progressCircle').style.strokeDashoffset = offset;
                    document.getElementById('capacityValue').textContent = capacity + '%';

                    // Update sensor values
                    if (reading) {
                        document.getElementById('ultrasonicValue').textContent = reading.ultrasonic_distance + ' cm';
                        document.getElementById('irValue').textContent = reading.ir_sensor_triggered ? 'TRIGGERED' : 'NORMAL';
                        document.getElementById('servoValue').textContent = reading.servo_position + '°';
                        document.getElementById('buzzerValue').textContent = reading.buzzer_active ? 'ON' : 'OFF';
                    }
                }
            })
            .catch(err => console.log('Update error:', err));
    }

    // Update every 3 seconds
    setInterval(updateDashboard, 3000);

    // Period change handler
    document.getElementById('chartPeriod').addEventListener('change', function() {
        const days = this.value;
        fetch(`/api/sensor/readings?days=${days}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    activityChart.data.labels = data.data.map(s => {
                        const date = new Date(s.date);
                        return date.toLocaleDateString('en-US', { day: '2-digit', weekday: 'short' });
                    });
                    activityChart.data.datasets[0].data = data.data.map(s => s.lid_open_count);
                    activityChart.data.datasets[1].data = data.data.map(s => s.object_detect_count);
                    activityChart.update();
                }
            });
    });
</script>
@endpush
