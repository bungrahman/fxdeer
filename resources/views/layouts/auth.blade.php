<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - FXDeer</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .auth-bg {
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(255, 45, 32, 0.05) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 45, 32, 0.05) 0%, transparent 40%);
        }
    </style>
</head>
<body class="auth-bg flex flex-col items-center justify-center min-h-screen p-4">
    <!-- Dark Mode Toggle floating top right -->
    <div class="absolute top-4 right-4">
        <button @click="darkMode = !darkMode" class="p-2 rounded-full bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 transition-colors shadow-sm hover:bg-gray-50 dark:hover:bg-white/10">
            <i x-show="!darkMode" data-feather="moon" style="display: none;"></i>
            <i x-show="darkMode" data-feather="sun" style="display: none;"></i>
        </button>
    </div>

    <!-- Auth Card -->
    <div class="w-full max-w-[420px] bg-white dark:bg-[#17171A]/70 backdrop-blur-[20px] border border-gray-200 dark:border-white/10 rounded-[24px] p-8 md:p-12 shadow-[0_25px_50px_-12px_rgba(0,0,0,0.1)] dark:shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)]">
        <div class="flex items-center justify-center gap-[10px] font-bold text-2xl text-gray-900 dark:text-white mb-10">
            <i data-feather="terminal" class="text-[#FF2D20] w-8 h-8"></i>
            FXDeer
        </div>
        
        <div class="text-center mb-10">
            <h1 class="text-[1.75rem] font-bold mb-2">@yield('title')</h1>
            <p class="text-gray-500 dark:text-[#94A3B8] text-[0.95rem]">@yield('subtitle', 'Welcome back to FXDeer')</p>
        </div>

        @yield('content')
    </div>

    <script>
        document.addEventListener('alpine:initialized', () => {
             feather.replace();
        });
        feather.replace();
    </script>
    @stack('scripts')
</body>
</html>
