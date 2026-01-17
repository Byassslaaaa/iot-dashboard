@extends('layouts.app')

@section('title', 'Settings - Smart Trash')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">System Settings</h2>
        <p class="text-gray-500">Configure your Smart Trash Monitoring System</p>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Device Settings -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Device Settings</h3>

            <form action="{{ route('settings.update') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Device Name</label>
                    <input type="text"
                           name="name"
                           value="{{ $trashBin->name ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text"
                           name="location"
                           value="{{ $trashBin->location ?? '' }}"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Notification Settings -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Notification Settings</h3>

            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div>
                        <p class="font-medium text-gray-800">Full Bin Alert</p>
                        <p class="text-sm text-gray-500">Notify when bin is full</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-gray-100">
                    <div>
                        <p class="font-medium text-gray-800">Telegram Notifications</p>
                        <p class="text-sm text-gray-500">Send alerts via Telegram</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium text-gray-800">Sound Alert</p>
                        <p class="text-sm text-gray-500">Enable buzzer when full</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Sensor Thresholds -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sensor Thresholds</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detection Distance (cm)</label>
                    <input type="number"
                           value="20"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Objects closer than this will trigger lid open</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lid Close Delay (seconds)</label>
                    <input type="number"
                           value="2"
                           class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Wait time before closing the lid</p>
                </div>
            </div>
        </div>

        <!-- API Information -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">API Information</h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Endpoint</label>
                    <div class="flex">
                        <input type="text"
                               value="{{ url('/api/sensor/data') }}"
                               readonly
                               class="flex-1 px-4 py-2 border border-gray-200 rounded-l-lg bg-gray-50 text-gray-600">
                        <button onclick="navigator.clipboard.writeText('{{ url('/api/sensor/data') }}')"
                                class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-200 rounded-r-lg hover:bg-gray-200 transition">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-2">Request Format (POST)</p>
                    <pre class="text-xs text-gray-600 overflow-x-auto"><code>{
  "distance": 25,
  "ir_triggered": false,
  "servo_position": 0,
  "buzzer_active": false
}</code></pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
