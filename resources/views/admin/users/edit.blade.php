@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->email)

@section('content')
<header>
    <h2>Edit User: <span>{{ $user->email }}</span></h2>
    <a href="{{ route('admin.users') }}" class="status-pill inline-block bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 text-sm hover:text-gray-900 dark:hover:text-white transition-colors py-2 px-4">Back to Users</a>
</header>

<div class="section-card max-w-4xl">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')
        
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 p-4 rounded-xl mb-8 border border-red-200 dark:border-red-500/20">
                <ul style="margin: 0; padding-left: 1.5rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Email Address</label>
                <input type="email" name="email" value="{{ $user->email }}" class="input-field" required>
            </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">New Password (Optional)</label>
                <input type="password" name="password" class="input-field" placeholder="Leave blank to keep current">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Status</label>
                <select name="status" class="input-field">
                    <option value="ACTIVE" {{ $user->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                    <option value="SUSPENDED" {{ $user->status == 'SUSPENDED' ? 'selected' : '' }}>SUSPENDED</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Current Plan</label>
                <select name="plan_id" class="input-field">
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}" {{ ($user->activeSubscription->plan_id ?? null) == $plan->id ? 'selected' : '' }}>
                        {{ $plan->name }} (${{ $plan->price }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Default Language</label>
            <select name="default_language" class="input-field" required>
                @foreach($languages as $lang)
                    <option value="{{ $lang['code'] }}" {{ $user->default_language == $lang['code'] ? 'selected' : '' }}>
                        {{ $lang['name'] }} ({{ strtoupper($lang['code']) }})
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary w-full">Update User Account</button>
    </form>
</div>
@endsection
