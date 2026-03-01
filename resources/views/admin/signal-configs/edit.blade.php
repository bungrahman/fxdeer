@extends('layouts.admin')

@section('title', 'Edit Signal Config')

@section('content')
<header>
    <div>
        <h2>Edit Signal Config</h2>
        <div style="font-size: 0.9rem; color: var(--text-dim);">Update pairs & API keys configuration</div>
    </div>
    <a href="{{ route('admin.signal-configs') }}" style="color: var(--text-dim); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
        <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i> Back
    </a>
</header>

@if($errors->any())
    <div class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 p-4 rounded-xl mb-8 border border-red-200 dark:border-red-500/20">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('admin.signal-configs.update', $signalConfig) }}" method="POST">
    @csrf @method('PUT')

    <div class="section-card">
        <div class="section-title"><i data-feather="cpu"></i> Configuration</div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Status</label>
            <select name="status" style="width: 100%; max-width: 300px; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;">
                <option value="active" {{ old('status', $signalConfig->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $signalConfig->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Trading Pairs <span style="font-size: 0.75rem; opacity: 0.7;">(comma-separated)</span></label>
            <textarea name="pairs" rows="4" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none; font-family: monospace; font-size: 0.85rem; resize: vertical;" placeholder="BTC/USD,ETH/USD,BNB/USD,SOL/USD">{{ old('pairs', $signalConfig->pairs) }}</textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">TwelveData API Keys <span style="font-size: 0.75rem; opacity: 0.7;">(comma-separated)</span></label>
            <textarea name="api_keys" rows="3" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none; font-family: monospace; font-size: 0.85rem; resize: vertical;" placeholder="key1,key2,key3">{{ old('api_keys', $signalConfig->api_keys) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.signal-configs') }}" style="padding: 0.8rem 2rem; border-radius: 10px; color: var(--text-dim); text-decoration: none; border: 1px solid var(--glass-border);">Cancel</a>
            <button type="submit" style="background: var(--primary); color: white; padding: 0.8rem 2rem; border-radius: 10px; border: none; font-weight: 600; cursor: pointer;">Update Config</button>
        </div>
    </div>
</form>
@endsection
