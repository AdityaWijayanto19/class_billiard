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

    <!-- NAVBAR -->
    <x-navbar />

    <div class="flex h-screen w-full overflow-hidden">

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto overflow-x-auto transition-all duration-300"
            style="width: calc(100% - 0px); padding-top: 6rem;" id="mainContent">
            <!-- Header -->
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-0 px-4 md:px-8 bg-bg-dark py-4"
                style="width: calc(100% - 0px); transition: width 0.3s ease;">
                <div class="min-w-0">
                  <h1 class="text-4xl md:text-5xl mb-2 text-white font-rumonds tracking-widest">Class Billiard Menu</h1>
                    <p class="text-gray-400 text-sm md:text-base tracking-[0.2em] uppercase font-light">
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
                <div class="flex gap-12 px-8 py-6">
                    <button
                        class="category-tab pb-2 font-semibold text-sm md:text-base tracking-widest whitespace-nowrap transition-all duration-300"
                        data-category="all"
                        style="color: #FFD700; border-bottom: 2px solid #FFD700;">All</button>
                    @foreach($categories as $category)
                        <button
                            class="category-tab pb-2 font-semibold text-sm md:text-base tracking-widest whitespace-nowrap transition-all duration-300"
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
            <div class="flex justify-between items-center mb-16 px-8 pt-8">
                <h2 class="text-xl font-semibold">Choose Dishes</h2>
            </div>

            <!-- Grid of Dishes (Fixed card size, responsive grid) -->
            <div class="px-8 pb-20">
                <div class="grid gap-x-6 gap-y-12" id="menuGrid" style="grid-template-columns: repeat(5, 1fr);">
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
                        <a href="{{ route('menu.detail', $menu->slug) }}" class="menu-card group relative bg-bg-sidebar rounded-xl pt-16 pb-4 px-4 text-center cursor-pointer transform-gpu transition-all duration-500 smooth-ease hover:translate-y-[-5px] hover:scale-105 hover:shadow-[0_35px_60px_rgba(0,0,0,0.45)] hover:border-2 no-underline text-white"
                            data-category="{{ $category->slug }}" data-name="{{ $menu->name }}"
                            data-price="{{ $menu->price }}"
                            data-image="{{ $menu->image_url }}"
                            data-label="{{ $menu->labels ?? '' }}"
                            style="border: 1px solid transparent; transition: all 500ms cubic-bezier(.22, .61, .36, 1); overflow: visible;"
                            onmouseenter="this.style.borderColor = '#FFD700';"
                            onmouseleave="this.style.borderColor = 'transparent';">
                            <div class="sheen rounded-xl"></div>
                            <img src="{{ $menu->image_url ?: 'https://via.placeholder.com/400' }}"
                                alt="{{ $menu->name }}"
                                class="w-48 h-48 rounded-full mx-auto -mt-36 object-cover group-hover:scale-110 transition-transform duration-500">
                            <h3 class="text-[15px] font-medium mb-2 px-4 -mt-6 leading-snug line-clamp-2">{{ $menu->name }}</h3>
                            @if($menu->short_description)
                                <p class="text-xs text-text-gray mb-2 px-2 line-clamp-2">{{ $menu->short_description }}</p>
                            @endif
                            <p class="text-sm mb-3 font-medium">Rp{{ number_format($menu->price, 0, ',', '.') }}</p>
                            <div class="flex gap-2 items-center justify-between">
                                <div class="flex gap-1 flex-wrap flex-1">
                                    @if($menu->labels)
                                        @php
                                            // Handle both JSON array and string formats
                                            $labelText = $menu->labels;
                                            if (str_starts_with($labelText, '[')) {
                                                $labelText = json_decode($labelText, true)[0] ?? $labelText;
                                            }
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
                                        <span class="text-xs px-2 py-0.5 rounded {{ $bgClass }} {{ $textClass }}">{{ $labelText }}</span>
                                    @endif
                                </div>
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