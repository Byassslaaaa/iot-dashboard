@extends('layouts.dashboard')

@section('title', 'Alerts - Smart Trash')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Alerts</h2>
            <p class="text-gray-500">System alerts and notifications</p>
        </div>
        <div class="flex gap-2">
            @php
                $unreadCount = $alerts->where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
            <form action="{{ route('alerts.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg text-sm hover:bg-gray-700 transition">
                    Mark All as Read ({{ $unreadCount }})
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Alerts List -->
    <div class="space-y-4">
        @forelse($alerts as $alert)
        <div class="card p-4 {{ !$alert->is_read ? 'border-l-4 border-l-red-500 bg-red-50/50' : '' }}">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center
                        {{ $alert->type === 'full' ? 'bg-red-100 text-red-600' :
                           ($alert->type === 'warning' ? 'bg-yellow-100 text-yellow-600' :
                           ($alert->type === 'maintenance' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600')) }}">
                        @if($alert->type === 'full')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        @elseif($alert->type === 'warning')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @elseif($alert->type === 'maintenance')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="font-medium text-gray-800">
                                {{ ucfirst($alert->type) }} Alert
                            </h4>
                            @if(!$alert->is_read)
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs rounded-full font-medium">New</span>
                            @endif
                            @if($alert->is_resolved)
                                <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full font-medium">Resolved</span>
                            @endif
                        </div>
                        <p class="text-gray-600 mt-1">{{ $alert->message }}</p>
                        <p class="text-sm text-gray-400 mt-2">{{ $alert->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if(!$alert->is_read)
                    <form action="{{ route('alerts.mark-read', $alert) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition">
                            Mark Read
                        </button>
                    </form>
                    @endif
                    @if(!$alert->is_resolved)
                    <form action="{{ route('alerts.resolve', $alert) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1 text-sm bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                            Resolve
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="card p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-600 mb-1">No alerts</h3>
            <p class="text-gray-400">Your trash bin is operating normally</p>
        </div>
        @endforelse
    </div>

    @if($alerts->hasPages())
    <div class="flex justify-center">
        {{ $alerts->links() }}
    </div>
    @endif
</div>
@endsection
