<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $item['name'] }} • Class Billiard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
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
                            rumonds: ['Rumonds', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
        <style>
            @font-face {
                font-family: 'Rumonds';
                src: url('/fonts/rumonds.otf') format('opentype');
                font-weight: normal;
                font-style: normal;
            }
        </style>
    @endif
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            overflow-x: hidden;
            background-color: #050505;
        }

        .smooth-ease {
            transition-timing-function: cubic-bezier(.22, .61, .36, 1);
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>

<body class="text-white antialiased selection:bg-gold-400 selection:text-black">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 h-24 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-6 h-full flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="w-10 h-10 object-contain transition-transform group-hover:scale-110">
                <span
                    class="text-white font-bold tracking-[0.2em] text-sm hidden md:block group-hover:text-gold-400 transition-colors">CLASS
                    BILLIARD</span>
            </a>

            <a href="{{ url('/menu') }}"
                class="flex items-center gap-2 text-xs font-bold tracking-[0.2em] text-gray-400 hover:text-gold-400 transition-colors uppercase">
                <span class="hidden sm:inline">Back to Menu</span>
                <div
                    class="w-8 h-8 rounded-full border border-white/20 flex items-center justify-center group-hover:border-gold-400">
                    <i class="fa fa-times"></i>
                </div>
            </a>
        </div>
    </nav>

    <main class="min-h-screen flex items-center relative pt-20 pb-10">
        <!-- Background Elements -->
        <div
            class="absolute top-0 right-0 w-1/2 h-full bg-[#0a0a0a] -skew-x-12 translate-x-1/4 pointer-events-none z-0">
        </div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-gold-600/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-20 items-center">

                <!-- Left Content -->
                <div class="lg:col-span-5 space-y-8 order-2 lg:order-1" data-aos="fade-right">
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <span class="h-px w-8 bg-gold-400"></span>
                            <span
                                class="text-gold-400 text-xs font-bold tracking-[0.3em] uppercase">{{ $item['category'] }}</span>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-rumonds text-white leading-tight">
                            {{ $item['name'] }}
                        </h1>
                        <div class="text-2xl text-gray-400 font-light tracking-widest">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </div>
                    </div>

                    <p class="text-gray-400 leading-relaxed font-light text-lg border-l-2 border-white/10 pl-6">
                        {{ $item['description'] }}
                    </p>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="glass-panel p-4 rounded-xl text-center">
                            <i class="fa-solid fa-utensils text-gold-400 mb-2 text-sm"></i>
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Serving</div>
                            <div class="font-medium">{{ $item['servings'] }}</div>
                        </div>
                        <div class="glass-panel p-4 rounded-xl text-center">
                            <i class="fa-regular fa-clock text-gold-400 mb-2 text-sm"></i>
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Prep</div>
                            <div class="font-medium">{{ $item['prep_time'] }}</div>
                        </div>
                        @if($item['cook_time'] !== '0m')
                            <div class="glass-panel p-4 rounded-xl text-center">
                                <i class="fa-solid fa-fire-burner text-gold-400 mb-2 text-sm"></i>
                                <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Cook</div>
                                <div class="font-medium">{{ $item['cook_time'] }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Image Showcase -->
                <div class="lg:col-span-7 order-1 lg:order-2 relative" data-aos="fade-left">
                    <div class="relative w-full aspect-square max-w-[600px] mx-auto flex items-center justify-center">
                        <!-- Decorative Circle -->
                        <div
                            class="absolute inset-0 border border-white/5 rounded-full scale-90 animate-[spin_60s_linear_infinite]">
                        </div>
                        <div
                            class="absolute inset-0 border border-dashed border-white/10 rounded-full scale-75 animate-[spin_40s_linear_infinite_reverse]">
                        </div>

                        <!-- Main Image -->
                        <div
                            class="relative z-10 w-3/4 h-3/4 transition-transform duration-700 hover:scale-105 hover:rotate-2">
                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}"
                                class="w-full h-full object-contain drop-shadow-[0_20px_50px_rgba(0,0,0,0.5)]">
                        </div>

                        <!-- Badge -->
                        @if($item['label'])
                            <div
                                class="absolute top-10 right-10 z-20 bg-gold-400 text-black text-xs font-bold px-4 py-2 rounded-full uppercase tracking-widest shadow-lg animate-bounce">
                                {{ $item['label'] }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Bottom Navigation -->
            <div class="fixed bottom-0 left-0 w-full z-40 px-6 pb-6 pointer-events-none">
                <div class="container mx-auto flex justify-between items-end">

                    <!-- Prev Button -->
                    <a href="{{ url('/menu/' . $prevItem['slug']) }}"
                        class="pointer-events-auto group flex items-center gap-4 text-left hover:opacity-100 transition-opacity opacity-60">
                        <div
                            class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center group-hover:bg-gold-400 group-hover:border-gold-400 group-hover:text-black transition-all shrink-0">
                            <i class="fa fa-arrow-left"></i>
                        </div>
                        <div
                            class="w-12 h-12 rounded-lg overflow-hidden border border-white/10 hidden sm:block shrink-0">
                            <img src="{{ asset($prevItem['image']) }}" alt="{{ $prevItem['name'] }}"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="hidden md:block">
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Previous</div>
                            <div class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors">
                                {{ $prevItem['name'] }}
                            </div>
                        </div>
                    </a>

                    <!-- Next Button -->
                    <a href="{{ url('/menu/' . $nextItem['slug']) }}"
                        class="pointer-events-auto group flex items-center gap-4 text-right hover:opacity-100 transition-opacity opacity-60 flex-row-reverse">
                        <div
                            class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center group-hover:bg-gold-400 group-hover:border-gold-400 group-hover:text-black transition-all shrink-0">
                            <i class="fa fa-arrow-right"></i>
                        </div>
                        <div
                            class="w-12 h-12 rounded-lg overflow-hidden border border-white/10 hidden sm:block shrink-0">
                            <img src="{{ asset($nextItem['image']) }}" alt="{{ $nextItem['name'] }}"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="hidden md:block">
                            <div class="text-[10px] text-gray-500 uppercase tracking-widest mb-1">Next</div>
                            <div class="text-sm font-bold text-white group-hover:text-gold-400 transition-colors">
                                {{ $nextItem['name'] }}
                            </div>
                        </div>
                    </a>

                </div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 1000,
            easing: 'ease-out-cubic'
        });

        const nav = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                nav.classList.add('bg-black', 'shadow-lg', 'h-20', 'border-b', 'border-white/10');
                nav.classList.remove('h-24');
            } else {
                nav.classList.remove('bg-black', 'shadow-lg', 'h-20', 'border-b', 'border-white/10');
                nav.classList.add('h-24');
            }
        });
    </script>
</body>

</html>
