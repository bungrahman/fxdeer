@extends('layouts.admin')

@section('title', 'Overview')

@section('content')
@php
    $isPaused = \App\Models\Setting::get('emergency_pause', false);
@endphp
<header>
    <h2>Overview</h2>
    <div class="flex gap-4">
        <div class="section-card py-2 px-4 flex items-center gap-2 rounded-xl mb-0">
            <div class="w-2 h-2 rounded-full {{ $isPaused ? 'bg-red-500' : 'bg-green-500' }}"></div>
            <span class="text-gray-900 dark:text-white font-semibold">{{ $isPaused ? 'System Paused' : 'System Live' }}</span>
        </div>
    </div>
</header>

<div class="stats-grid grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="section-card !mb-0">
        <span class="text-gray-500 dark:text-gray-400 text-sm">Total Users</span>
        <h4 class="text-3xl font-bold mt-2">{{ $stats['total_users'] }}</h4>
    </div>
    <div class="section-card !mb-0">
        <span class="text-gray-500 dark:text-gray-400 text-sm">Active Subscriptions</span>
        <h4 class="text-3xl font-bold mt-2">{{ $stats['active_subs'] }}</h4>
    </div>
    <div class="section-card !mb-0">
        <span class="text-gray-500 dark:text-gray-400 text-sm">Events Handled (Today)</span>
        <h4 class="text-3xl font-bold mt-2">{{ $stats['events_today'] }}</h4>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="section-card lg:col-span-2">
        <div class="section-title"><i data-feather="shield"></i> Global Kill-Switches</div>
        
        <form action="{{ route('admin.toggle-switch') }}" method="POST" id="switchForm">
            @csrf
            <input type="hidden" name="key" id="switchKey">
            <input type="hidden" name="value" id="switchValue">

            @foreach(['emergency_pause' => 'Emergency Global Pause', 'kill_switch_pipeline_a' => 'Pipeline A (High Priority)', 'kill_switch_pipeline_b' => 'Pipeline B (Standard)'] as $key => $label)
            <div class="flex justify-between items-center py-5 {{ !$loop->last ? 'border-b border-gray-200 dark:border-white/10' : '' }}">
                <div>
                    <h5 class="text-base font-semibold mb-1">{{ $label }}</h5>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">Instantly control news distribution for this module.</p>
                </div>
                <label class="switch-label">
                    <input type="checkbox" class="switch-input" onchange="submitToggle('{{ $key }}', this.checked)" {{ \App\Models\Setting::get($key, false) ? 'checked' : '' }}>
                    <span class="switch-slider"></span>
                </label>
            </div>
            @endforeach
        </form>
    </div>

    <div class="section-card">
        <div class="section-title"><i data-feather="user-check"></i> Recent Users</div>
        <table>
            <thead>
                <tr><th>User</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($recent_users as $user)
                <tr>
                    <td>
                        <div class="font-medium">{{ $user->email }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->activeSubscription->plan->name ?? 'No Plan' }}</div>
                    </td>
                    <td><span class="status-pill {{ $user->status == 'ACTIVE' ? 'status-active' : 'status-suspended' }}">{{ $user->status }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function submitToggle(key, value) {
        document.getElementById('switchKey').value = key;
        document.getElementById('switchValue').value = value ? 1 : 0;
        document.getElementById('switchForm').submit();
    }
</script>
@endsection
