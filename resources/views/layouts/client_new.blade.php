<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: false }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Dashboard') - FXDeer</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body :class="{ 'overflow-hidden md:overflow-auto': sidebarOpen }" class="min-h-screen flex flex-col md:flex-row">
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-30 md:hidden" @click="sidebarOpen = false"></div>

    <div class="sidebar" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        <div class="flex items-center justify-between mb-8">
            <a href="/" class="logo flex items-center gap-2"><i data-feather="terminal" class="text-[#FF2D20]"></i> FXDeer</a>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-900 dark:hover:text-white">
                <i data-feather="x"></i>
            </button>
        </div>

        <div class="nav-links flex flex-col gap-2">
            <a href="{{ route('client.dashboard') }}" class="nav-link {{ Route::is('client.dashboard') ? 'active' : '' }}"><i data-feather="grid"></i> Dashboard</a>
            <a href="{{ route('client.pipelines') }}" class="nav-link {{ Route::is('client.pipelines') ? 'active' : '' }}"><i data-feather="settings"></i> My Pipelines</a>
            <a href="{{ route('client.billing') }}" class="nav-link {{ Route::is('client.billing') ? 'active' : '' }}"><i data-feather="credit-card"></i> Billing</a>
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
        <!-- Top Navigation -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="md:hidden p-2 rounded-lg bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300">
                    <i data-feather="menu"></i>
                </button>
                <div class="hidden md:flex items-center gap-4">
                    <h1 class="text-2xl font-bold">@yield('header_title')</h1>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 transition-colors hover:bg-gray-50 dark:hover:bg-white/10">
                    <i x-show="!darkMode" data-feather="moon" style="display: none;" class="w-5 h-5"></i>
                    <i x-show="darkMode" data-feather="sun" style="display: none;" class="w-5 h-5"></i>
                </button>
                <div class="w-10 h-10 bg-[#FF2D20] text-white rounded-full flex items-center justify-center font-bold">
                    {{ strtoupper(substr(Auth::user()->email, 0, 1)) }}
                </div>
            </div>
        </div>

        <div class="mb-8 md:hidden">
            <h1 class="text-2xl font-bold">@yield('header_title')</h1>
            <p class="text-gray-500 dark:text-[#94A3B8]">@yield('header_subtitle')</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-500 p-4 rounded-xl mb-8 border border-green-200 dark:border-green-500/20">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 p-4 rounded-xl mb-8 border border-red-200 dark:border-red-500/20">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <script>
        document.addEventListener('alpine:initialized', () => {
             feather.replace();
        });
        feather.replace();
    </script>
    @yield('scripts')
</body>
</html>
