@extends('layouts.admin')

@section('title', 'Signal Config')

@section('content')
<header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold mb-1">Signal Config</h2>
        <div class="text-gray-500 dark:text-[#94A3B8] text-sm">Manage TwelveData pairs & API keys</div>
    </div>
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <a href="{{ route('admin.signal-configs.create') }}" class="btn-primary flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Add Config
        </a>
    </div>
</header>

@if(session('success'))
    <div class="bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 p-4 rounded-xl mb-8 border border-green-200 dark:border-green-500/20">
        {{ session('success') }}
    </div>
@endif

<div class="section-card">
    @if($configs->count() > 0)
        <div class="overflow-x-auto">
            <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Status</th>
                    <th>Pairs</th>
                    <th>API Keys</th>
                    <th>Created</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($configs as $index => $config)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <span class="status-pill {{ $config->status === 'active' ? 'status-active' : 'status-suspended' }}">
                                {{ ucfirst($config->status) }}
                            </span>
                        </td>
                        <td>
                            @php $pairsArr = explode(',', $config->pairs); @endphp
                            <div style="display: flex; flex-wrap: wrap; gap: 0.3rem; max-width: 400px;">
                                @foreach(array_slice($pairsArr, 0, 5) as $pair)
                                    <span style="background: rgba(255,255,255,0.05); padding: 0.2rem 0.5rem; border-radius: 6px; font-size: 0.75rem; font-family: monospace;">{{ trim($pair) }}</span>
                                @endforeach
                                @if(count($pairsArr) > 5)
                                    <span style="color: var(--text-dim); font-size: 0.75rem; padding: 0.2rem 0.5rem;">+{{ count($pairsArr) - 5 }} more</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php $keysCount = count(explode(',', $config->api_keys)); @endphp
                            <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $keysCount }} key(s)</span>
                        </td>
                        <td class="text-gray-500 dark:text-gray-400 text-sm">{{ $config->created_at->format('d M Y') }}</td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="{{ route('admin.signal-configs.edit', $config) }}" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; padding: 0.5rem 0.8rem; border-radius: 8px; text-decoration: none; font-size: 0.8rem;">
                                    <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                </a>
                                <form action="{{ route('admin.signal-configs.delete', $config) }}" method="POST" onsubmit="return confirm('Delete this config?')">
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
        </div>
    @else
        <div style="text-align: center; padding: 3rem; color: var(--text-dim);">
            <i data-feather="cpu" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.3;"></i>
            <p>No signal configs yet. Create your first one.</p>
        </div>
    @endif
</div>
@endsection
