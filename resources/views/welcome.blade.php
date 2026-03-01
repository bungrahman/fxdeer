<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', mobileMenuOpen: false }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FXDeer - AI-Powered News Distribution</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}?v=1">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f4f4f5] dark:bg-[#0A0A0B] text-gray-900 dark:text-white font-['Outfit'] antialiased overflow-x-hidden transition-colors duration-300">
    <!-- Background Glow -->
    <div class="fixed w-[600px] h-[600px] rounded-full -z-10 blur-[80px]" style="background: radial-gradient(circle, rgba(255,45,32,0.08) 0%, transparent 70%); top: -10%; left: -10%;"></div>
    <div class="fixed w-[600px] h-[600px] rounded-full -z-10 blur-[80px]" style="background: radial-gradient(circle, rgba(255,45,32,0.08) 0%, transparent 70%); bottom: 10%; right: -10%;"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full px-6 py-4 flex justify-between items-center bg-white/80 dark:bg-[#0A0A0B]/80 backdrop-blur-md z-50 border-b border-gray-200 dark:border-white/10 transition-colors duration-300">
        <a href="#" class="flex items-center gap-2 font-bold text-xl text-gray-900 dark:text-white no-underline">
            <i data-feather="terminal" class="text-[#FF2D20]"></i>
            FXDeer
        </a>
        
        <!-- Desktop Nav -->
        <div class="hidden md:flex items-center gap-8">
            <a href="#features" class="text-gray-600 dark:text-[#94A3B8] hover:text-[#FF2D20] dark:hover:text-white transition-colors duration-300 no-underline font-medium">Features</a>
            <a href="#pricing" class="text-gray-600 dark:text-[#94A3B8] hover:text-[#FF2D20] dark:hover:text-white transition-colors duration-300 no-underline font-medium">Pricing</a>
            
            <button @click="darkMode = !darkMode" class="p-2 rounded-full border border-gray-200 dark:border-white/10 text-gray-600 dark:text-[#94A3B8] transition-colors hover:bg-gray-100 dark:hover:bg-white/10">
                <i x-show="!darkMode" data-feather="moon" style="display: none;" class="w-4 h-4"></i>
                <i x-show="darkMode" data-feather="sun" style="display: none;" class="w-4 h-4"></i>
            </button>
            
            @auth
            <a href="{{ route('admin.dashboard') }}" class="btn-primary py-2 px-6 rounded-xl hover:shadow-lg">Dashboard</a>
            @else
            <a href="{{ route('login') }}" class="btn-secondary py-2 px-6 rounded-xl">Login</a>
            <a href="{{ route('register') }}" class="btn-primary py-2 px-6 rounded-xl hover:shadow-lg">Get Started</a>
            @endauth
        </div>

        <!-- Mobile Menu Button -->
        <div class="md:hidden flex items-center gap-4">
            <button @click="darkMode = !darkMode" class="p-2 rounded-full border border-gray-200 dark:border-white/10 text-gray-600 dark:text-[#94A3B8]">
                <i x-show="!darkMode" data-feather="moon" style="display: none;" class="w-4 h-4"></i>
                <i x-show="darkMode" data-feather="sun" style="display: none;" class="w-4 h-4"></i>
            </button>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-600 dark:text-[#94A3B8] focus:outline-none">
                <i x-show="!mobileMenuOpen" data-feather="menu"></i>
                <i x-show="mobileMenuOpen" data-feather="x" style="display: none;"></i>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-white/95 dark:bg-[#0A0A0B]/95 backdrop-blur-md z-40 md:hidden flex flex-col items-center justify-center gap-8 pt-20">
        <a href="#features" @click="mobileMenuOpen = false" class="text-2xl font-bold text-gray-800 dark:text-white">Features</a>
        <a href="#pricing" @click="mobileMenuOpen = false" class="text-2xl font-bold text-gray-800 dark:text-white">Pricing</a>
        @auth
        <a href="{{ route('admin.dashboard') }}" class="btn-primary py-3 px-8 text-lg rounded-xl">Dashboard</a>
        @else
        <a href="{{ route('login') }}" class="btn-secondary py-3 px-8 text-lg rounded-xl">Login</a>
        <a href="{{ route('register') }}" class="btn-primary py-3 px-8 text-lg rounded-xl mt-4">Get Started</a>
        @endauth
    </div>

    <!-- Hero Section -->
    <section class="min-h-screen flex flex-col justify-center items-center text-center px-6 pt-24 pb-12">
        <h1 class="text-5xl md:text-[5rem] font-bold leading-tight md:leading-[1.1] mb-6 tracking-tight max-w-4xl">
            Elite <span class="text-[#FF2D20]">Financial Intelligence</span> for Modern Traders.
        </h1>
        <p class="text-lg md:text-xl text-gray-600 dark:text-[#94A3B8] max-w-2xl mb-10">
            Personalize your news alerts through customized Signal Bots and multi-pipeline automation. Powered by AI and precision execution.
        </p>
        <a href="#pricing" class="btn-primary py-4 px-10 text-lg rounded-2xl shadow-[0_20px_40px_-10px_rgba(255,45,32,0.4)] hover:shadow-[0_30px_60px_-12px_rgba(255,45,32,0.5)]">Start Your Elite Journey</a>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 px-6 md:px-[10%] bg-gray-50/50 dark:bg-white/5 border-y border-gray-200 dark:border-white/10">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Core Infrastructure</h2>
            <p class="text-lg text-gray-600 dark:text-[#94A3B8]">Advanced automation for institutional-grade financial news delivery.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-[#17171a]/70 p-8 rounded-3xl border border-gray-200 dark:border-white/10 hover:border-[#FF2D20] transition-colors duration-300 group shadow-sm dark:shadow-none">
                <div class="w-14 h-14 bg-red-50 dark:bg-red-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i data-feather="bot" class="text-[#FF2D20]"></i>
                </div>
                <h4 class="text-xl font-bold mb-4">Personal Signal Bot</h4>
                <p class="text-gray-600 dark:text-[#94A3B8] leading-relaxed">Configure your own Telegram Bot Token and Chat ID to receive exclusive signals on your private channels.</p>
            </div>

            <div class="bg-white dark:bg-[#17171a]/70 p-8 rounded-3xl border border-gray-200 dark:border-white/10 hover:border-[#FF2D20] transition-colors duration-300 group shadow-sm dark:shadow-none">
                <div class="w-14 h-14 bg-red-50 dark:bg-red-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i data-feather="layers" class="text-[#FF2D20]"></i>
                </div>
                <h4 class="text-xl font-bold mb-4">Multi-Pipeline System</h4>
                <p class="text-gray-600 dark:text-[#94A3B8] leading-relaxed">Separated flows for Daily Outlook, Event Alerts, and Post-Event Reactions powered by AI processing.</p>
            </div>

            <div class="bg-white dark:bg-[#17171a]/70 p-8 rounded-3xl border border-gray-200 dark:border-white/10 hover:border-[#FF2D20] transition-colors duration-300 group shadow-sm dark:shadow-none">
                <div class="w-14 h-14 bg-red-50 dark:bg-red-500/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i data-feather="shield-off" class="text-[#FF2D20]"></i>
                </div>
                <h4 class="text-xl font-bold mb-4">Global Kill-Switches</h4>
                <p class="text-gray-600 dark:text-[#94A3B8] leading-relaxed">Institutional control with emergency pause for all news distribution during high market volatility.</p>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 px-6 md:px-[10%]">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Ready to Choose Your Plan?</h2>
            <p class="text-lg text-gray-600 dark:text-[#94A3B8]">Simple, transparent pricing for every trading style.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($plans as $plan)
                <div class="bg-white dark:bg-[#17171a]/70 border border-gray-200 dark:border-white/10 hover:border-[#FF2D20] transition-all duration-300 rounded-[32px] p-8 md:p-12 text-center relative overflow-hidden group hover:scale-[1.02] shadow-sm dark:shadow-none {{ $plan->name == 'PRO' ? 'border-[#FF2D20] bg-red-50/50 dark:bg-red-500/5' : '' }}">
                    @if($plan->name == 'PRO')
                        <div class="absolute top-5 -right-[35px] bg-[#FF2D20] text-white py-1 px-12 rotate-45 text-xs font-bold tracking-wider">MOST POPULAR</div>
                    @endif
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                    <div class="text-5xl font-bold my-6 text-gray-900 dark:text-white">
                        ${{ number_format($plan->price, 0) }}<span class="text-lg text-gray-500 dark:text-[#94A3B8] font-normal">/mo</span>
                    </div>
                    
                    <ul class="text-left mb-8 space-y-4">
                        <li class="flex items-start gap-3 text-gray-600 dark:text-[#94A3B8]">
                            <i data-feather="check" class="text-[#FF2D20] w-5 h-5 shrink-0 mt-0.5"></i> 
                            <span>{{ $plan->max_alerts_per_day }} Alerts Per Day</span>
                        </li>
                        @if($plan->daily_outlook) 
                        <li class="flex items-start gap-3 text-gray-600 dark:text-[#94A3B8]">
                            <i data-feather="check" class="text-[#FF2D20] w-5 h-5 shrink-0 mt-0.5"></i> 
                            <span>Daily Market Outlook</span>
                        </li> 
                        @endif
                        @if($plan->upcoming_event_alerts) 
                        <li class="flex items-start gap-3 text-gray-600 dark:text-[#94A3B8]">
                            <i data-feather="check" class="text-[#FF2D20] w-5 h-5 shrink-0 mt-0.5"></i> 
                            <span>Upcoming Event Alerts</span>
                        </li> 
                        @endif
                        @if($plan->post_event_reaction) 
                        <li class="flex items-start gap-3 text-gray-600 dark:text-[#94A3B8]">
                            <i data-feather="check" class="text-[#FF2D20] w-5 h-5 shrink-0 mt-0.5"></i> 
                            <span>Post-Event Reactions</span>
                        </li> 
                        @endif
                        @if($plan->enable_telegram) 
                        <li class="flex items-start gap-3 text-gray-600 dark:text-[#94A3B8]">
                            <i data-feather="check" class="text-[#FF2D20] w-5 h-5 shrink-0 mt-0.5"></i> 
                            <span>Telegram Delivery</span>
                        </li> 
                        @endif
                        @if($plan->enable_blotato) 
                        <li class="flex items-start gap-3 text-gray-600 dark:text-[#94A3B8]">
                            <i data-feather="check" class="text-[#FF2D20] w-5 h-5 shrink-0 mt-0.5"></i> 
                            <span>Blotato Integration</span>
                        </li> 
                        @endif
                    </ul>

                    <a href="{{ route('register', ['plan' => $plan->id]) }}" class="btn-primary w-full block rounded-xl mt-auto">Select Plan</a>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200 dark:border-white/10 px-6 md:px-[10%] py-12 flex flex-col md:flex-row justify-between items-center gap-6 text-sm text-gray-500 dark:text-[#94A3B8]">
        <div class="flex items-center gap-2 font-bold text-lg text-gray-900 dark:text-white">
            <i data-feather="terminal" class="w-5 h-5 text-[#FF2D20]"></i>
            FXDeer
        </div>
        <p>&copy; {{ date('Y') }} FXDeer Intelligence. All rights reserved.</p>
        <div class="flex gap-6">
            <a href="#" class="hover:text-[#FF2D20] transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-[#FF2D20] transition-colors">Terms of Service</a>
        </div>
    </footer>

    <script>
        document.addEventListener('alpine:initialized', () => {
             feather.replace();
        });
        feather.replace();
    </script>
</body>
</html>
