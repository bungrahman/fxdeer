@extends('layouts.client_new')

@section('title', 'Billing History')
@section('header_title', 'Billing History')
@section('header_subtitle', 'Track your payments and subscription activity')

@section('content')
@if($transactions->count() > 0)
    <div class="section-card overflow-x-auto">
        <div class="section-title"><i data-feather="credit-card"></i> Your Transactions</div>
        <table class="w-full text-left min-w-[600px]">
            <thead>
                <tr>
                    <th class="py-4 px-4 text-xs font-bold text-gray-500 dark:text-[#94A3B8] uppercase tracking-wider border-b border-gray-200 dark:border-white/10">Date</th>
                    <th class="py-4 px-4 text-xs font-bold text-gray-500 dark:text-[#94A3B8] uppercase tracking-wider border-b border-gray-200 dark:border-white/10">Description</th>
                    <th class="py-4 px-4 text-xs font-bold text-gray-500 dark:text-[#94A3B8] uppercase tracking-wider border-b border-gray-200 dark:border-white/10">Price</th>
                    <th class="py-4 px-4 text-xs font-bold text-gray-500 dark:text-[#94A3B8] uppercase tracking-wider border-b border-gray-200 dark:border-white/10">Gateway</th>
                    <th class="py-4 px-4 text-xs font-bold text-gray-500 dark:text-[#94A3B8] uppercase tracking-wider border-b border-gray-200 dark:border-white/10">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                @foreach($transactions as $tx)
                <tr class="hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                    <td class="py-4 px-4 text-gray-900 dark:text-white">{{ $tx->created_at->format('M d, Y') }}</td>
                    <td class="py-4 px-4 text-gray-900 dark:text-white">Plan: <strong>{{ $tx->plan->name ?? 'Unknown' }}</strong></td>
                    <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">${{ number_format($tx->amount, 2) }}</td>
                    <td class="py-4 px-4 text-xs font-mono uppercase tracking-wider text-gray-500 dark:text-[#94A3B8]">{{ $tx->gateway }}</td>
                    <td class="py-4 px-4">
                        @php
                            $statusClass = match($tx->status) {
                                'SUCCESS' => 'status-active',
                                'PENDING' => 'status-suspended',
                                'FAILED' => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 border-red-200 dark:border-red-500/20',
                                default => 'bg-gray-50 text-gray-400'
                            };
                        @endphp
                        <div class="flex items-center gap-2">
                            <span class="status-pill {{ $statusClass }}">
                                {{ $tx->status }}
                            </span>
                            @if($tx->status === 'PENDING')
                                <a href="{{ route('payment.resume', $tx) }}" class="text-[10px] font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/20 px-2 py-1 rounded-md hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-colors uppercase">
                                    Pay Now
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6 flex justify-center">
        {{ $transactions->links() }}
    </div>
@else
    <div class="section-card text-center py-16">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-2xl mb-6">
            <i data-feather="credit-card" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h2 class="text-3xl font-bold mb-4 text-gray-900 dark:text-white">No Transactions Yet</h2>
        <p class="text-gray-500 dark:text-[#94A3B8] text-lg mb-8">When you purchase a plan, your receipts will appear here.</p>
        <a href="/#pricing" class="btn-primary inline-block py-4 px-10 text-lg rounded-xl shadow-md">Browse Plans</a>
    </div>
@endif
@endsection
