@extends('layouts.client_new')

@section('title', 'My Profile')
@section('header_title', 'My Profile')
@section('header_subtitle', 'Manage your account settings and password')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="section-card">
        <div class="section-title"><i data-feather="user"></i> Account Information</div>
        
        <form action="{{ route('client.profile.update') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-sm border border-red-200 dark:border-red-500/20">
                    <ul class="list-disc ml-4">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-6">
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Display Name</label>
                <div class="relative">
                    <i data-feather="user" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="auth-input" placeholder="Your Name" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Email Address</label>
                <div class="relative">
                    <i data-feather="mail" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
                    <input type="email" value="{{ $user->email }}" class="auth-input opacity-60 bg-gray-50 dark:bg-white/5" disabled>
                </div>
                <p class="text-[10px] text-gray-400 mt-1 italic">Email address cannot be changed.</p>
            </div>

            <hr class="border-gray-100 dark:border-white/10 mb-8 mt-8">
            
            <div class="section-title text-sm opacity-80 mb-6"><i data-feather="lock" class="w-4 h-4"></i> Security (Change Password)</div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">New Password</label>
                    <div class="relative">
                        <i data-feather="key" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
                        <input type="password" name="password" class="auth-input" placeholder="••••••••">
                    </div>
                </div>
                <div>
                    <label class="block text-gray-500 dark:text-[#94A3B8] text-sm mb-2">Confirm Password</label>
                    <div class="relative">
                        <i data-feather="shield" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-[18px] h-[18px]"></i>
                        <input type="password" name="password_confirmation" class="auth-input" placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-8 py-3">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
