{{-- Navbar Component --}}
<style>
    @keyframes smoothBounceInRight {
        0% {
            opacity: 0;
            transform: translateX(100%);
        }
        70% {
            opacity: 1;
            transform: translateX(0);
        }
        85% {
            transform: translateX(-8px);
        }
        100% {
            transform: translateX(0);
        }
    }

    @keyframes smoothBounceOutRight {
        0% {
            opacity: 1;
            transform: translateX(0);
        }
        15% {
            transform: translateX(-8px);
        }
        30% {
            transform: translateX(100%);
        }
        100% {
            opacity: 0;
            transform: translateX(100%);
        }
    }

    .animate-smooth-bounce-in {
        animation: smoothBounceInRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    .animate-smooth-bounce-out {
        animation: smoothBounceOutRight 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
</style>

<nav class="fixed top-0 left-0 w-full z-50 h-24 transition-all duration-300" data-aos="fade-down"
    data-aos-duration="1000" id="mainNavbar" x-data="{ mobileMenuOpen: false, isClosing: false }" @click.away="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);">
    @php
        // Use admin-managed Contact for navbar. The navbar contact is always active
        // (user requested always-active behavior). We prefer an explicit navbar_link
        // and navbar_label if set; otherwise fall back to whatsapp.
        // Optimized: Use cached data with select specific columns
        $contact = cache()->remember('navbar_contact', 3600, function () {
            return \App\Models\Contact::select('id', 'navbar_link', 'whatsapp', 'navbar_label', 'title')
                ->where('is_active', true)
                ->first();
        });
        $navbarLink = null;
        $navbarLabel = 'CONTACT US';
        if ($contact) {
            if ($contact->navbar_link && trim($contact->navbar_link) !== '') {
                $navbarLink = trim($contact->navbar_link);
            } elseif ($contact->whatsapp && trim($contact->whatsapp) !== '') {
                $navbarLink = trim($contact->whatsapp);
            }
            if ($contact->navbar_label && trim($contact->navbar_label) !== '') {
                $navbarLabel = trim($contact->navbar_label);
            } elseif ($contact->title && trim($contact->title) !== '') {
                $navbarLabel = trim($contact->title);
            }
        }

        // Hero CTA1 link (if admin configured a booking/group link) - optimized
        $hero = cache()->remember('navbar_hero', 3600, function () {
            return \App\Models\HeroSection::select('id', 'cta_link_1')
                ->where('is_active', true)
                ->first();
        });
        $ctaLink1 = $hero && isset($hero->cta_link_1) && $hero->cta_link_1 && trim($hero->cta_link_1) !== '' ? trim($hero->cta_link_1) : null;
        $ctaLink1IsExternal = $ctaLink1 && preg_match('#^https?://#i', $ctaLink1);
    @endphp
    <div class="container mx-auto px-6 h-full flex items-center justify-between">
        <!-- Left: Brand/Logo (Small) -->
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo"
                class="w-12 h-12 object-contain drop-shadow-[0_0_5px_rgba(255,215,0,0.5)]">
            <span class="text-white font-bold tracking-[0.2em] text-sm hidden md:block">CLASS BILLIARD</span>
        </div>

        <!-- Right: Menu Links (Desktop) -->
        <div class="hidden md:flex space-x-12 items-center">
            <a href="{{ route('home') }}" style="{{ Route::currentRouteName() === 'home' ? 'color: #FFD700' : '' }}" class="text-sm font-bold tracking-widest relative group {{ Route::currentRouteName() === 'home' ? '' : 'text-gray-400 hover:text-white' }} transition duration-300">
                HOME
                @if(Route::currentRouteName() === 'home')
                    <span class="absolute -bottom-2 left-1/2 w-1 h-1 rounded-full transform -translate-x-1/2" style="background-color: #FFD700;"></span>
                @endif
            </a>
            <a href="{{ route('menu.index') }}" style="{{ Route::currentRouteName() === 'menu.index' ? 'color: #FFD700' : '' }}" class="text-sm font-bold tracking-widest relative group {{ Route::currentRouteName() === 'menu.index' ? '' : 'text-gray-400 hover:text-white' }} transition duration-300">
                MENU
                @if(Route::currentRouteName() === 'menu.index')
                    <span class="absolute -bottom-2 left-1/2 w-1 h-1 rounded-full transform -translate-x-1/2" style="background-color: #FFD700;"></span>
                @endif
            </a>
            @if($ctaLink1)
                <a href="{{ $ctaLink1 }}" @if($ctaLink1IsExternal) target="_blank" rel="noopener noreferrer" @endif
                    class="text-gray-400 hover:text-white text-sm font-semibold tracking-widest transition duration-300">RESERVATION</a>
            @else
                <a href="#reservation"
                    class="text-gray-400 hover:text-white text-sm font-semibold tracking-widest transition duration-300">RESERVATION</a>
            @endif

            @unless(request()->is('admin*') || request()->is('dapur*'))
            <a href="{{ $navbarLink ? $navbarLink : (Route::currentRouteName() === 'home' ? '#contact' : route('home') . '#contact') }}"
                @if($navbarLink) target="_blank" rel="noopener noreferrer" @endif
                class="px-6 py-2 text-sm font-bold tracking-widest transition duration-300 rounded-sm hover:text-black"
                style="border: 1px solid rgba(255, 215, 0, 0.3); color: #FFD700;"
                onmouseenter="this.style.backgroundColor='#FFD700'; this.style.color='black';"
                onmouseleave="this.style.backgroundColor='transparent'; this.style.color='#FFD700';">
                {{ $navbarLabel }}
            </a>
            @endunless

            @auth
                @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'kitchen']))
                    @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        <a href="{{ route('admin.dashboard') }}"
                            class="px-6 py-2 text-sm font-bold tracking-widest transition duration-300 rounded-sm hover:text-black"
                            style="border: 1px solid rgba(255, 215, 0, 0.3); color: #FFD700;"
                            onmouseenter="this.style.backgroundColor='#FFD700'; this.style.color='black';"
                            onmouseleave="this.style.backgroundColor='transparent'; this.style.color='#FFD700';">
                            DASHBOARD
                        </a>
                    @elseif(auth()->user()->hasRole('kitchen'))
                        <a href="{{ route('dapur') }}"
                            class="px-6 py-2 text-sm font-bold tracking-widest transition duration-300 rounded-sm hover:text-black"
                            style="border: 1px solid rgba(255, 215, 0, 0.3); color: #FFD700;"
                            onmouseenter="this.style.backgroundColor='#FFD700'; this.style.color='black';"
                            onmouseleave="this.style.backgroundColor='transparent'; this.style.color='#FFD700';">
                            DAPUR
                        </a>
                    @endif
                @endif
            @endauth

        </div>

        <!-- Mobile Hamburger Button -->
        <button @click="mobileMenuOpen = !mobileMenuOpen; isClosing = false;" class="md:hidden flex flex-col gap-1.5 focus:outline-none p-2 z-50 relative">
            <span :class="['w-6 h-0.5 bg-gold-400 transition-all duration-300', mobileMenuOpen && 'rotate-45 translate-y-2']"></span>
            <span :class="['w-6 h-0.5 bg-gold-400 transition-all duration-300', mobileMenuOpen && 'opacity-0']"></span>
            <span :class="['w-6 h-0.5 bg-gold-400 transition-all duration-300', mobileMenuOpen && '-rotate-45 -translate-y-2']"></span>
        </button>
    </div>

    <!-- Luxury Mobile Menu -->
    <div x-show="mobileMenuOpen" 
        :class="isClosing ? 'animate-smooth-bounce-out' : 'animate-smooth-bounce-in'"
        @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
        class="fixed top-24 right-0 w-4/5 md:hidden bg-gradient-to-b from-black/95 via-black/98 to-black/99 border-l-2 border-gold-400/40 backdrop-blur-2xl shadow-2xl shadow-gold-400/20 z-40"
        style="height: calc(100vh - 96px); overflow-y: auto; background: linear-gradient(135deg, rgba(0,0,0,0.98) 0%, rgba(215,170,30,0.05) 50%, rgba(0,0,0,0.99) 100%);">
        
        <!-- Menu Container with luxury styling -->
        <div class="px-6 pt-4 pb-8 space-y-2" @click.stop>
            <!-- Home Link -->
            <a href="{{ route('home') }}" @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                class="block px-6 py-4 font-bold tracking-[0.15em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg"
                style="{{ Route::currentRouteName() === 'home' ? 'color: #FFD700; background: linear-gradient(to right, rgba(255,215,0,0.2) 0%, transparent 100%);' : 'color: #d1d5db;' }}"
                onmouseenter="if(this.style.color !== '#FFD700') { this.style.color = '#FFD700'; this.style.background = 'linear-gradient(to right, rgba(255,215,0,0.15) 0%, transparent 100%)'; }"
                onmouseleave="if('{{ Route::currentRouteName() }}' !== 'home') { this.style.color = '#d1d5db'; this.style.background = 'transparent'; }">
                HOME
            </a>

            <!-- Menu Link -->
            <a href="{{ route('menu.index') }}" @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                class="block px-6 py-4 font-semibold tracking-[0.1em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg"
                style="{{ Route::currentRouteName() === 'menu.index' ? 'color: #FFD700; background: linear-gradient(to right, rgba(255,215,0,0.2) 0%, transparent 100%);' : 'color: #d1d5db;' }}"
                onmouseenter="if(this.style.color !== '#FFD700') { this.style.color = '#FFD700'; this.style.background = 'linear-gradient(to right, rgba(255,215,0,0.15) 0%, transparent 100%)'; }"
                onmouseleave="if('{{ Route::currentRouteName() }}' !== 'menu.index') { this.style.color = '#d1d5db'; this.style.background = 'transparent'; }">
                MENU
            </a>

            <!-- Reservation Link -->
            @if($ctaLink1)
                <a href="{{ $ctaLink1 }}" @if($ctaLink1IsExternal) target="_blank" rel="noopener noreferrer" @endif @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                    class="block px-6 py-4 font-semibold tracking-[0.1em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg text-gray-200"
                    onmouseenter="this.style.color = '#FFD700'; this.style.background = 'linear-gradient(to right, rgba(255,215,0,0.15) 0%, transparent 100%)';"
                    onmouseleave="this.style.color = '#d1d5db'; this.style.background = 'transparent';">
                    RESERVATION
                </a>
            @else
                <a href="#reservation" @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                    class="block px-6 py-4 font-semibold tracking-[0.1em] text-base rounded-lg transition duration-400 hover:pl-8 hover:shadow-lg text-gray-200"
                    onmouseenter="this.style.color = '#FFD700'; this.style.background = 'linear-gradient(to right, rgba(255,215,0,0.15) 0%, transparent 100%)';"
                    onmouseleave="this.style.color = '#d1d5db'; this.style.background = 'transparent';">
                    RESERVATION
                </a>
            @endif

            <!-- Divider -->
            <div class="my-4 h-px bg-gradient-to-r from-transparent via-gold-400/50 to-transparent"></div>

            <!-- Contact Us Button -->
            @unless(request()->is('admin*') || request()->is('dapur*'))
            <a href="{{ $navbarLink ? $navbarLink : (Route::currentRouteName() === 'home' ? '#contact' : route('home') . '#contact') }}" @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                @if($navbarLink) target="_blank" rel="noopener noreferrer" @endif
                class="block w-full px-6 py-3 mt-4 border border-gold-400/60 text-gold-400 hover:bg-gold-400 hover:text-black text-sm font-bold tracking-[0.12em] transition duration-400 rounded-lg text-center hover:border-gold-400 hover:shadow-xl hover:shadow-gold-400/40">
                {{ $navbarLabel }}
            </a>
            @endunless

            <!-- Dashboard Button (if authenticated) -->
            @auth
                @if(auth()->user()->hasAnyRole(['super_admin', 'admin', 'kitchen']))
                    @if(auth()->user()->hasAnyRole(['super_admin', 'admin']))
                        <a href="{{ route('admin.dashboard') }}" @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                            class="block w-full px-6 py-3 mt-3 bg-gradient-to-r from-gold-400/30 to-gold-400/10 border border-gold-400/60 text-gold-400 hover:from-gold-400/40 hover:to-gold-400/20 text-sm font-bold tracking-[0.12em] transition duration-400 rounded-lg text-center hover:shadow-xl hover:shadow-gold-400/40">
                            DASHBOARD
                        </a>
                    @elseif(auth()->user()->hasRole('kitchen'))
                        <a href="{{ route('dapur') }}" @click="isClosing = true; setTimeout(() => { mobileMenuOpen = false; isClosing = false; }, 600);"
                            class="block w-full px-6 py-3 mt-3 bg-gradient-to-r from-gold-400/30 to-gold-400/10 border border-gold-400/60 text-gold-400 hover:from-gold-400/40 hover:to-gold-400/20 text-sm font-bold tracking-[0.12em] transition duration-400 rounded-lg text-center hover:shadow-xl hover:shadow-gold-400/40">
                            DAPUR
                        </a>
                    @endif
                @endif
            @endauth

            <!-- Extra spacing at bottom -->
            <div class="h-6"></div>
        </div>
    </div>
</nav>


<!-- Navbar Scroll Effects Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const navbar = document.getElementById('mainNavbar');
        if (!navbar) return;
        
        let lastScrollY = 0;
        let ticking = false;
        let isHidden = false;
        
        const updateNavbar = () => {
            const scrollY = window.scrollY;
            const scrollDirection = scrollY > lastScrollY ? 'down' : 'up';
            
            if (scrollY > 100) {
                // Apply sticky styling
                navbar.classList.add(
                    'bg-black/80',
                    'backdrop-blur-md',
                    'shadow-2xl',
                    'shadow-black/50'
                );
                navbar.classList.remove('absolute');
                
                // Hide on scroll down
                if (scrollDirection === 'down' && !isHidden) {
                    navbar.style.transform = 'translateY(-100%)';
                    isHidden = true;
                }
                // Show on scroll up
                else if (scrollDirection === 'up' && isHidden) {
                    navbar.style.transform = 'translateY(0)';
                    isHidden = false;
                }
            } else {
                // At top - remove sticky styling and show navbar
                navbar.classList.remove(
                    'bg-black/80',
                    'backdrop-blur-md',
                    'shadow-2xl',
                    'shadow-black/50'
                );
                navbar.classList.add('absolute');
                navbar.style.transform = 'translateY(0)';
                isHidden = false;
            }
            
            lastScrollY = scrollY;
            ticking = false;
        };
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        }, { passive: true });
        
        // Initialize
        updateNavbar();
    });
</script>