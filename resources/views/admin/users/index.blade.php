@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold mb-1">Users Management</h2>
    </div>
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 px-4 py-3 rounded-xl flex items-center">
            <span class="text-gray-500 dark:text-[#94A3B8] text-sm font-medium">Total: {{ $users->total() }}</span>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i> Add New User
        </a>
    </div>
</header>
 
@if(session('success'))
    <div class="bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 p-4 rounded-xl mb-8 border border-green-200 dark:border-green-500/20">
        {{ session('success') }}
    </div>
@endif

<div class="section-card">
    <div class="section-title"><i data-feather="users"></i> Registered Users</div>
    <div class="overflow-x-auto">
        <table>
        <thead>
            <tr>
                <th class="whitespace-nowrap">ID</th>
                <th class="whitespace-nowrap">Name</th>
                <th class="whitespace-nowrap">Email</th>
                <th class="whitespace-nowrap">Plan</th>
                <th class="whitespace-nowrap">Status</th>
                <th class="whitespace-nowrap">Joined</th>
                <th class="whitespace-nowrap">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td class="text-gray-500 dark:text-gray-400 whitespace-nowrap">#{{ $user->id }}</td>
                <td class="font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $user->name ?? 'N/A' }}</td>
                <td class="text-gray-500 dark:text-gray-400 font-medium whitespace-nowrap">{{ $user->email }}</td>
                <td class="whitespace-nowrap">
                    <span class="text-[#FF2D20]">{{ $user->activeSubscription->plan->name ?? 'No Plan' }}</span>
                </td>
                <td class="whitespace-nowrap">
                    <span class="status-pill {{ $user->status == 'ACTIVE' ? 'status-active' : 'status-suspended' }}">{{ $user->status }}</span>
                </td>
                <td class="text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="whitespace-nowrap">
                    <div style="display: flex; gap: 0.5rem;">
                        <a href="{{ route('admin.users.edit', $user) }}" class="status-pill bg-white dark:bg-white/5 text-gray-700 dark:text-white border border-gray-200 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors cursor-pointer text-xs">Edit</a>
                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure?')" style="display: inline;">
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
    
    <div style="margin-top: 2rem;">
        {{ $users->links() }}
    </div>
</div>
@endsection
