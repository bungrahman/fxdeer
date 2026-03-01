@extends('layouts.client_new')

@section('title', 'Client Dashboard')
@section('header_title', 'Welcome, ' . Auth::user()->email)
@section('header_subtitle', 'Manage your automated news flows')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="section-card !mb-0">
        <div class="text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Active Plan</div>
        <div class="text-3xl font-bold text-[#FF2D20]">{{ Auth::user()->activeSubscription->plan->name ?? 'NO ACTIVE PLAN' }}</div>
        @if(!Auth::user()->activeSubscription)
            <a href="/#pricing" class="btn-primary mt-6 block text-center">Choose a Plan</a>
        @endif
    </div>

    <div class="section-card !mb-0">
        <div class="text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Remaining Alerts Today</div>
        <div class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ ($user->activeSubscription->plan->max_alerts_per_day ?? 0) - $usageToday }} / {{ $user->activeSubscription->plan->max_alerts_per_day ?? 0 }}
        </div>
    </div>
</div>

<div class="section-card mb-8">
    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-white">Pipeline Status</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 p-4 rounded-2xl text-center">
            <div class="text-gray-500 dark:text-[#94A3B8] text-xs font-bold mb-2 uppercase">DAILY OUTLOOK</div>
            <div class="font-bold text-gray-900 dark:text-white">{{ (Auth::user()->activeSubscription->plan->daily_outlook ?? false) ? 'ENABLED' : 'DISABLED' }}</div>
        </div>
        <div class="bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 p-4 rounded-2xl text-center">
            <div class="text-gray-500 dark:text-[#94A3B8] text-xs font-bold mb-2 uppercase">UPCOMING ALERTS</div>
            <div class="font-bold text-gray-900 dark:text-white">{{ (Auth::user()->activeSubscription->plan->upcoming_event_alerts ?? false) ? 'ENABLED' : 'DISABLED' }}</div>
        </div>
    </div>
</div>

@php
    $subscription = Auth::user()->activeSubscription;
    $plan = $subscription ? $subscription->plan : null;
@endphp

@if($plan)
    <div class="space-y-8 mb-8">
        <!-- Language Settings Card -->
        <div class="section-card !mb-0">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <i data-feather="globe" class="text-[#FF2D20]"></i>
                    <h3 class="text-xl font-bold m-0 text-gray-900 dark:text-white">Language Settings</h3>
                </div>
                <span class="status-pill inline-block bg-red-50 dark:bg-red-500/10 text-[#FF2D20]">Default: {{ strtoupper(Auth::user()->default_language) }}</span>
            </div>
            <p class="text-gray-500 dark:text-[#94A3B8] text-sm mb-6">Choose your preferred language for news delivery and alerts.</p>
            
            <form action="{{ route('client.settings.language') }}" method="POST" class="flex flex-col md:flex-row gap-4">
                @csrf
                <select name="default_language" class="input-field cursor-pointer py-3 appearance-none flex-1">
                    @foreach(\App\Models\Setting::getSupportedLanguages() as $lang)
                        <option value="{{ $lang['code'] }}" {{ Auth::user()->default_language == $lang['code'] ? 'selected' : '' }} class="bg-white dark:bg-[#17171a] text-gray-900 dark:text-white">
                            {{ $lang['name'] }} ({{ strtoupper($lang['code']) }})
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-primary py-3 px-8">Save</button>
            </form>
        </div>

        @if($plan->enable_signal_alert)
        <!-- Signal Bot Configuration Card -->
        <div class="section-card !mb-0 border-[#FF2D20] dark:border-[#FF2D20]/30 bg-red-50/30 dark:bg-red-500/5">
            <div class="flex items-center gap-3 mb-6">
                <i data-feather="settings" class="text-[#FF2D20]"></i>
                <h3 class="text-xl font-bold m-0 text-gray-900 dark:text-white">Signal Bot Configuration</h3>
            </div>
            <form action="{{ route('client.settings.bot') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Signal Bot Token</label>
                        <input type="text" name="signal_bot_token" value="{{ Auth::user()->signal_bot_token }}" placeholder="123456:ABC..." class="input-field">
                    </div>
                    <div>
                        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Signal Telegram Chat</label>
                        <input type="text" name="signal_telegram_chat" value="{{ Auth::user()->signal_telegram_chat }}" placeholder="-100..." class="input-field">
                    </div>
                </div>
                <button type="submit" class="btn-primary mt-6">Save Bot Settings</button>
            </form>
        </div>
        @endif
    </div>
@endif
@endsection
