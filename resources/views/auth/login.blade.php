@extends('layouts.auth')

@section('title', 'Login')
@section('subtitle', 'Sign in to manage your news pipelines')

@section('content')
@if($errors->any())
    <div class="auth-error-msg">
        {{ $errors->first() }}
    </div>
@endif

<form action="{{ route('login.post') }}" method="POST">
    @csrf
    <div class="mb-6">
        <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Email Address</label>
        <div class="relative">
            <i data-feather="mail" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
            <input type="email" name="email" value="{{ old('email') }}" class="auth-input" placeholder="name@company.com" required autofocus>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex justify-between items-center mb-2">
            <label class="text-gray-500 dark:text-[#94A3B8] text-sm m-0">Password</label>
            <a href="#" class="text-sm text-[#FF2D20] no-underline hover:underline">Forgot?</a>
        </div>
        <div class="relative">
            <i data-feather="lock" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
            <input type="password" name="password" class="auth-input" placeholder="••••••••" required>
        </div>
    </div>

    <label class="flex items-center gap-3 cursor-pointer mb-8">
        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-[#FF2D20] focus:ring-[#FF2D20]/20 bg-gray-50 dark:bg-black/30 dark:border-white/10">
        <span class="text-sm text-gray-600 dark:text-gray-400">Keep me signed in</span>
    </label>

    <button type="submit" class="btn-primary w-full">Sign In to Dashboard</button>
</form>

<div class="text-center mt-8 text-sm text-gray-500 dark:text-[#94A3B8]">
    Don't have an account? <a href="{{ route('register') }}" class="text-[#FF2D20] font-semibold no-underline hover:underline">Create one now</a>
</div>
@endsection
