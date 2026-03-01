@extends('layouts.admin')

@section('title', 'Plans Management')

@section('content')
<header>
    <h2>Subscription Plans</h2>
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="stat-card" style="padding: 0.5rem 1rem; background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 12px; margin-bottom: 0;">
            <span style="color: var(--text-dim); font-size: 0.9rem;">Total: {{ $plans->count() }}</span>
        </div>
        <a href="{{ route('admin.plans.create') }}" class="status-pill inline-block btn-primary text-sm py-2 px-4">+ Create New Plan</a>
    </div>
</header>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 p-4 rounded-xl mb-8 border border-green-200 dark:border-green-500/20">
        {{ session('success') }}
    </div>
@endif

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    @foreach($plans as $plan)
    <div class="section-card" style="margin-bottom: 0; display: flex; flex-direction: column; gap: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="text-[#FF2D20]">{{ $plan->name }}</h3>
            <div style="font-weight: 700; font-size: 1.5rem;">${{ number_format($plan->price, 2) }} <span style="font-size: 0.8rem; color: var(--text-dim);">/mo</span></div>
        </div>
        
        <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 12px; font-size: 0.85rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span class="text-gray-500 dark:text-gray-400">Daily Quota:</span>
                <span>{{ $plan->max_alerts_per_day }} alerts</span>
            </div>

            <div style="display: flex; justify-content: space-between;">
                <span class="text-gray-500 dark:text-gray-400">Channels:</span>
                <span style="text-transform: capitalize;">{{ is_array($plan->channels_allowed) ? implode(', ', $plan->channels_allowed) : $plan->channels_allowed }}</span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @php $features = ['daily_outlook' => 'Daily Outlook', 'upcoming_event_alerts' => 'Upcoming Alerts', 'post_event_reaction' => 'Event Reactions']; @endphp
            @foreach($features as $key => $label)
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: {{ $plan->$key ? 'var(--success)' : '#444' }};">
                <i data-feather="{{ $plan->$key ? 'check-circle' : 'x-circle' }}" style="width: 16px;"></i>
                {{ $label }}
            </div>
            @endforeach
        </div>

        <div style="margin-top: auto; display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.plans.edit', $plan) }}" class="status-pill text-gray-800 dark:text-white" style="flex: 1; text-align: center; padding: 0.8rem; background: var(--card-bg); border: 1px solid var(--glass-border); text-decoration: none; border-radius: 12px;">Edit</a>
            <form action="{{ route('admin.plans.delete', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?')" style="flex: 1; display: flex;">
                @csrf
                @method('DELETE')
                <button type="submit" class="status-pill" style="width: 100%; padding: 0.8rem; background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); cursor: pointer; border-radius: 12px;">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endsection
