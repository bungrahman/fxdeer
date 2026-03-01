@extends('layouts.admin')

@section('title', 'Edit Signal')

@section('content')
<header>
    <div>
        <h2>Edit Signal</h2>
        <div style="font-size: 0.9rem; color: var(--text-dim);">Update signal data for {{ $signal->pair }}</div>
    </div>
    <a href="{{ route('admin.signals') }}" style="color: var(--text-dim); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 0.5rem;">
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

<form action="{{ route('admin.signals.update', $signal) }}" method="POST">
    @csrf @method('PUT')

    <div class="section-card">
        <div class="section-title"><i data-feather="trending-up"></i> Signal Data</div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem;">
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Pair *</label>
                <input type="text" name="pair" value="{{ old('pair', $signal->pair) }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="BTC/USD">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Signal *</label>
                <select name="signal" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;">
                    <option value="BUY" {{ old('signal', $signal->signal) === 'BUY' ? 'selected' : '' }}>BUY</option>
                    <option value="SELL" {{ old('signal', $signal->signal) === 'SELL' ? 'selected' : '' }}>SELL</option>
                </select>
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Price *</label>
                <input type="text" name="price" value="{{ old('price', $signal->price) }}" required style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="0.123">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Stop Loss (SL)</label>
                <input type="text" name="sl" value="{{ old('sl', $signal->sl) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="0.121714">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Take Profit (TP)</label>
                <input type="text" name="tp" value="{{ old('tp', $signal->tp) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="0.124929">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Confidence Level</label>
                <select name="conf_level" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;">
                    <option value="">— None —</option>
                    <option value="HIGH" {{ old('conf_level', $signal->conf_level) === 'HIGH' ? 'selected' : '' }}>HIGH</option>
                    <option value="MEDIUM" {{ old('conf_level', $signal->conf_level) === 'MEDIUM' ? 'selected' : '' }}>MEDIUM</option>
                    <option value="LOW" {{ old('conf_level', $signal->conf_level) === 'LOW' ? 'selected' : '' }}>LOW</option>
                </select>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Score</label>
                <input type="text" name="score" value="{{ old('score', $signal->score) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="4">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Stars</label>
                <input type="text" name="stars" value="{{ old('stars', $signal->stars) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="⭐⭐⭐⭐">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Timestamp</label>
                <input type="text" name="signal_timestamp" value="{{ old('signal_timestamp', $signal->signal_timestamp) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="2026-02-22 2:12 PM">
            </div>
        </div>

        <div style="margin-top: 1.5rem;">
            <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Reason</label>
            <input type="text" name="reason" value="{{ old('reason', $signal->reason) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;" placeholder="A | TREND | RR 1.50:1">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Last SL</label>
                <input type="text" name="last_sl" value="{{ old('last_sl', $signal->last_sl) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Last TP</label>
                <input type="text" name="last_tp" value="{{ old('last_tp', $signal->last_tp) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;">
            </div>
            <div>
                <label style="display: block; color: var(--text-dim); margin-bottom: 0.5rem; font-size: 0.85rem;">Result</label>
                <input type="text" name="result" value="{{ old('result', $signal->result) }}" style="width: 100%; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; padding: 0.8rem; border-radius: 10px; outline: none;">
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('admin.signals') }}" style="padding: 0.8rem 2rem; border-radius: 10px; color: var(--text-dim); text-decoration: none; border: 1px solid var(--glass-border);">Cancel</a>
            <button type="submit" style="background: var(--primary); color: white; padding: 0.8rem 2rem; border-radius: 10px; border: none; font-weight: 600; cursor: pointer;">Update Signal</button>
        </div>
    </div>
</form>
@endsection
