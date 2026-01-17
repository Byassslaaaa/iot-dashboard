<aside class="sidebar text-white flex flex-col h-screen sticky top-0 transition-all duration-300"
       x-data="{ expanded: false }"
       :class="expanded ? 'w-64' : 'w-16'">
    <!-- Menu Toggle -->
    <div class="p-4 border-b border-white/10 flex-shrink-0 flex items-center" :class="expanded ? 'justify-between' : 'justify-center'">
        <span x-show="expanded" class="font-semibold text-lg whitespace-nowrap">Smart Trash</span>
        <button @click="expanded = !expanded" class="w-8 h-8 flex items-center justify-center hover:bg-white/10 rounded transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-4 overflow-y-auto">
        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}"
                   class="sidebar-item flex items-center py-4 px-4 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   :class="expanded ? 'justify-start gap-3' : 'justify-center'"
                   :title="expanded ? '' : 'Overview'">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span x-show="expanded" class="whitespace-nowrap">Overview</span>
                </a>
            </li>
            <li>
                <a href="{{ route('live-monitoring') }}"
                   class="sidebar-item flex items-center py-4 px-4 {{ request()->routeIs('live-monitoring') ? 'active' : '' }}"
                   :class="expanded ? 'justify-start gap-3' : 'justify-center'"
                   :title="expanded ? '' : 'Live Monitoring'">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="expanded" class="whitespace-nowrap">Live Monitoring</span>
                </a>
            </li>
            <li>
                <a href="{{ route('system-logs') }}"
                   class="sidebar-item flex items-center py-4 px-4 {{ request()->routeIs('system-logs') ? 'active' : '' }}"
                   :class="expanded ? 'justify-start gap-3' : 'justify-center'"
                   :title="expanded ? '' : 'System Logs'">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="expanded" class="whitespace-nowrap">System Logs</span>
                </a>
            </li>
            <li>
                <a href="{{ route('alerts') }}"
                   class="sidebar-item flex items-center py-4 px-4 relative {{ request()->routeIs('alerts') ? 'active' : '' }}"
                   :class="expanded ? 'justify-start gap-3' : 'justify-center'"
                   :title="expanded ? '' : 'Alerts'">
                    <div class="relative flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        @php
                            $unreadAlertsCount = \App\Models\Alert::where('is_read', false)->count();
                        @endphp
                        @if($unreadAlertsCount > 0)
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </div>
                    <span x-show="expanded" class="whitespace-nowrap">Alerts</span>
                </a>
            </li>
            <li>
                <a href="{{ route('settings') }}"
                   class="sidebar-item flex items-center py-4 px-4 {{ request()->routeIs('settings') ? 'active' : '' }}"
                   :class="expanded ? 'justify-start gap-3' : 'justify-center'"
                   :title="expanded ? '' : 'Settings'">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span x-show="expanded" class="whitespace-nowrap">Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('about-device') }}"
                   class="sidebar-item flex items-center py-4 px-4 {{ request()->routeIs('about-device') ? 'active' : '' }}"
                   :class="expanded ? 'justify-start gap-3' : 'justify-center'"
                   :title="expanded ? '' : 'About Device'">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="expanded" class="whitespace-nowrap">About Device</span>
                </a>
            </li>
        </ul>
    </nav>

</aside>
