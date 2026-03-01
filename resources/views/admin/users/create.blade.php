@extends('layouts.admin')

@section('title', 'Create New User')

@section('content')
<header>
    <h2>Add New User</h2>
    <a href="{{ route('admin.users') }}" class="status-pill inline-block bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-400 text-sm hover:text-gray-900 dark:hover:text-white transition-colors py-2 px-4">Back to Users</a>
</header>

<div class="section-card max-w-4xl">
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
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
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Full Name</label>
                <input type="text" name="name" class="input-field" placeholder="John Doe" required autofocus>
            </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Email Address</label>
                <input type="email" name="email" class="input-field" placeholder="user@example.com" required>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Initial Password</label>
                <input type="password" name="password" class="input-field" placeholder="Min. 8 characters" required>
            </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Status</label>
                <select name="status" class="input-field">
                    <option value="ACTIVE">ACTIVE</option>
                    <option value="SUSPENDED">SUSPENDED</option>
                </select>
            </div>
        </div>
            <div>
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Assign Plan</label>
                <select name="plan_id" class="input-field">
                    @foreach($plans as $plan)
                    <option value="{{ $plan->id }}">{{ $plan->name }} (${{ $plan->price }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom: 2rem;">
            <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Default Language</label>
            <select name="default_language" class="input-field" required>
                @foreach($languages as $lang)
                    <option value="{{ $lang['code'] }}">{{ $lang['name'] }} ({{ strtoupper($lang['code']) }})</option>
                @endforeach
            </select>
            <input type="hidden" name="timezone" value="UTC">
        </div>
        
        <button type="submit" class="btn-primary w-full">Register User & Activate Plan</button>
    </form>
</div>
@endsection
