@extends('layouts.admin')

@section('title', 'Edit Transaction')

@section('content')
<header class="mb-8">
    <a href="{{ route('admin.transactions') }}" class="text-gray-500 hover:text-[#FF2D20] flex items-center gap-2 mb-4 no-underline">
        <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Transactions
    </a>
    <h2 class="text-2xl font-bold">Edit Transaction #{{ $transaction->id }}</h2>
</header>

@if ($errors->any())
    <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-8">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="section-card max-w-2xl">
    <div class="section-title"><i data-feather="edit"></i> Transaction Details</div>
    
    <form action="{{ route('admin.transactions.update', $transaction) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">User</label>
                <div class="px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-gray-500">
                    {{ $transaction->user->email ?? 'N/A' }}
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Plan</label>
                <div class="px-4 py-3 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl text-gray-500">
                    {{ $transaction->plan->name ?? 'N/A' }}
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Amount (USD)</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" class="auth-input w-full" required>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reference ID (Gateway ID)</label>
            <input type="text" name="reference_id" value="{{ old('reference_id', $transaction->reference_id) }}" class="auth-input w-full">
        </div>

        <div class="mb-8">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select name="status" class="auth-input w-full">
                <option value="PENDING" {{ old('status', $transaction->status) == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                <option value="SUCCESS" {{ old('status', $transaction->status) == 'SUCCESS' ? 'selected' : '' }}>SUCCESS</option>
                <option value="FAILED" {{ old('status', $transaction->status) == 'FAILED' ? 'selected' : '' }}>FAILED</option>
            </select>
        </div>

        <div class="flex gap-4">
            <button type="submit" class="btn-primary flex-grow">Update Transaction</button>
            <a href="{{ route('admin.transactions') }}" class="btn-secondary text-center">Cancel</a>
        </div>
    </form>
</div>
@endsection
