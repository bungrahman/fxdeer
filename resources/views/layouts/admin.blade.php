<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | FXDeer</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <!-- Alpine JS for state management -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body x-data="{ sidebarOpen: false }" :class="{ 'overflow-hidden md:overflow-auto': sidebarOpen }" class="min-h-screen flex flex-col md:flex-row">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-30 md:hidden" @click="sidebarOpen = false"></div>

    <div class="sidebar" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="flex items-center justify-between">
            <a href="/" class="logo">FX<span>Deer</span></a>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-900 dark:hover:text-white">
                <i data-feather="x"></i>
            </button>
        </div>
        
        <div class="nav-links flex flex-col gap-2">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i data-feather="grid"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}"><i data-feather="users"></i> Users</a>
            <a href="{{ route('admin.plans') }}" class="nav-link {{ request()->routeIs('admin.plans') ? 'active' : '' }}"><i data-feather="package"></i> Plans</a>
            <a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions') ? 'active' : '' }}"><i data-feather="dollar-sign"></i> Transactions</a>
            <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}"><i data-feather="activity"></i> Logs</a>
            <a href="{{ route('admin.signal-configs') }}" class="nav-link {{ request()->routeIs('admin.signal-configs*') ? 'active' : '' }}"><i data-feather="cpu"></i> Signal Config</a>
            <a href="{{ route('admin.signals') }}" class="nav-link {{ request()->routeIs('admin.signals*') ? 'active' : '' }}"><i data-feather="trending-up"></i> New Signal</a>
            <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}"><i data-feather="settings"></i> Settings</a>
        </div>
        
        <div class="mt-auto">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-full border-none bg-transparent cursor-pointer text-left">
                    <i data-feather="log-out"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <!-- Top Navigation Area for Mobile & Dark Mode -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300">
                    <i data-feather="menu"></i>
                </button>
                <div class="hidden md:block">
                    <!-- Placeholder for desktop header if needed -->
                </div>
            </div>

            <!-- Dark Mode Toggle -->
            <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 transition-colors hover:bg-gray-50 dark:hover:bg-white/10">
                <i x-show="!darkMode" data-feather="moon" style="display: none;"></i>
                <i x-show="darkMode" data-feather="sun" style="display: none;"></i>
            </button>
        </div>

        @yield('content')
    </div>

    <script>
        // Use Alpine to wait for x-show processing before replacing feather icons,
        // or just replace them immediately and let Alpine manage the visibility.
        document.addEventListener('alpine:initialized', () => {
             feather.replace();
        });
        // Fallback for icons not in Alpine components
        feather.replace();
    </script>
    @yield('scripts')
</body>
</html>
