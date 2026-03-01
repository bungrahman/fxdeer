@extends('layouts.admin')

@section('title', 'New Signal')

@section('content')
<header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold mb-1">New Signal</h2>
        <div class="text-gray-500 dark:text-[#94A3B8] text-sm">Trading signals received from TwelveData</div>
    </div>
    <div class="flex flex-col md:flex-row gap-4 items-center w-full md:w-auto">
        <button type="button" id="bulk-delete-btn" onclick="submitBulkDelete()" class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 px-6 py-3 rounded-xl border border-red-200 dark:border-red-500/20 font-bold hidden items-center gap-2 cursor-pointer transition-colors hover:bg-red-100 dark:hover:bg-red-500/20">
            <i data-feather="trash-2" class="w-4 h-4"></i> Delete Selected (<span id="selected-count">0</span>)
        </button>
        <a href="{{ route('admin.signals.create') }}" class="btn-primary flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Add Signal
        </a>
    </div>
</header>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 p-4 rounded-xl mb-8 border border-green-200 dark:border-green-500/20">
        {{ session('success') }}
    </div>
@endif

<form id="bulk-delete-form" action="{{ route('admin.signals.bulk-delete') }}" method="POST">
    @csrf
    <div class="section-card" style="overflow-x: auto;">
        @if($signals->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--primary);">
                        </th>
                        <th>Pair</th>
                        <th>Signal</th>
                        <th>Price</th>
                        <th>SL</th>
                        <th>TP</th>
                        <th>Score</th>
                        <th>Confidence</th>
                        <th>Reason</th>
                        <th>Result</th>
                        <th>Timestamp</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($signals as $signal)
                        <tr>
                            <td>
                                <input type="checkbox" name="ids[]" value="{{ $signal->id }}" class="signal-checkbox" onchange="updateBulkButton()" style="cursor: pointer; width: 16px; height: 16px; accent-color: var(--primary);">
                            </td>
                            <td style="font-weight: 600; font-family: monospace;">{{ $signal->pair }}</td>
                            <td>
                                <span class="status-pill" style="background: {{ $signal->signal === 'BUY' ? 'rgba(34,197,94,0.1)' : 'rgba(239,68,68,0.1)' }}; color: {{ $signal->signal === 'BUY' ? 'var(--success)' : 'var(--danger)' }};">
                                    {{ $signal->signal }}
                                </span>
                            </td>
                            <td style="font-family: monospace; font-size: 0.85rem;">{{ $signal->price }}</td>
                            <td style="font-family: monospace; font-size: 0.85rem; color: var(--danger);">{{ $signal->sl ?: '-' }}</td>
                            <td style="font-family: monospace; font-size: 0.85rem; color: var(--success);">{{ $signal->tp ?: '-' }}</td>
                            <td>
                                @if($signal->stars)
                                    <span style="font-size: 0.8rem;">{{ $signal->stars }}</span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">{{ $signal->score ?: '-' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($signal->conf_level)
                                    @php
                                        $confColors = ['HIGH' => '#22c55e', 'MEDIUM' => '#f59e0b', 'LOW' => '#ef4444'];
                                        $confColor = $confColors[$signal->conf_level] ?? 'var(--text-dim)';
                                    @endphp
                                    <span style="color: {{ $confColor }}; font-weight: 600; font-size: 0.85rem;">{{ $signal->conf_level }}</span>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-dim); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $signal->reason }}">{{ $signal->reason ?: '-' }}</td>
                            <td>
                                @if($signal->result)
                                    <span style="font-size: 0.85rem; font-weight: 600;">{{ $signal->result }}</span>
                                @else
                                    <span style="color: var(--text-dim); font-size: 0.8rem;">Pending</span>
                                @endif
                            </td>
                            <td style="color: var(--text-dim); font-size: 0.8rem; white-space: nowrap;">{{ $signal->signal_timestamp ?: $signal->created_at->format('Y-m-d H:i') }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a href="{{ route('admin.signals.edit', $signal) }}" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.5rem 0.8rem; border-radius: 8px; text-decoration: none; font-size: 0.8rem;">
                                        <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                    </a>
                                    <form action="{{ route('admin.signals.delete', $signal) }}" method="POST" onsubmit="return confirm('Delete this signal?')" style="margin: 0;">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background: rgba(239, 68, 68, 0.1); color: var(--danger); padding: 0.5rem 0.8rem; border-radius: 8px; border: none; cursor: pointer;">
                                            <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 1.5rem;">
                {{ $signals->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem; color: var(--text-dim);">
                <i data-feather="trending-up" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.3;"></i>
                <p>No signals yet. Signals will appear here when received via API.</p>
            </div>
        @endif
    </div>
</form>

<!-- Daily Signal Stats -->
<div class="section-card" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div class="section-title" style="margin-bottom: 0;"><i data-feather="bar-chart-2"></i> Daily Signal Stats</div>
        <form action="{{ route('admin.signals') }}" method="GET" style="display: flex; align-items: center; gap: 0.75rem;">
            <input type="date" name="stats_date" value="{{ $stats['date'] }}" style="background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.6rem 1rem; border-radius: 10px; outline: none; font-family: 'Outfit', sans-serif;" onchange="this.form.submit()">
        </form>
    </div>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 16px; padding: 1.25rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700;">{{ $stats['total_signals'] }}</div>
            <div style="color: var(--text-dim); font-size: 0.8rem; margin-top: 0.25rem;">Total Signals</div>
        </div>
        <div style="background: rgba(34,197,94,0.05); border: 1px solid rgba(34,197,94,0.15); border-radius: 16px; padding: 1.25rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--success);">{{ $stats['buy_count'] }}</div>
            <div style="color: var(--text-dim); font-size: 0.8rem; margin-top: 0.25rem;">BUY</div>
        </div>
        <div style="background: rgba(239,68,68,0.05); border: 1px solid rgba(239,68,68,0.15); border-radius: 16px; padding: 1.25rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--danger);">{{ $stats['sell_count'] }}</div>
            <div style="color: var(--text-dim); font-size: 0.8rem; margin-top: 0.25rem;">SELL</div>
        </div>
        <div style="background: rgba(245,158,11,0.05); border: 1px solid rgba(245,158,11,0.15); border-radius: 16px; padding: 1.25rem; text-align: center;">
            <div style="font-size: 2rem; font-weight: 700; color: var(--warning);">{{ $stats['win_rate'] }}</div>
            <div style="color: var(--text-dim); font-size: 0.8rem; margin-top: 0.25rem;">Win Rate</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Total</th>
                <th>BUY</th>
                <th>SELL</th>
                <th>TP Hits</th>
                <th>SL Hits</th>
                <th>Win Rate</th>
                <th>Top Pair</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: 600;">{{ $stats['date'] }}</td>
                <td>{{ $stats['total_signals'] }}</td>
                <td style="color: var(--success); font-weight: 600;">{{ $stats['buy_count'] }}</td>
                <td style="color: var(--danger); font-weight: 600;">{{ $stats['sell_count'] }}</td>
                <td style="color: var(--success);">{{ $stats['tp_hits'] }}</td>
                <td style="color: var(--danger);">{{ $stats['sl_hits'] }}</td>
                <td style="font-weight: 700;">{{ $stats['win_rate'] }}</td>
                <td style="font-family: monospace; font-weight: 600;">{{ $stats['top_pair'] }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection

@section('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.signal-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkButton();
    }

    function updateBulkButton() {
        const checked = document.querySelectorAll('.signal-checkbox:checked');
        const btn = document.getElementById('bulk-delete-btn');
        const count = document.getElementById('selected-count');
        count.innerText = checked.length;
        btn.style.display = checked.length > 0 ? 'flex' : 'none';
    }

    function submitBulkDelete() {
        const checked = document.querySelectorAll('.signal-checkbox:checked');
        if (checked.length === 0) return;
        if (!confirm(`Delete ${checked.length} signal(s)?`)) return;
        document.getElementById('bulk-delete-form').submit();
    }
</script>
@endsection
