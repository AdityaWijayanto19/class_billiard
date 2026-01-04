{{-- Achievements Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $achievements = $achievements ?? cache()->remember('component_achievements', 1800, function () {
        return \App\Models\PortfolioAchievement::select('id', 'title', 'subtitle', 'type', 'icon', 'number', 'label', 'description', 'image', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    });
@endphp

@if($achievements->count() > 0)
<section id="achievements" 
    class="py-16 md:py-24 bg-[#0a0a0a] relative overflow-hidden"
    x-data="{
        active: 2, // default center card open (desktop)
        isMobile: window.innerWidth < 768,
        setActive(index) {
            if (!this.isMobile) this.active = index;
        },
        toggle(index) {
            if (this.isMobile) {
                this.active = this.active === index ? -1 : index;
            }
        }
    }"
    x-init="window.addEventListener('resize', () => { this.isMobile = window.innerWidth < 768; if (!this.isMobile && this.active === -1) this.active = 2; })"
<section id="achievements"
    class="py-16 md:py-24 bg-[#0a0a0a] relative overflow-hidden"
    x-data="{ active: -1, isMobile: window.innerWidth < 768, toggle(index) { if (this.isMobile) { this.active = this.active === index ? -1 : index; } } }"
    x-init="window.addEventListener('resize', () => { this.isMobile = window.innerWidth < 768; })">
    <!-- Diagonal Separator Top -->
    <div class="absolute top-0 left-0 w-full -mt-1">
        <div class="h-16 w-full bg-[#111111] transform -skew-y-2 origin-top-left"></div>
        <div class="h-2 w-full bg-gold-600 transform -skew-y-2 origin-top-left translate-y-[-0.5rem] opacity-70">
        </div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <h2 class="text-3xl md:text-4xl text-white text-center mb-10 md:mb-16 font-rumonds tracking-widest uppercase">
            OUR ACHIEVEMENT
        </h2>

        <!-- DESKTOP: Accordion Gallery -->
            <!-- DESKTOP: Accordion Gallery (original style, no Alpine.js) -->
            <div class="hidden md:flex flex-row gap-2 md:gap-4 h-[500px] max-w-7xl mx-auto px-4">
            @foreach($achievements as $index => $achievement)
            <div
                @click="setActive({{ $index }})"
                @mouseenter="setActive({{ $index }})"
                :class="active === {{ $index }} 
                    class="relative {{ $isCenterCard ? 'flex-[3] hover:flex-[4]' : 'flex-1 hover:flex-[3]' }} transition-all duration-700 ease-in-out rounded-3xl overflow-hidden group cursor-pointer border {{ $isCenterCard ? 'border-2 border-gold-400 shadow-[0_0_20px_rgba(255,215,0,0.3)]' : 'border-gray-800 hover:border-gold-400' }}">
                @else
                <div class="absolute inset-0 w-full h-full bg-gray-900 flex items-center justify-center">
                    <p class="text-gray-600 text-sm">No image</p>
                </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full p-8 transition-all duration-500 bg-gradient-to-t from-black/90 to-transparent">
                    <h3 class="text-gold-400 font-bold text-3xl font-rumonds tracking-widest uppercase mb-1">{{ $achievement->title ?? $achievement->label }}</h3>
                    <p class="text-white text-base font-light">{{ $achievement->description ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- MOBILE: Tap-to-expand Cards -->
        <div class="md:hidden flex flex-col gap-3 max-w-lg mx-auto">
            @foreach($achievements as $index => $achievement)
            <div
                @click="toggle({{ $index }})"
                :class="active === {{ $index }} 
                    ? 'border-gold-400/70 shadow-[0_0_20px_rgba(255,215,0,0.15)]' 
                    : 'border-gray-800'"
                class="relative rounded-2xl overflow-hidden cursor-pointer border bg-neutral-900 transition-all duration-500 ease-out">
                <div class="relative h-24 overflow-hidden">
                    @if($achievement->image)
                        <img src="{{ asset('storage/' . $achievement->image) }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700"
                            :class="active === {{ $index }} ? 'scale-110' : 'scale-100'">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-transparent"></div>
                    <div class="absolute inset-0 p-4 flex items-center justify-between">
                        <div>
                            <span class="text-gold-400 text-[10px] font-bold tracking-[0.2em] uppercase block mb-1">Achievement #{{ $index + 1 }}</span>
                            <h3 class="text-white font-rumonds tracking-wider uppercase text-lg leading-tight">{{ $achievement->title }}</h3>
                        </div>
                        <div class="w-10 h-10 rounded-full border border-gold-400/30 flex items-center justify-center transition-all duration-300"
                             :class="active === {{ $index }} ? 'bg-gold-400 rotate-180' : 'bg-transparent'">
                            <i class="fa-solid fa-chevron-down text-sm transition-colors duration-300"
                               :class="active === {{ $index }} ? 'text-black' : 'text-gold-400'"></i>
                        </div>
                    </div>
                </div>
                <div class="overflow-hidden transition-all duration-500 ease-out"
                     :style="active === {{ $index }} ? 'max-height: 350px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    <div class="relative h-40 overflow-hidden">
                        @if($achievement->image)
                            <img src="{{ asset('storage/' . $achievement->image) }}"
                                class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-900 via-transparent to-transparent"></div>
                    </div>
                    <div class="p-5 pt-0 -mt-8 relative z-10">
                        <p class="text-gray-300 text-sm font-light leading-relaxed">{{ $achievement->description }}</p>
                        <div class="mt-4 h-px w-full bg-gradient-to-r from-gold-400/50 via-gold-400/20 to-transparent"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="md:hidden text-center mt-6 text-gray-500 text-xs tracking-wider uppercase">
            <i class="fa-solid fa-hand-pointer mr-2"></i> Tap to expand
        </div>
    </div>
</section>
@endif

