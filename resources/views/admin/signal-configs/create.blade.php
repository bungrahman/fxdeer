@extends('layouts.admin')

@section('title', 'Create Signal Config')

@section('content')
<header>
    <div>
        <h2>Create Signal Config</h2>
        <div style="font-size: 0.9rem; color: var(--text-dim);">Add new pairs & API keys configuration</div>
    </div>
    <a href="{{ route('admin.signal-configs') }}" class="text-gray-500 hover:text-[#FF2D20] no-underline font-medium flex items-center gap-2">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back to List
    </a>
</header>

@if($errors->any())
    <div class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 p-4 rounded-xl mb-8 border border-red-200 dark:border-red-500/20">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form action="{{ route('admin.signal-configs.store') }}" method="POST">
    @csrf

    <div class="section-card">
        <div class="section-title"><i data-feather="cpu"></i> Configuration</div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Status</label>
            <select name="status" class="input-field max-w-xs">
                <option value="active" {{ old('status') === 'active' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Active</option>
                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }} class="bg-white dark:bg-[#17171a]">Inactive</option>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Trading Pairs <span style="font-size: 0.75rem; opacity: 0.7;">(comma-separated)</span></label>
            <textarea name="pairs" rows="4" class="input-field font-mono" placeholder="BTC/USD,ETH/USD,BNB/USD,SOL/USD">{{ old('pairs') }}</textarea>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">TwelveData API Keys <span style="font-size: 0.75rem; opacity: 0.7;">(comma-separated)</span></label>
            <textarea name="api_keys" rows="3" class="input-field font-mono" placeholder="key1,key2,key3">{{ old('api_keys') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.signal-configs') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary">Create Config</button>
        </div>
    </div>
</form>
@endsection
