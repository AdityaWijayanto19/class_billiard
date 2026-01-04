<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Class Billiard</title>
    <!-- Google Font Montserrat (sesuai dengan home page) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Remixicon -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.min.css" rel="stylesheet">
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS 4.0 CDN -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.tailwind) {
                tailwind.config = {
                    theme: {
                        extend: {
                            colors: {
                                gold: {
                                    400: '#FFD700',
                                    500: '#E6C200',
                                    600: '#B39700',
                                }
                            },
                            fontFamily: {
                                sans: ['Montserrat', 'sans-serif'],
                            }
                        }
                    }
                }
            }
        });
    </script>
    <style type="text/tailwindcss">
        @theme {
            --color-bg-dark: #0a0a0a;
            --color-bg-sidebar: #1a1a1a;
            --color-primary: #FFD700;
            --color-text-gray: #abbbc2;
            --color-border-base: #393c49;
            --font-barlow: "Montserrat", sans-serif;
        }

        body {
            background-color: var(--color-bg-dark);
            color: white;
            font-family: var(--font-barlow);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #393c49; border-radius: 10px; }
        /* Smooth Ease Transition */
        .smooth-ease {
            transition-timing-function: cubic-bezier(.22, .61, .36, 1);
            will-change: transform, box-shadow, border-color;
        }

        /* Premium Subtle Glow Effect */
        .sheen {
            position: absolute;
            inset: 0;
            pointer-events: none;
            border-radius: inherit;
            opacity: 0;
            transition: opacity 400ms ease;
            box-shadow: inset 0 0 25px rgba(255, 255, 255, 0.04);
        }

        .group:hover .sheen {
            opacity: 1;
        }
    </style>
</head>

<body class="antialiased">


    <!-- NAVBAR ala home -->
    <nav class="fixed top-0 left-0 w-full z-50 h-24 transition-all duration-300" id="mainNavbar" x-data="{ mobileMenuOpen: false, isClosing: false }" @click.away="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);">
        <div class="container mx-auto px-6 h-full flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/logo.png" alt="Logo" class="w-12 h-12 object-contain drop-shadow-[0_0_5px_rgba(255,215,0,0.5)]">
                <span class="text-white font-bold tracking-[0.2em] text-sm hidden md:block">CLASS BILLIARD</span>
            </div>
            <button @click="mobileMenuOpen = !mobileMenuOpen; isClosing = false;" class="md:hidden flex flex-col gap-1.5 focus:outline-none p-2 z-50 relative">
                <span :class="['w-6 h-0.5 bg-gold-400 transition-all duration-300', mobileMenuOpen && 'rotate-45 translate-y-2']"></span>
                <span :class="['w-6 h-0.5 bg-gold-400 transition-all duration-300', mobileMenuOpen && 'opacity-0']"></span>
                <span :class="['w-6 h-0.5 bg-gold-400 transition-all duration-300', mobileMenuOpen && '-rotate-45 -translate-y-2']"></span>
            </button>
        </div>
        <!-- Luxury Mobile Menu -->
        <div x-show="mobileMenuOpen" :class="isClosing ? 'animate-smooth-bounce-out' : 'animate-smooth-bounce-in'"
            @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
            class="fixed top-24 right-0 w-4/5 md:hidden bg-gradient-to-b from-black/95 via-black/98 to-black/99 border-l-2 border-gold-400/40 backdrop-blur-2xl shadow-2xl shadow-gold-400/20 z-40"
            style="height: calc(100vh - 96px); overflow-y: auto; background: linear-gradient(135deg, rgba(0,0,0,0.98) 0%, rgba(215,170,30,0.05) 50%, rgba(0,0,0,0.99) 100%);">
            <div class="px-6 pt-4 pb-8 space-y-2" @click.stop>
                <a href="/" class="block px-6 py-4 font-bold tracking-[0.15em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg text-gold-400">HOME</a>
                <a href="/menu" class="block px-6 py-4 font-semibold tracking-[0.1em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg text-gold-400 bg-gradient-to-r from-gold-400/20 to-transparent">MENU</a>
                <a href="#reservation" class="block px-6 py-4 font-semibold tracking-[0.1em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg text-gray-200">RESERVATION</a>
                <div class="my-4 h-px bg-gradient-to-r from-transparent via-gold-400/50 to-transparent"></div>
                <a href="#contact" class="block w-full px-6 py-3 mt-4 border border-gold-400/60 text-gold-400 hover:bg-gold-400 hover:text-black text-sm font-bold tracking-[0.12em] transition duration-400 rounded-lg text-center hover:border-gold-400 hover:shadow-xl hover:shadow-gold-400/40">CONTACT US</a>
            </div>
        </div>
    </nav>
    <style>
        @keyframes smoothBounceInRight {
            0% { opacity: 0; transform: translateX(100%); }
            70% { opacity: 1; transform: translateX(0); }
            85% { transform: translateX(-8px); }
            100% { transform: translateX(0); }
        }
        @keyframes smoothBounceOutRight {
            0% { opacity: 1; transform: translateX(0); }
            15% { transform: translateX(-8px); }
            30% { transform: translateX(100%); }
            100% { opacity: 0; transform: translateX(100%); }
        }
        .animate-smooth-bounce-in { animation: smoothBounceInRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
        .animate-smooth-bounce-out { animation: smoothBounceOutRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    </style>

    <div class="flex h-screen w-full overflow-hidden">

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto overflow-x-auto transition-all duration-300"
            style="width: calc(100% - 0px); padding-top: 6rem;" id="mainContent">
            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-0 px-4 md:px-8 bg-bg-dark py-4"
                style="width: calc(100% - 0px); transition: width 0.3s ease;">
                <div class="min-w-0">
                  <h1 class="text-2xl md:text-5xl mb-2 text-white font-rumonds tracking-widest">Class Billiard Menu</h1>
                    <p class="text-gray-400 text-xs md:text-base tracking-[0.2em] uppercase font-light">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d M Y') }}
                    </p>
                </div>
                <div class="relative w-full md:w-72">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-gray">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input type="text" id="searchInput" placeholder="Search for food, menu, etc.."
                        class="w-full bg-bg-sidebar border border-border-base rounded-lg py-2.5 md:py-3 pl-10 pr-4 text-xs md:text-sm focus:outline-none focus:border-primary placeholder:text-text-gray">
                </div>
            </header>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-700 mb-0 overflow-x-auto no-scrollbar bg-bg-dark" style="width: calc(100% - 0px); transition: width 0.3s ease;">
                <div class="flex gap-4 md:gap-12 px-4 md:px-8 py-4 md:py-6">
                    <button
                        class="category-tab pb-2 font-semibold text-xs md:text-base tracking-widest whitespace-nowrap transition-all duration-300"
                        data-category="all"
                        style="color: #FFD700; border-bottom: 2px solid #FFD700;">All</button>
                    @foreach($categories as $category)
                        <button
                            class="category-tab pb-2 font-semibold text-xs md:text-base tracking-widest whitespace-nowrap transition-all duration-300"
                            data-category="{{ $category->slug }}"
                            style="color: #9ca3af; border-bottom: 2px solid transparent;"
                            onmouseenter="this.style.color = '#FFD700';"
                            onmouseleave="if(!this.classList.contains('active')) this.style.color = '#9ca3af';">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Section Title -->
            <div class="flex justify-between items-center mb-8 md:mb-16 px-4 md:px-8 pt-6 md:pt-8">
                <h2 class="text-base md:text-xl font-semibold">Choose Dishes</h2>
            </div>

            <!-- Grid of Dishes (Responsive grid) -->
            <div class="px-4 md:px-8 pb-20">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-x-3 gap-y-16 md:gap-x-6 md:gap-y-20" id="menuGrid">
                    @php
                        $allMenus = collect();
                        foreach ($categories as $category) {
                            foreach ($category->menus as $menu) {
                                $allMenus->push((object) [
                                    'menu' => $menu,
                                    'category' => $category
                                ]);
                            }
                        }
                        $urlParams = request()->query();
                        $hasBarcodeParams = isset($urlParams['table']) || isset($urlParams['room']) || isset($urlParams['order_id']);
                    @endphp

                    @forelse($allMenus as $item)
                        @php
                            $menu = $item->menu;
                            $category = $item->category;
                        @endphp
                        @php
                            $labelDisplay = '';
                            if($menu->labels && is_array($menu->labels) && count($menu->labels) > 0) {
                                $labelDisplay = $menu->labels[0];
                            }
                        @endphp
                        <a href="{{ route('menu.detail', $menu->slug) }}" class="menu-card group relative bg-bg-sidebar rounded-xl pt-10 md:pt-16 pb-3 md:pb-4 px-2 md:px-4 text-center cursor-pointer transform-gpu transition-all duration-500 smooth-ease hover:translate-y-[-5px] hover:scale-105 hover:shadow-[0_35px_60px_rgba(0,0,0,0.45)] hover:border-2 no-underline text-white"
                            data-category="{{ $category->slug }}" data-name="{{ $menu->name }}"
                            data-price="{{ $menu->price }}"
                            data-image="{{ $menu->image_url }}"
                            data-label="{{ $labelDisplay }}"
                            style="border: 1px solid transparent; transition: all 500ms cubic-bezier(.22, .61, .36, 1); overflow: visible;"
                            onmouseenter="this.style.borderColor = '#FFD700';"
                            onmouseleave="this.style.borderColor = 'transparent';">
                            <div class="sheen rounded-xl"></div>
                            <img src="{{ $menu->image_url ?: 'https://via.placeholder.com/400' }}"
                                alt="{{ $menu->name }}"
                                class="w-20 h-20 md:w-48 md:h-48 rounded-full mx-auto -mt-14 md:-mt-36 object-cover group-hover:scale-110 transition-transform duration-500 border-2 border-white/10">
                            <h3 class="text-[11px] md:text-[15px] font-medium mb-1 md:mb-2 px-1 md:px-4 mt-1 md:-mt-6 leading-snug line-clamp-2">{{ $menu->name }}</h3>
                            @if($menu->short_description)
                                <p class="text-[9px] md:text-xs text-text-gray mb-1 md:mb-2 px-1 md:px-2 line-clamp-2 hidden md:block">{{ $menu->short_description }}</p>
                            @endif
                            <p class="text-[10px] md:text-sm mb-1 md:mb-3 font-medium">Rp{{ number_format($menu->price, 0, ',', '.') }}</p>
                            <div class="flex gap-1 items-center justify-center flex-wrap">
                                    @if($menu->labels && is_array($menu->labels) && count($menu->labels) > 0)
                                        @php
                                            $labelText = $menu->labels[0];
                                            $label = strtolower($labelText);
                                            if(strpos($label, 'best seller') !== false || strpos($label, 'rekomendasi') !== false) {
                                                $bgClass = 'bg-primary/20';
                                                $textClass = 'text-primary';
                                            } elseif(strpos($label, 'new') !== false || strpos($label, 'baru') !== false) {
                                                $bgClass = 'bg-emerald-500/20';
                                                $textClass = 'text-emerald-500';
                                            } else {
                                                $bgClass = 'bg-red-500/20';
                                                $textClass = 'text-red-500';
                                            }
                                        @endphp
                                        <span class="text-[8px] md:text-xs px-1.5 md:px-2 py-0.5 rounded {{ $bgClass }} {{ $textClass }}">{{ $labelText }}</span>
                                    @endif
                            </div>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-12 text-text-gray">
                            <p>Belum ada menu yang tersedia.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>

    </div>

    <script>
        // Category filtering with better styling
        document.querySelectorAll('.category-tab').forEach(btn => {
            btn.addEventListener('click', function () {
                const category = this.getAttribute('data-category');

                // Update tab active state with inline styles
                document.querySelectorAll('.category-tab').forEach(b => {
                    b.style.color = '#9ca3af';
                    b.style.borderBottom = '2px solid transparent';
                    b.classList.remove('active');
                });
                this.style.color = '#FFD700';
                this.style.borderBottom = '2px solid #FFD700';
                this.classList.add('active');

                // Filter cards
                document.querySelectorAll('.menu-card').forEach(card => {
                    const cardCategory = card.getAttribute('data-category');
                    if (category === 'all' || cardCategory === category) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });

        // Search functionality
        document.getElementById('searchInput')?.addEventListener('keyup', function () {
            const search = this.value.toLowerCase();
            document.querySelectorAll('.menu-card').forEach(card => {
                const name = card.getAttribute('data-name').toLowerCase();
                card.style.display = name.includes(search) ? 'block' : 'none';
            });
        });
    </script>

    <!-- AOS Animation Library -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
    </script>

</body>

</html>