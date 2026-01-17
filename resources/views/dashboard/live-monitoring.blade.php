@extends('layouts.dashboard')

@section('title', 'Live Monitoring - Smart Trash')

@section('content')
<div class="space-y-6">
    <!-- Connection Alert -->
    @if(!$trashBin->is_connected)
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <h3 class="text-sm font-medium text-red-800">No Live Data Available</h3>
                <div class="mt-2 text-sm text-red-700">
                    <p>ESP32 device is not sending data. Please check:</p>
                    <ul class="list-disc list-inside mt-1 ml-2 space-y-1">
                        <li>ESP32 power supply and WiFi connection</li>
                        <li>Dashboard API URL: <code class="bg-red-100 px-1 rounded">http://{{ request()->ip() }}:8000/api/sensor/data</code></li>
                        <li>Serial Monitor for error messages</li>
                    </ul>
                    @if($trashBin->last_connection)
                    <p class="mt-2 font-medium">Last connected: {{ $trashBin->last_connection->diffForHumans() }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Live Monitoring</h2>
            <p class="text-gray-500">Real-time sensor data from your Smart Trash Bin</p>
        </div>
        <div class="flex items-center gap-2">
            @if($trashBin->is_connected)
                <span id="connectionStatus" class="flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Connected
                </span>
            @else
                <span id="connectionStatus" class="flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm">
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    Disconnected
                </span>
            @endif
            @if($trashBin->last_connection)
                <span class="text-xs text-gray-500">Last seen: {{ $trashBin->last_connection->diffForHumans() }}</span>
            @endif
        </div>
    </div>

    <!-- Main Monitoring Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Overview -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Current Status</h3>

            <!-- Big Status Display -->
            <div id="statusDisplay" class="p-8 rounded-xl text-center {{ $trashBin->status === 'full' ? 'bg-red-500' : ($trashBin->status === 'normal' ? 'bg-yellow-500' : 'bg-green-500') }} text-white mb-6">
                <div class="text-6xl mb-2">
                    @if($trashBin->status === 'full')
                        &#128680;
                    @elseif($trashBin->status === 'normal')
                        &#128293;
                    @else
                        &#9989;
                    @endif
                </div>
                <h2 id="statusText" class="text-3xl font-bold">{{ strtoupper($trashBin->status) }}</h2>
                <p id="capacityText" class="text-white/80 mt-2">Capacity: {{ $trashBin->capacity_percentage }}%</p>
            </div>

            <!-- Capacity Bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Capacity Level</span>
                    <span id="capacityPercent">{{ $trashBin->capacity_percentage }}%</span>
                </div>
                <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                    <div id="capacityBar"
                         class="h-full transition-all duration-500 {{ $trashBin->status === 'full' ? 'bg-red-500' : ($trashBin->status === 'normal' ? 'bg-yellow-500' : 'bg-green-500') }}"
                         style="width: {{ $trashBin->capacity_percentage }}%"></div>
                </div>
            </div>
        </div>

        <!-- Sensor Readings -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sensor Readings</h3>

            <div class="grid grid-cols-2 gap-4">
                <!-- Ultrasonic -->
                <div class="p-4 bg-gray-100 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Ultrasonic</span>
                    </div>
                    <p id="ultrasonicReading" class="text-2xl font-bold text-gray-800">{{ $trashBin->latestReading?->ultrasonic_distance ?? 0 }} <span class="text-sm font-normal">cm</span></p>
                </div>

                <!-- IR Sensor -->
                <div class="p-4 bg-gray-100 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="text-sm text-gray-600">IR Sensor</span>
                    </div>
                    <p id="irReading" class="text-2xl font-bold text-gray-800">
                        {{ $trashBin->latestReading?->ir_sensor_triggered ? 'TRIGGERED' : 'CLEAR' }}
                    </p>
                </div>

                <!-- Servo -->
                <div class="p-4 bg-gray-100 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="text-sm text-gray-600">Servo Position</span>
                    </div>
                    <p id="servoReading" class="text-2xl font-bold text-gray-800">{{ $trashBin->latestReading?->servo_position ?? 0 }}°</p>
                    <p id="servoStatus" class="text-sm text-gray-500">{{ ($trashBin->latestReading?->servo_position ?? 0) === 90 ? 'LID OPEN' : 'LID CLOSED' }}</p>
                </div>

                <!-- Buzzer -->
                <div class="p-4 bg-gray-100 rounded-xl">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Buzzer</span>
                    </div>
                    <p id="buzzerReading" class="text-2xl font-bold text-gray-800">
                        {{ $trashBin->latestReading?->buzzer_active ? 'ON' : 'OFF' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Readings Table -->
    <div class="card p-6" x-data="{ showAll: false }">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Sensor Readings</h3>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Time</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Distance</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">IR Sensor</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Servo</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Buzzer</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Object</th>
                    </tr>
                </thead>
                <tbody id="readingsTable">
                    @forelse($recentReadings as $index => $reading)
                    <tr class="border-b border-gray-100 hover:bg-gray-50" x-show="showAll || {{ $index }} < 5">
                        <td class="py-3 px-4 text-sm text-gray-600">{{ $reading->created_at->format('H:i:s') }}</td>
                        <td class="py-3 px-4 text-sm font-medium">{{ $reading->ultrasonic_distance }} cm</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $reading->ir_sensor_triggered ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ $reading->ir_sensor_triggered ? 'TRIGGERED' : 'CLEAR' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm">{{ $reading->servo_position }}°</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $reading->buzzer_active ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $reading->buzzer_active ? 'ON' : 'OFF' }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            @if($reading->object_detected)
                                <span class="text-green-500">&#10004;</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500">No readings yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentReadings->count() > 5)
        <div class="mt-4 text-center">
            <button @click="showAll = !showAll" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                <span x-show="!showAll">Show More ({{ $recentReadings->count() - 5 }} more)</span>
                <span x-show="showAll">Show Less</span>
            </button>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function updateMonitoring() {
        fetch('/api/sensor/status')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const trashBin = data.data.trash_bin;
                    const reading = data.data.latest_reading;

                    // Update status display
                    const statusDisplay = document.getElementById('statusDisplay');
                    const statusText = document.getElementById('statusText');
                    const capacityText = document.getElementById('capacityText');
                    const capacityPercent = document.getElementById('capacityPercent');
                    const capacityBar = document.getElementById('capacityBar');

                    statusText.textContent = trashBin.status.toUpperCase();
                    capacityText.textContent = `Capacity: ${trashBin.capacity_percentage}%`;
                    capacityPercent.textContent = `${trashBin.capacity_percentage}%`;
                    capacityBar.style.width = `${trashBin.capacity_percentage}%`;

                    // Update colors based on status
                    const statusColors = {
                        full: ['bg-red-500', '&#128680;'],
                        normal: ['bg-yellow-500', '&#128293;'],
                        empty: ['bg-green-500', '&#9989;']
                    };
                    const barColors = {
                        full: 'bg-red-500',
                        normal: 'bg-yellow-500',
                        empty: 'bg-green-500'
                    };

                    statusDisplay.className = `p-8 rounded-xl text-center ${statusColors[trashBin.status][0]} text-white mb-6`;
                    capacityBar.className = `h-full transition-all duration-500 ${barColors[trashBin.status]}`;

                    // Update connection status based on is_connected attribute
                    const connectionStatus = document.getElementById('connectionStatus');
                    if (trashBin.is_connected) {
                        connectionStatus.innerHTML = `
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Connected
                        `;
                        connectionStatus.className = 'flex items-center gap-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm';
                    } else {
                        connectionStatus.innerHTML = `
                            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                            Disconnected
                        `;
                        connectionStatus.className = 'flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm';
                    }

                    // Update sensor readings if available
                    if (reading) {
                        document.getElementById('ultrasonicReading').innerHTML = `${reading.ultrasonic_distance} <span class="text-sm font-normal">cm</span>`;
                        document.getElementById('irReading').textContent = reading.ir_sensor_triggered ? 'TRIGGERED' : 'CLEAR';
                        document.getElementById('irReading').className = 'text-2xl font-bold text-gray-800';
                        document.getElementById('servoReading').textContent = `${reading.servo_position}°`;
                        document.getElementById('servoStatus').textContent = reading.servo_position === 90 ? 'LID OPEN' : 'LID CLOSED';
                        document.getElementById('buzzerReading').textContent = reading.buzzer_active ? 'ON' : 'OFF';
                        document.getElementById('buzzerReading').className = 'text-2xl font-bold text-gray-800';
                    }
                }
            })
            .catch(err => {
                console.log('Update error:', err);
                document.getElementById('connectionStatus').innerHTML = `
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    Disconnected
                `;
                document.getElementById('connectionStatus').className = 'flex items-center gap-2 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm';
            });
    }

    // Update every 2 seconds
    setInterval(updateMonitoring, 2000);
</script>
@endpush
