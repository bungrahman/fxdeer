@extends('layouts.auth')

@section('title', 'Register')
@section('subtitle', 'Join the news automation revolution')

@section('content')
@if($errors->any())
    <div class="auth-error-msg">
        {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('register.post') }}" method="POST">
    @csrf
    <input type="hidden" name="plan_id" value="{{ request()->query('plan') }}">
    
    <div class="mb-6">
        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Email Address</label>
        <div class="relative">
            <i data-feather="mail" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
            <input type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="name@company.com" required>
        </div>
    </div>

    <div class="mb-6">
        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Password</label>
        <div class="relative">
            <i data-feather="lock" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
            <input type="password" name="password" class="auth-input" placeholder="••••••••" required>
        </div>
    </div>

    <div class="mb-6">
        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Confirm Password</label>
        <div class="relative">
            <i data-feather="shield" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
            <input type="password" name="password_confirmation" class="auth-input" placeholder="••••••••" required>
        </div>
    </div>

    <div class="mb-6">
        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Preferred Language</label>
        <div class="relative">
            <i data-feather="globe" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
            <select name="default_language" class="auth-input appearance-none px-12">
                @foreach($languages as $lang)
                    <option value="{{ $lang['code'] }}" class="bg-white dark:bg-[#17171a] text-gray-900 dark:text-white">{{ $lang['name'] }} ({{ strtoupper($lang['code']) }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <p class="text-xs text-center text-gray-500 dark:text-[#94A3B8] mb-6">
        By signing up, you agree to our <a href="#" class="text-[#FF2D20] no-underline">Terms</a> and <a href="#" class="text-[#FF2D20] no-underline">Privacy Policy</a>.
    </p>

    <button type="submit" class="btn-primary w-full">Create Your Account</button>
</form>

<div class="text-center mt-8 text-sm text-gray-500 dark:text-[#94A3B8]">
    Already have an account? <a href="{{ route('login') }}" class="text-[#FF2D20] font-semibold no-underline hover:underline">Sign in here</a>
</div>
@endsection
