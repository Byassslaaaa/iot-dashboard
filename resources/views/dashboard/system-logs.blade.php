@extends('layouts.dashboard')

@section('title', 'System Logs - Smart Trash')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">System Logs</h2>
            <p class="text-gray-500">Activity and event logs from your device</p>
        </div>
        <div class="flex gap-2">
            <select class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Levels</option>
                <option value="info">Info</option>
                <option value="warning">Warning</option>
                <option value="error">Error</option>
            </select>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Time</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Level</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Action</th>
                    <th class="text-left py-4 px-6 text-sm font-medium text-gray-500">Description</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr class="border-b border-gray-100 hover:bg-gray-50">
                    <td class="py-4 px-6 text-sm text-gray-600">
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="py-4 px-6">
                        @php
                            $levelColors = [
                                'info' => 'bg-blue-100 text-blue-700',
                                'warning' => 'bg-yellow-100 text-yellow-700',
                                'error' => 'bg-red-100 text-red-700',
                                'debug' => 'bg-gray-100 text-gray-700',
                            ];
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $levelColors[$log->level] ?? 'bg-gray-100 text-gray-700' }}">
                            {{ strtoupper($log->level) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-sm font-medium text-gray-800">
                        {{ $log->action }}
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-600">
                        {{ $log->description }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>No logs found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
