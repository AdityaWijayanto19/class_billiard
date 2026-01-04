<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Class Billiard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js untuk interactive menu -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                                400: '#FFD700', // Yellow/Gold from mockup
                                500: '#E6C200',
                                600: '#B39700',
                            }
                        },
                        fontFamily: {
                            sans: ['Montserrat', 'sans-serif'],
                            rumonds: ['Rumonds', 'sans-serif'], // Fallback config
                        }
                    }
                }
            }
        </script>
        <style>
            * {
                /* Optimal font smoothing */
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            html {
                /* Smooth scroll behavior */
                scroll-behavior: smooth;
                scroll-padding-top: 96px;
            }

            body {
                /* iOS momentum scrolling */
                -webkit-overflow-scrolling: touch;
            }

            /* Prevent unnecessary repaints */
            body > * {
                /* Only enable will-change during scroll */
                contain: layout style;
            }

            /* Custom scrollbar styling */
            ::-webkit-scrollbar {
                width: 10px;
            }

            ::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.5);
            }

            ::-webkit-scrollbar-thumb {
                background: rgba(255, 215, 0, 0.6);
                border-radius: 5px;
                transition: background 0.3s ease;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 215, 0, 0.9);
            }

            /* Firefox scrollbar */
            * {
                scrollbar-color: rgba(255, 215, 0, 0.6) rgba(0, 0, 0, 0.5);
                scrollbar-width: thin;
            }

            /* Disable GPU acceleration yang terlalu aggressive */
            /* Lebih smooth dengan CPU rendering yang optimal */
            img, video {
                backface-visibility: visible;
            }
        </style>
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
        }

        /* Custom Clip Paths for Diagonals */
        .clip-diagonal-hero {
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }

        .navbar-bg-shape {
            background: linear-gradient(to right, #1a1a1a 0%, #1a1a1a 80%, transparent 100%);
            transform: skewX(-20deg);
            transform-origin: top left;
        }

        /* Marquee Animation */
        @keyframes marquee {
            0% {
                transform: translateX(-100%);
            }

            100% {
                transform: translateX(0%);
            }
        }

        .animate-marquee {
            animation: marquee 20s linear infinite;
        }
    </style>
</head>

<body class="bg-black text-white antialiased">
    <!-- Navbar Component -->
    @include('components.navbar')

    <!-- Hero Section Component -->
    @include('components.hero-section', ['hero' => $hero])

    <!-- About Section Component -->
    @include('components.about-section', ['about' => $about])

    <!-- Achievements Section Component -->
    @include('components.achievements-section', ['achievements' => $achievements])

    <!-- About Founder Section Component -->
    @include('components.founder-section', ['founder' => $founder])

    <!-- Events Section Component -->
    @include('components.events-section', ['events' => $events])

    <!-- Team Section Component -->
    @include('components.team-section', ['teamMembers' => $teamMembers])

    <!-- Running Text Strip -->
    <div class="bg-gold-400 overflow-hidden py-3 transform -skew-y-1 relative z-20">
        <div class="flex whitespace-nowrap">
            <div class="animate-marquee flex-shrink-0 flex items-center pr-8">
                <span class="text-black font-bold text-xl tracking-widest uppercase flex items-center gap-8">
                    PREMIUM TABLES <span class="text-2xl">•</span>
                    PRO COACHING <span class="text-2xl">•</span>
                    TOURNAMENTS <span class="text-2xl">•</span>
                    VIP LOUNGE <span class="text-2xl">•</span>
                    FINE DINING <span class="text-2xl">•</span>
                </span>
            </div>
            <div class="animate-marquee flex-shrink-0 flex items-center pr-8">
                <span class="text-black font-bold text-xl tracking-widest uppercase flex items-center gap-8">
                    PREMIUM TABLES <span class="text-2xl">•</span>
                    PRO COACHING <span class="text-2xl">•</span>
                    TOURNAMENTS <span class="text-2xl">•</span>
                    VIP LOUNGE <span class="text-2xl">•</span>
                    FINE DINING <span class="text-2xl">•</span>
                </span>
            </div>
            <div class="animate-marquee flex-shrink-0 flex items-center pr-8">
                <span class="text-black font-bold text-xl tracking-widest uppercase flex items-center gap-8">
                    PREMIUM TABLES <span class="text-2xl">•</span>
                    PRO COACHING <span class="text-2xl">•</span>
                    TOURNAMENTS <span class="text-2xl">•</span>
                    VIP LOUNGE <span class="text-2xl">•</span>
                    FINE DINING <span class="text-2xl">•</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Testimonials Section Component -->
    @include('components.testimonials-section', ['testimonials' => $testimonials])

    <!-- Footer Section Component -->
    @include('components.footer-section', ['footer' => $footer])

    <!-- AOS Animation Script -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: false,
            duration: 1000,
            easing: 'ease-out-cubic',
            offset: 50,
            delay: 0,
        });

        // Enhanced smooth scroll with momentum
        let scrollTop = 0;
        let targetScroll = 0;
        let isScrolling = false;

        // Smooth momentum scrolling
        document.addEventListener('wheel', (e) => {
            // Don't override native scroll, just enhance it
            isScrolling = true;
            setTimeout(() => {
                isScrolling = false;
            }, 100);
        }, { passive: true });

        // Optimize scroll performance dengan requestAnimationFrame
        let ticking = false;
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    // Lightweight scroll handling
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function () {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-black/90', 'backdrop-blur-md', 'shadow-lg');
                nav.classList.remove('h-24');
                nav.classList.add('h-20');
            } else {
                nav.classList.remove('bg-black/90', 'backdrop-blur-md', 'shadow-lg');
                nav.classList.remove('h-20');
                nav.classList.add('h-24');
            }
        });

        // Show error notification if exists in session
        @if(session('error'))
            Swal.fire({
                title: 'Akses Ditolak',
                text: '{{ session("error") }}',
                icon: 'error',
                background: document.documentElement.classList.contains('dark') ? '#0A0A0A' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                confirmButtonColor: '#fa9a08',
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'rounded-lg border border-white/5',
                    confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5'
                }
            });
        @endif
    </script>
</body>

</html>