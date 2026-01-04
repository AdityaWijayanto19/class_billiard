<!DOCTYPE html>
<html lang="id" class="@if(request()->routeIs('dapur') || request()->routeIs('reports') || request()->routeIs('pengaturan-audio') || request()->is('admin*')){{ request()->cookie('theme') === 'dark' ? 'dark' : '' }}@endif">
    <head>
        {{-- Initialize theme immediately before any content loads - HANYA untuk Admin & Dapur --}}
        @if(request()->routeIs('dapur') || request()->routeIs('reports') || request()->routeIs('pengaturan-audio') || request()->is('admin*'))
        <script>
            (function() {
                try {
                    const savedTheme = localStorage.getItem('theme');
                    const cookieTheme = document.cookie.split('; ').find(row => row.startsWith('theme='))?.split('=')[1];
                    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    
                    // Prioritize cookie, then localStorage, then system preference
                    const theme = cookieTheme || savedTheme || (prefersDark ? 'dark' : 'light');
                    
                    if (theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                    
                    // Sync localStorage with cookie if cookie exists
                    if (cookieTheme && cookieTheme !== savedTheme) {
                        localStorage.setItem('theme', cookieTheme);
                    }
                } catch(e) {
                    console.error('Theme initialization error:', e);
                }
            })();
        </script>
        @endif
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Billiard Class')</title>

        {{-- Typography: Plus Jakarta Sans (selaras dengan admin dashboard) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        {{-- Hina Mincho untuk subtitle --}}
        <link href="https://fonts.googleapis.com/css2?family=Hina+Mincho&display=swap" rel="stylesheet">

        {{-- Remix Icon untuk icon menu --}}
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/remixicon@4.0.0/fonts/remixicon.css"
        />

        {{-- SweetAlert2 untuk popup --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- Alpine.js --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('head')
        @stack('styles')
    </head>
    <body class="font-['Plus_Jakarta_Sans',system-ui,-apple-system,BlinkMacSystemFont,'Segoe_UI',sans-serif] antialiased bg-black text-white">
        {{-- Hidden Logout Form (must be at start of body for early availability) - HANYA untuk Admin & Dapur --}}
        @if(request()->routeIs('dapur') || request()->routeIs('reports') || request()->routeIs('pengaturan-audio') || request()->is('admin*'))
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
        @endif
        
        @if(!request()->routeIs('dapur') && !request()->routeIs('reports') && !request()->routeIs('pengaturan-audio'))
        <header class="fixed top-0 left-0 right-0 w-full z-50 bg-[#1A1A1A]/95 backdrop-blur-md border-b border-white/10 shadow-lg">
            <div class="max-w-[1400px] mx-auto px-4 md:px-6">
                <div class="flex items-center justify-between py-4 md:py-5">
                    <div class="logo">
                        <a href="{{ route('home') }}" class="flex items-center gap-2">
                            <h1 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-white to-[#fa9a08] bg-clip-text text-transparent">Class Billiard</h1>
                        </a>
                    </div>

                    <nav class="hidden md:flex items-center gap-8">
                        <a href="{{ route('home') }}" class="text-sm font-semibold transition-colors duration-200 hover:text-[#fa9a08] {{ request()->routeIs('home') ? 'text-[#fa9a08]' : 'text-gray-300' }}">Beranda</a>
                        <a href="#tentang-kami" class="text-sm font-semibold transition-colors duration-200 hover:text-[#fa9a08] text-gray-300">Tentang Kami</a>
                        <a href="#fasilitas" class="text-sm font-semibold transition-colors duration-200 hover:text-[#fa9a08] text-gray-300">Fasilitas</a>
                        <a href="#portfolio" class="text-sm font-semibold transition-colors duration-200 hover:text-[#fa9a08] text-gray-300">Portfolio</a>
                        <a href="#event" class="text-sm font-semibold transition-colors duration-200 hover:text-[#fa9a08] text-gray-300">Event</a>
                        <a href="{{ route('orders.create') }}" class="bg-gradient-to-r from-[#fa9a08] to-[#e19e2b] text-black px-6 py-2.5 rounded-xl font-bold text-sm transition-all hover:shadow-lg hover:shadow-[#fa9a08]/30 hover:scale-105">
                            <i class="ri-shopping-cart-line mr-1"></i> Pesan
                        </a>
                    </nav>

                    <div class="md:hidden cursor-pointer" id="navbar-toggle">
                        <i class="ri-menu-3-line text-2xl text-white"></i>
                    </div>
                </div>
            </div>
            
            {{-- Mobile Menu --}}
            <div class="md:hidden fixed top-[73px] left-0 right-0 bg-[#1A1A1A]/98 backdrop-blur-md border-b border-white/10 shadow-xl opacity-0 invisible translate-y-[-20px] transition-all duration-300" id="navbar-menu">
                <nav class="px-4 py-6 space-y-4">
                    <a href="{{ route('home') }}" class="block py-3 px-4 rounded-lg transition-colors duration-200 {{ request()->routeIs('home') ? 'bg-[#fa9a08]/20 text-[#fa9a08]' : 'text-gray-300 hover:bg-white/5' }} font-semibold">
                        <i class="ri-home-line mr-2"></i> Beranda
                    </a>
                    <a href="#tentang-kami" class="block py-3 px-4 rounded-lg transition-colors duration-200 text-gray-300 hover:bg-white/5 font-semibold">
                        <i class="ri-information-line mr-2"></i> Tentang Kami
                    </a>
                    <a href="#fasilitas" class="block py-3 px-4 rounded-lg transition-colors duration-200 text-gray-300 hover:bg-white/5 font-semibold">
                        <i class="ri-star-line mr-2"></i> Fasilitas
                    </a>
                    <a href="#portfolio" class="block py-3 px-4 rounded-lg transition-colors duration-200 text-gray-300 hover:bg-white/5 font-semibold">
                        <i class="ri-trophy-line mr-2"></i> Portfolio
                    </a>
                    <a href="#event" class="block py-3 px-4 rounded-lg transition-colors duration-200 text-gray-300 hover:bg-white/5 font-semibold">
                        <i class="ri-calendar-event-line mr-2"></i> Event
                    </a>
                    <a href="{{ route('orders.create') }}" class="block py-3 px-4 rounded-lg bg-gradient-to-r from-[#fa9a08] to-[#e19e2b] text-black font-bold text-center mt-4">
                        <i class="ri-shopping-cart-line mr-2"></i> Pesan Sekarang
                    </a>
                </nav>
            </div>
        </header>
        @endif

        <main class="@if(!request()->routeIs('dapur') && !request()->routeIs('reports') && !request()->routeIs('pengaturan-audio'))pt-[73px] @endif bg-black">
            <!-- ALERTS -->
            @if(session('error') && (request()->routeIs('dapur') || request()->routeIs('reports') || request()->routeIs('pengaturan-audio') || request()->is('admin*')))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                    class="relative z-50 mx-4 mt-4 p-4 bg-red-500/10 border border-red-500/30 rounded-lg flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                    <i class="ri-alert-fill text-red-500 text-xl shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-red-400">{{ session('error') }}</p>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700 shrink-0">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if(session('warning') && (request()->routeIs('dapur') || request()->routeIs('reports') || request()->routeIs('pengaturan-audio') || request()->is('admin*')))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                    class="relative z-50 mx-4 mt-4 p-4 bg-amber-500/10 border border-amber-500/30 rounded-lg flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                    <i class="ri-alert-line text-amber-500 text-xl shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-amber-400">{{ session('warning') }}</p>
                    </div>
                    <button @click="show = false" class="text-amber-500 hover:text-amber-700 shrink-0">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    class="relative z-50 mx-4 mt-4 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-lg flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                    <i class="ri-checkbox-circle-fill text-emerald-500 text-xl shrink-0 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-emerald-400">{{ session('success') }}</p>
                    </div>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 shrink-0">
                        <i class="ri-close-line"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <script>
            (function () {
                const toggle = document.getElementById('navbar-toggle');
                const menu = document.getElementById('navbar-menu');

                if (toggle && menu) {
                    toggle.addEventListener('click', () => {
                        const isVisible = menu.classList.contains('opacity-0');
                        if (isVisible) {
                            menu.classList.remove('opacity-0', 'invisible', 'translate-y-[-20px]');
                            menu.classList.add('opacity-100', 'visible', 'translate-y-0');
                        } else {
                            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                            menu.classList.add('opacity-0', 'invisible', 'translate-y-[-20px]');
                        }
                    });
                    
                    // Close menu when clicking outside
                    document.addEventListener('click', (e) => {
                        if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                            menu.classList.add('opacity-0', 'invisible', 'translate-y-[-20px]');
                        }
                    });
                    
                    // Close menu when clicking on a link
                    menu.querySelectorAll('a').forEach(link => {
                        link.addEventListener('click', () => {
                            menu.classList.remove('opacity-100', 'visible', 'translate-y-0');
                            menu.classList.add('opacity-0', 'invisible', 'translate-y-[-20px]');
                        });
                    });
                }
            })();
            
            // Smooth scroll untuk anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        const headerOffset = 100;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Handle Flash Alert Messages
            const alertType = "{{ session('alert_type') }}";
            const alertTitle = "{{ session('alert_title') }}";
            const alertMessage = "{{ session('alert_message') }}";
            const alertIcon = "{{ session('alert_icon', 'info') }}";

            if (alertType && alertTitle && alertMessage) {
                Swal.fire({
                    title: alertTitle,
                    text: alertMessage,
                    icon: alertIcon,
                    background: '#1A1A1A',
                    color: '#fff',
                    confirmButtonColor: '#fa9a08',
                    confirmButtonText: 'Mengerti',
                    customClass: {
                        popup: 'rounded-lg border border-white/10',
                        confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5'
                    }
                });
            }
        </script>

        @stack('scripts')
    </body>
</html>
