@extends('layouts.admin')

@section('title', 'System Logs')

@section('content')
<header>
    <h2>System Logs & Reliability</h2>
    <div style="display: flex; gap: 1rem;">
        <div class="section-card" style="padding: 0.5rem 1rem; background: var(--card-bg); border: 1px solid var(--glass-border); border-radius: 12px; margin-bottom: 0;">
            <span style="color: var(--text-dim); font-size: 0.9rem;">Total Events: {{ $logs->total() }}</span>
        </div>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Usage Logs -->
    <div class="section-card lg:col-span-2">
        <div class="section-title"><i data-feather="terminal"></i> Usage Logs (History)</div>
        <div class="overflow-x-auto">
            <table>
                <thead>
                    <tr>
                        <th class="whitespace-nowrap">User</th>
                        <th class="whitespace-nowrap">Pipeline</th>
                        <th class="whitespace-nowrap">Event ID</th>
                        <th class="whitespace-nowrap">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="whitespace-nowrap">{{ $log->user->email ?? 'System' }}</td>
                        <td class="whitespace-nowrap"><span class="tag" style="background: rgba(255,45,32,0.1); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.7rem;">PIPE {{ $log->pipeline }}</span></td>
                        <td class="whitespace-nowrap" style="font-family: monospace; font-size: 0.8rem; color: var(--text-dim);">{{ $log->event_id }}</td>
                        <td class="text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1.5rem;">{{ $logs->links() }}</div>
    </div>

    <!-- Failed Deliveries -->
    <div class="section-card">
        <div class="section-title" style="color: var(--danger);"><i data-feather="alert-circle"></i> Failed Deliveries</div>
        @forelse($failed as $f)
        <div style="padding: 1rem; border-bottom: 1px solid var(--glass-border);">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span style="font-size: 0.8rem; font-weight: 600;">{{ $f->channel }}</span>
                <span style="font-size: 0.7rem; color: var(--text-dim);">Retry: {{ $f->retry_count }}</span>
            </div>
            <p style="color: var(--danger); font-size: 0.8rem; line-height: 1.4;">{{ $f->error_message }}</p>
            <div style="font-size: 0.7rem; color: var(--text-dim); margin-top: 0.5rem;">{{ $f->created_at->diffForHumans() }}</div>
        </div>
        @empty
        <div style="text-align: center; padding: 2rem; color: var(--text-dim);">
            <i data-feather="check-circle" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.2;"></i>
            <p>No failed deliveries detected.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
