@extends('layouts.admin')

@section('title', 'Edit Plan: ' . $plan->name)

@section('content')
<header>
    <h2>Edit Subscription Plan: <span>{{ $plan->name }}</span></h2>
    <a href="{{ route('admin.plans') }}" class="status-pill inline-block bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 text-sm hover:text-gray-900 dark:hover:text-white transition-colors py-2 px-4">Back to Plans</a>
</header>

<style>
    .switch { position: relative; display: inline-block; width: 44px; height: 22px; }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 34px; }
    .slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #FF2D20 !important; }
    input:checked + .slider:before { transform: translateX(22px); }
</style>

<div class="section-card max-w-4xl">
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <!-- ... form fields stay same ... -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Plan Name</label>
                <input type="text" name="name" value="{{ $plan->name }}" class="input-field" required>
            </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Monthly Price ($)</label>
                <input type="number" step="0.01" name="price" value="{{ $plan->price }}" class="input-field" required>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 2rem; margin-bottom: 2rem;">
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Daily Alert Quota</label>
                <input type="number" name="max_alerts_per_day" value="{{ $plan->max_alerts_per_day }}" class="input-field" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Client Tags</label>
                <input type="text" name="tags" value="{{ old('tags', $plan->tags) }}" class="input-field">
            </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">HashTags</label>
                <input type="text" name="hashtags" value="{{ old('hashtags', $plan->hashtags) }}" class="input-field">
            </div>
        </div>

            <!-- Telegram Config REMOVED (Now handled per Language) -->
            <div style="margin-bottom: 2rem; padding: 1.5rem; background: rgba(255,45,32,0.05); border: 1px dashed #FF2D20; border-radius: 16px;">
                 <p style="color: #FF2D20; font-size: 0.85rem; margin: 0; text-align: center;">Telegram Bot Configuration is now managed globally per Language in System Settings.</p>
            </div>

        <input type="hidden" name="channels_allowed[]" value="telegram"> <!-- Default channel -->

        <div style="margin-bottom: 2rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 1rem; font-size: 0.9rem;">Delivery Channels & Features</label>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach([
                        'enable_telegram' => 'Enable Telegram Delivery',
                        'enable_blotato' => 'Enable Blotato Integration',
                        'enable_email' => 'Enable Email Notifications'
                    ] as $key => $label)
                    <label style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <span style="font-size: 0.9rem;">{{ $label }}</span>
                        <label class="switch">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1" {{ $plan->$key ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </label>
                    @endforeach
                </div>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    @foreach([
                        'daily_outlook' => 'Daily Market Outlook',
                        'upcoming_event_alerts' => 'Upcoming News Alerts',
                        'post_event_reaction' => 'Post-Event Sentiment',
                        'enable_signal_alert' => 'Signal Alert Feature'
                    ] as $key => $label)
                    <label style="display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                        <span style="font-size: 0.9rem;">{{ $label }}</span>
                        <label class="switch">
                            <input type="hidden" name="{{ $key }}" value="0">
                            <input type="checkbox" name="{{ $key }}" value="1" {{ $plan->$key ? 'checked' : '' }}>
                            <span class="slider"></span>
                        </label>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" style="width: 100%; background: #FF2D20; color: white; padding: 1.2rem; border-radius: 12px; border: none; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.2s;">Update Subscription Plan</button>
    </form>
</div>
@endsection
