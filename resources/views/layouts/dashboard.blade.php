<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Smart Trash Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: 'Figtree', sans-serif;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Status card gradients */
        .status-card-full {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
        }
        .status-card-normal {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
            color: white !important;
        }
        .status-card-empty {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
            color: white !important;
        }

        /* Card hover effect */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Chart card */
        .card.p-6 {
            background: white !important;
        }

        /* Progress ring */
        .progress-ring__circle {
            transition: stroke-dashoffset 0.35s;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        /* Sidebar active state */
        .nav-link {
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.1);
        }
        .nav-link.active {
            background: rgba(255,255,255,0.15);
            border-left: 3px solid white;
        }

        /* Wave animation */
        .wave {
            animation: wave 2s ease-in-out infinite;
            display: inline-block;
        }
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(20deg); }
            75% { transform: rotate(-20deg); }
        }

        /* Pulse animation for live indicator */
        .pulse {
            animation: pulse 2s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Sidebar styles */
        .sidebar {
            background: #000000;
            position: relative;
        }

        .sidebar-item {
            transition: all 0.2s ease;
            position: relative;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar-item.active {
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 70%;
            background: #ffffff;
        }

        .badge {
            padding: 2px 8px;
            border-radius: 9999px;
            font-size: 0.75rem;
        }

        /* Toast notification */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-hidden {
            animation: slideOut 0.3s ease-in;
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
    </style>

    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            @include('components.header')

            <!-- Page Content -->
            <main class="flex-1 p-6 overflow-auto">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Toast Notification for Disconnected Device -->
    @php
        $trashBin = \App\Models\TrashBin::first();
    @endphp
    @if($trashBin && !$trashBin->is_connected)
    <div class="toast-container" x-data="{ show: true, dismissed: false, expanded: false }">
        <div x-show="show && !dismissed"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="transform translate-x-full opacity-0"
             x-transition:enter-end="transform translate-x-0 opacity-100"
             class="toast bg-white rounded-xl shadow-2xl border border-yellow-300 overflow-hidden"
             :class="expanded ? 'max-w-lg' : 'max-w-md'">

            <!-- Header with gradient -->
            <div class="bg-gradient-to-r from-yellow-400 to-orange-400 px-4 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <h4 class="font-semibold text-white text-sm">Device Not Connected</h4>
                </div>
                <button @click="show = false" class="text-white hover:text-yellow-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-4">
                <p class="text-sm text-gray-700 mb-3">Your ESP32 device is not sending data. Follow these steps to connect:</p>

                <!-- Quick Setup Steps -->
                <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg p-3 mb-3">
                    <p class="font-semibold text-gray-800 text-xs mb-2 flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Quick Setup
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-5 h-5 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                            <p class="text-xs text-gray-700 flex-1">Power on ESP32 and connect to WiFi</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-5 h-5 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                            <div class="text-xs text-gray-700 flex-1">
                                <p class="mb-1">Set API URL in your ESP32 code:</p>
                                <code class="block bg-gray-800 text-green-400 px-2 py-1 rounded text-[10px] font-mono">http://{{ request()->ip() }}:8000/api/sensor/data</code>
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="flex-shrink-0 w-5 h-5 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                            <p class="text-xs text-gray-700 flex-1">Upload code & check Serial Monitor</p>
                        </div>
                    </div>
                </div>

                <!-- Expandable Troubleshooting -->
                <div x-show="expanded" x-collapse class="mb-3">
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <p class="font-semibold text-gray-800 text-xs mb-2 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Troubleshooting
                        </p>
                        <ul class="space-y-1.5 text-xs text-gray-700">
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-600 mt-0.5">•</span>
                                <span>Verify WiFi SSID and password in ESP32 code</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-600 mt-0.5">•</span>
                                <span>Ensure ESP32 and server on same network</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-600 mt-0.5">•</span>
                                <span>Check firewall blocking port 8000</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-yellow-600 mt-0.5">•</span>
                                <span>Test connection: <code class="bg-gray-200 px-1 rounded text-[10px]">ping {{ request()->ip() }}</code></span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2 flex-wrap">
                    <button @click="expanded = !expanded" class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-xs font-medium transition flex items-center gap-1">
                        <svg x-show="!expanded" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                        <svg x-show="expanded" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                        <span x-text="expanded ? 'Hide Details' : 'Show Details'"></span>
                    </button>
                    <a href="{{ route('live-monitoring') }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium transition">
                        Live Monitor
                    </a>
                    <button @click="dismissed = true" class="ml-auto text-xs text-gray-500 hover:text-gray-700 transition">
                        Dismiss
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @stack('scripts')
</body>
</html>
