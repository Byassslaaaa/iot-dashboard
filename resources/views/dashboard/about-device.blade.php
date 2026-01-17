@extends('layouts.app')

@section('title', 'About Device - Smart Trash')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">About Device</h2>
        <p class="text-gray-500">Smart Trash Bin specifications and information</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Device Info -->
        <div class="card p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-gradient-to-br from-gray-700 to-gray-900 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">{{ $trashBin->name ?? 'Smart Trash Bin' }}</h3>
                    <p class="text-gray-500">IoT-enabled Smart Waste Management</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">Device ID</span>
                    <span class="font-medium text-gray-800">#STB-{{ str_pad($trashBin->id ?? 1, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">Location</span>
                    <span class="font-medium text-gray-800">{{ $trashBin->location ?? 'Not Set' }}</span>
                </div>
                <div class="flex justify-between py-3 border-b border-gray-100">
                    <span class="text-gray-600">Status</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $trashBin->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $trashBin->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex justify-between py-3">
                    <span class="text-gray-600">Registered</span>
                    <span class="font-medium text-gray-800">{{ $trashBin->created_at?->format('d M Y') ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Hardware Specifications -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Hardware Specifications</h3>

            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        <span class="font-medium text-gray-800">Microcontroller</span>
                    </div>
                    <p class="text-sm text-gray-600">ESP32 DevKit V1</p>
                    <p class="text-xs text-gray-500 mt-1">WiFi + Bluetooth enabled</p>
                </div>

                <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="font-medium text-gray-800">Ultrasonic Sensor</span>
                    </div>
                    <p class="text-sm text-gray-600">HC-SR04</p>
                    <p class="text-xs text-gray-500 mt-1">Range: 2cm - 400cm</p>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span class="font-medium text-gray-800">IR Sensor</span>
                    </div>
                    <p class="text-sm text-gray-600">Infrared Obstacle Sensor</p>
                    <p class="text-xs text-gray-500 mt-1">Detects when bin is full</p>
                </div>

                <div class="p-4 bg-orange-50 rounded-lg">
                    <div class="flex items-center gap-3 mb-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="font-medium text-gray-800">Servo Motor</span>
                    </div>
                    <p class="text-sm text-gray-600">SG90 Micro Servo</p>
                    <p class="text-xs text-gray-500 mt-1">Controls lid opening/closing</p>
                </div>
            </div>
        </div>

        <!-- Pin Configuration -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pin Configuration</h3>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 text-sm font-medium text-gray-500">Component</th>
                            <th class="text-left py-3 text-sm font-medium text-gray-500">Pin</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-600">Ultrasonic TRIG</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 12</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-600">Ultrasonic ECHO</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 27</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-600">IR Sensor</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 26</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-600">Buzzer</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 25</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-600">Servo Motor</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 23</td>
                        </tr>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 text-gray-600">OLED SDA</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 21</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-gray-600">OLED SCL</td>
                            <td class="py-3 font-mono text-gray-800">GPIO 22</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Features -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Features</h3>

            <ul class="space-y-3">
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Automatic Lid Opening</p>
                        <p class="text-sm text-gray-500">Hands-free operation using ultrasonic detection</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Fill Level Detection</p>
                        <p class="text-sm text-gray-500">IR sensor detects when bin reaches capacity</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Telegram Notifications</p>
                        <p class="text-sm text-gray-500">Instant alerts when bin is full</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">OLED Display</p>
                        <p class="text-sm text-gray-500">Real-time status display on device</p>
                    </div>
                </li>
                <li class="flex items-start gap-3">
                    <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div>
                        <p class="font-medium text-gray-800">Web Dashboard</p>
                        <p class="text-sm text-gray-500">Monitor and manage remotely via browser</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
