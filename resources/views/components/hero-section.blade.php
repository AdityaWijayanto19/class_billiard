{{-- Hero Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $hero = $hero ?? cache()->remember('component_hero', 3600, function () {
        return \App\Models\HeroSection::select('id', 'background_image', 'logo_image', 'title', 'subtitle', 'tagline', 'cta_text_1', 'cta_link_1', 'cta_text_2', 'is_active')
            ->where('is_active', true)
            ->first();
    });
    
    // Tidak ada fallback values - hanya menampilkan data dari database
    if ($hero) {
        $logoImage = $hero->logo_image_url;
        $backgroundImage = $hero->background_image_url;
        $title = $hero->title && trim($hero->title) !== '' ? trim($hero->title) : '';
        $subtitle = $hero->subtitle && trim($hero->subtitle) !== '' ? trim($hero->subtitle) : '';
        $tagline = $hero->tagline && trim($hero->tagline) !== '' ? trim($hero->tagline) : '';
        $ctaText1 = $hero->cta_text_1 && trim($hero->cta_text_1) !== '' ? trim($hero->cta_text_1) : '';
        $ctaText2 = $hero->cta_text_2 && trim($hero->cta_text_2) !== '' ? trim($hero->cta_text_2) : '';
        $ctaLink1 = (isset($hero->cta_link_1) && $hero->cta_link_1 && trim($hero->cta_link_1) !== '') ? trim($hero->cta_link_1) : null;
        $ctaLink1IsExternal = $ctaLink1 && preg_match('#^https?://#i', $ctaLink1);
    } else {
        $logoImage = '';
        $backgroundImage = '';
        $title = '';
        $subtitle = '';
        $tagline = '';
        $ctaText1 = '';
        $ctaText2 = '';
        $ctaLink1 = null;
        $ctaLink1IsExternal = false;
    }
@endphp

@if($hero && ($backgroundImage || $logoImage || $title || $subtitle))
<header class="relative h-screen min-h-[700px] flex items-center justify-center overflow-hidden">
    <!-- Background Image with Parallax Feel -->
    <div class="absolute inset-0 z-0" id="heroParallaxBg">
        @if($backgroundImage)
        <img src="{{ $backgroundImage }}" alt="Background"
            class="w-full h-full object-cover scale-105 filter brightness-[0.3] contrast-125"
            id="heroParallaxImage">
        @else
        <div class="w-full h-full bg-black"></div>
        @endif
        <!-- Vignette & Gradient -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/20 to-[#111111]"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(0,0,0,0.8)_100%)]">
        </div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 flex flex-col items-center text-center px-4">
        <!-- Decorative Line -->
        <div class="h-16 w-px bg-gradient-to-b from-transparent via-gold-400 to-transparent mb-8 opacity-50"
            data-aos="fade-down" data-aos-delay="200"></div>

        <!-- Main Logo (Refined) -->
        @if($logoImage)
        <div class="mb-10 relative group cursor-default" data-aos="zoom-in" data-aos-duration="1500">
            <div
                class="absolute -inset-8 bg-gold-400/10 blur-[40px] rounded-full group-hover:bg-gold-400/20 transition duration-700">
            </div>
            <img src="{{ $logoImage }}" alt="CLASS BILLIARD"
                class="h-40 md:h-56 w-auto object-contain drop-shadow-[0_10px_30px_rgba(0,0,0,0.5)] relative z-10 transform transition duration-700 group-hover:scale-105">
        </div>
        @endif

        <!-- Typography -->
        @if($title || $subtitle)
        <h1 class="text-white text-4xl md:text-6xl font-serif font-light tracking-wide mb-4" data-aos="fade-up"
            data-aos-delay="300">
            @if($title){{ $title }}@endif @if($subtitle)<span class="text-gold-400 font-serif italic pr-2">{{ $subtitle }}</span>@endif
        </h1>
        @endif

        @if($tagline)
        <p class="text-gray-400 text-sm md:text-base tracking-[0.3em] uppercase mb-12 font-light border-t border-b border-white/10 py-3 px-8"
            data-aos="fade-up" data-aos-delay="500">
            {{ $tagline }}
        </p>
        @endif

        <!-- CTA Buttons -->
        @if($ctaText1 || $ctaText2)
        <div class="flex flex-col md:flex-row gap-6" data-aos="fade-up" data-aos-delay="700">
            @if($ctaText1)
            <a href="{{ $ctaLink1 ? $ctaLink1 : '#reservation' }}"
                @if($ctaLink1IsExternal) target="_blank" rel="noopener noreferrer" @endif
                class="group relative px-8 py-4 bg-transparent overflow-hidden rounded-sm transition-all duration-300">
                <div
                    class="absolute inset-0 w-full h-full bg-gold-400/90 group-hover:bg-gold-500 transition duration-300">
                </div>
                <span class="relative text-black font-bold tracking-widest text-sm flex items-center gap-2">
                    {{ $ctaText1 }}
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </span>
            </a>
            @endif
            @if($ctaText2)
            <a href="#about"
                class="px-8 py-4 border border-white/20 text-white hover:border-white hover:bg-white/5 transition duration-300 rounded-sm font-light tracking-widest text-sm">
                {{ $ctaText2 }}
            </a>
            @endif
        </div>
        @endif
    </div>

    <!-- Scroll Indicator -->
   
</header>

<!-- Parallax Scroll Script -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const bgImage = document.getElementById('heroParallaxImage');
        const header = document.querySelector('header');
        
        if (!bgImage || !header) return;
        
        // Parallax speed multiplier (0.5 = moves at 50% of scroll speed)
        const parallaxSpeed = 0.5;
        
        // Smoothing animation frame for better performance
        let ticking = false;
        let scrollY = 0;
        
        window.addEventListener('scroll', () => {
            scrollY = window.scrollY;
            
            if (!ticking) {
                window.requestAnimationFrame(() => {
                    // Only apply parallax while header is in view
                    const headerRect = header.getBoundingClientRect();
                    if (headerRect.bottom > 0) {
                        const offset = scrollY * parallaxSpeed;
                        bgImage.style.transform = `translateY(${offset}px)`;
                    }
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });
    });
</script>
@endif

