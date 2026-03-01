@extends('layouts.admin')

@section('title', 'Transactions Management')

@section('content')
<header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold mb-1">Transactions</h2>
        <p class="text-gray-500 dark:text-[#94A3B8] text-sm">Monitor all payments and subscription renewals.</p>
    </div>
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 px-4 py-3 rounded-xl flex items-center">
            <span class="text-gray-500 dark:text-[#94A3B8] text-sm font-medium">Total: {{ $transactions->total() }}</span>
        </div>
    </div>
</header>

<div class="section-card">
    <div class="section-title"><i data-feather="dollar-sign"></i> All Transactions</div>
    <div class="overflow-x-auto">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>User</th>
                    <th>Plan</th>
                    <th>Amount</th>
                    <th>Gateway</th>
                    <th>Status</th>
                    <th>Reference ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr>
                    <td class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $transaction->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td>
                        <div class="flex flex-col">
                            <span class="font-medium truncate max-w-[150px]">{{ $transaction->user->email ?? 'Deactivated User' }}</span>
                            <span class="text-[10px] text-gray-400">ID: #{{ $transaction->user_id }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="text-[#FF2D20] font-medium">{{ $transaction->plan->name ?? 'Unknown Plan' }}</span>
                    </td>
                    <td class="font-bold">
                        ${{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>
                        <span class="status-pill bg-gray-100 dark:bg-white/5 text-gray-600 dark:text-gray-400 capitalize">
                            {{ $transaction->gateway }}
                        </span>
                    </td>
                    <td>
                        @php
                            $statusClass = match($transaction->status) {
                                'SUCCESS' => 'status-active',
                                'PENDING' => 'status-suspended',
                                'FAILED' => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 border-red-200 dark:border-red-500/20',
                                default => 'bg-gray-50 text-gray-400'
                            };
                        @endphp
                        <span class="status-pill {{ $statusClass }}">{{ $transaction->status }}</span>
                    </td>
                    <td>
                        <code class="text-[10px] bg-gray-50 dark:bg-white/5 px-2 py-1 rounded border border-gray-100 dark:border-white/5 text-gray-500">
                            {{ $transaction->reference_id ?? 'N/A' }}
                        </code>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.transactions.edit', $transaction) }}" class="status-pill bg-white dark:bg-white/5 text-gray-700 dark:text-white border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors cursor-pointer text-xs">Edit</a>
                            <form action="{{ route('admin.transactions.delete', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="status-pill bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 border border-red-200 dark:border-red-500/20 hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors cursor-pointer text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-8">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
