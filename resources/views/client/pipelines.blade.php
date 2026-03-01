@extends('layouts.client_new')

@section('title', 'My Pipelines')
@section('header_title', 'My News Pipelines')
@section('header_subtitle', 'Detailed configuration of your automated news delivery')

@section('content')
@php $plan = $user->activeSubscription->plan ?? null; @endphp

@if($plan)
    <div class="section-card">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold flex items-center gap-2 text-gray-900 dark:text-white"><i data-feather="activity" class="text-[#FF2D20]"></i> Active Pipelines</h3>
            <span class="status-pill bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 border border-green-200 dark:border-green-500/20">Plan: {{ $plan->name }}</span>
        </div>

        <div class="space-y-4">
            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl">
                <span class="font-medium text-gray-900 dark:text-white">Pipeline A: Daily Market Outlook</span>
                <span class="status-pill {{ $plan->daily_outlook ? 'status-active' : 'status-suspended' }}">{{ $plan->daily_outlook ? 'ACTIVE' : 'DISABLED' }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl">
                <span class="font-medium text-gray-900 dark:text-white">Pipeline B: Upcoming Event Alerts</span>
                <span class="status-pill {{ $plan->upcoming_event_alerts ? 'status-active' : 'status-suspended' }}">{{ $plan->upcoming_event_alerts ? 'ACTIVE' : 'DISABLED' }}</span>
            </div>
            <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl">
                <span class="font-medium text-gray-900 dark:text-white">Pipeline C: Post-Event Reactions</span>
                <span class="status-pill {{ $plan->post_event_reaction ? 'status-active' : 'status-suspended' }}">{{ $plan->post_event_reaction ? 'ACTIVE' : 'DISABLED' }}</span>
            </div>
        </div>
    </div>

    <div class="section-card">
        <h3 class="text-xl font-bold mb-2 flex items-center gap-2 text-gray-900 dark:text-white"><i data-feather="map" class="text-[#FF2D20]"></i> Delivery Metadata</h3>
        <p class="text-gray-500 dark:text-[#94A3B8] text-sm mb-6">These endpoints are used by n8n to deliver your news automations.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 dark:bg-[#111114] p-6 rounded-2xl border border-gray-200 dark:border-white/10">
                <div class="text-xs text-gray-500 dark:text-[#94A3B8] font-bold mb-2 uppercase tracking-wider">Telegram Chat ID</div>
                <div class="font-mono text-[#FF2D20] break-all">{{ $plan->telegram_chat_id ?? 'Not Set' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-[#111114] p-6 rounded-2xl border border-gray-200 dark:border-white/10">
                <div class="text-xs text-gray-500 dark:text-[#94A3B8] font-bold mb-2 uppercase tracking-wider">Tags Filter</div>
                <div class="font-mono text-[#FF2D20] break-all">{{ $plan->tags ?? 'Default' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-[#111114] p-6 rounded-2xl border border-gray-200 dark:border-white/10">
                <div class="text-xs text-gray-500 dark:text-[#94A3B8] font-bold mb-2 uppercase tracking-wider">Hashtags</div>
                <div class="font-mono text-[#FF2D20] text-sm break-all">{{ $plan->hashtags ?? 'None' }}</div>
            </div>
            <div class="bg-gray-50 dark:bg-[#111114] p-6 rounded-2xl border border-gray-200 dark:border-white/10">
                <div class="text-xs text-gray-500 dark:text-[#94A3B8] font-bold mb-2 uppercase tracking-wider">Delivery Channels</div>
                <div class="text-gray-900 dark:text-white font-medium">
                    {{ $plan->enable_telegram ? 'Telegram' : '' }}
                    {{ $plan->enable_blotato ? ', Blotato' : '' }}
                    {{ $plan->enable_email ? ', Email' : '' }}
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-8 mb-8">
        <!-- Signal Alert Status Card -->
        <div class="section-card !mb-0 border-l-4 {{ $plan->enable_signal_alert ? 'border-l-green-500' : 'border-l-gray-400' }} flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <i data-feather="zap" class="{{ $plan->enable_signal_alert ? 'text-green-500' : 'text-gray-400' }}"></i>
                    <h3 class="text-xl font-bold m-0 text-gray-900 dark:text-white">Signal Alert Status</h3>
                </div>
                <p class="text-gray-500 dark:text-[#94A3B8] text-sm m-0">Feature access based on your current <strong class="text-gray-900 dark:text-white">{{ $plan->name }}</strong> plan.</p>
            </div>
            <span class="status-pill {{ $plan->enable_signal_alert ? 'status-active' : 'status-suspended' }}">
                {{ $plan->enable_signal_alert ? 'ACTIVE' : 'DISABLED' }}
            </span>
        </div>

        @if($plan->enable_signal_alert)
        <div class="section-card border-[#FF2D20] dark:border-[#FF2D20]/30 bg-red-50/30 dark:bg-red-500/5">
            <div class="flex items-center gap-3 mb-6">
                <i data-feather="settings" class="text-[#FF2D20]"></i>
                <h3 class="text-xl font-bold m-0 text-gray-900 dark:text-white">Signal Bot Configuration</h3>
            </div>
            <form action="{{ route('client.settings.bot') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Signal Bot Token</label>
                        <input type="text" name="signal_bot_token" value="{{ $user->signal_bot_token }}" placeholder="123456:ABC..." class="input-field">
                    </div>
                    <div>
                        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Signal Telegram Chat</label>
                        <input type="text" name="signal_telegram_chat" value="{{ $user->signal_telegram_chat }}" placeholder="-100..." class="input-field">
                    </div>
                </div>
                <button type="submit" class="btn-primary mt-6">Save Bot Settings</button>
            </form>
        </div>
        @endif
    </div>
@else
    <div class="section-card text-center py-16">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-2xl mb-6">
            <i data-feather="slash" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h2 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">No Active Subscription</h2>
        <p class="text-gray-500 dark:text-[#94A3B8] text-lg mb-8">Subscribe to a plan to activate your news pipelines.</p>
        <a href="/#pricing" class="btn-primary inline-block py-4 px-10 text-lg rounded-xl shadow-md">View Plans</a>
    </div>
@endif
@endsection
