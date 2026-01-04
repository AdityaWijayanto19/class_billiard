@php
    $achievements = $achievements ?? cache()->remember('component_achievements', 1800, function () {
        return \App\Models\PortfolioAchievement::select('id', 'title', 'subtitle', 'image', 'description', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    });
@endphp

@if($achievements->count() > 0)
<section id="achievements" 
    class="py-16 md:py-24 bg-[#0a0a0a] relative overflow-hidden"
    x-data="{ 
        active: 0,
        isMobile: window.innerWidth < 768,
        toggle(index) {
            if (this.isMobile) {
                this.active = this.active === index ? -1 : index;
            } else {
                this.active = index;
            }
        }
    }"
    x-init="window.addEventListener('resize', () => isMobile = window.innerWidth < 768)">
    
    <!-- Decorative Diagonal Background -->
    <div class="absolute top-0 left-0 w-full -mt-1 pointer-events-none">
        <div class="h-16 md:h-20 w-full bg-[#111111] transform -skew-y-2 origin-top-left"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-10 md:mb-16" data-aos="fade-up">
            <span class="text-gold-400 text-xs md:text-sm font-bold tracking-[0.3em] uppercase mb-3 block">Portfolio</span>
            <h2 class="text-2xl md:text-5xl text-white font-rumonds tracking-[0.15em] md:tracking-[0.2em] uppercase">
                Our <span class="text-gold-400">Achievements</span>
            </h2>
            <div class="h-0.5 md:h-1 w-16 md:w-20 bg-gradient-to-r from-gold-400 to-gold-600 mx-auto mt-4"></div>
        </div>

        <!-- DESKTOP: Horizontal Accordion Gallery -->
        <div class="hidden md:flex flex-row gap-4 h-[500px] max-w-7xl mx-auto">
            @foreach($achievements as $index => $achievement)
            <div
                @click="active = {{ $index }}"
                @mouseenter="active = {{ $index }}"
                :class="active === {{ $index }} 
                    ? 'flex-[4] border-gold-400/50 shadow-[0_0_40px_rgba(255,215,0,0.2)]' 
                    : 'flex-1 border-white/5 grayscale-[50%] opacity-70 hover:opacity-90'"
                class="relative transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] rounded-2xl overflow-hidden cursor-pointer border group bg-neutral-900"
                data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                
                <!-- Image -->
                @if($achievement->image)
                    <img src="{{ asset('storage/' . $achievement->image) }}"
                        alt="{{ $achievement->title }}"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 ease-out"
                        :class="active === {{ $index }} ? 'scale-110' : 'scale-100'">
                @endif

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent transition-opacity duration-500"
                    :class="active === {{ $index }} ? 'opacity-100' : 'opacity-70'"></div>

                <!-- Vertical Title (Collapsed State) -->
                <div x-show="active !== {{ $index }}" 
                     x-transition:enter="transition ease-out duration-300 delay-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="absolute inset-0 flex items-center justify-center">
                    <h3 class="text-white/50 font-rumonds tracking-[0.3em] uppercase text-lg -rotate-90 whitespace-nowrap">
                        {{ $achievement->title }}
                    </h3>
                </div>

                <!-- Active Content -->
                <div class="absolute bottom-0 left-0 right-0 p-8 transition-all duration-500"
                     x-show="active === {{ $index }}"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-8"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    
                    <span class="text-gold-400 text-xs font-bold tracking-[0.3em] uppercase mb-2 block">Achievement</span>
                    <h3 class="text-white font-rumonds tracking-widest uppercase text-3xl mb-3">{{ $achievement->title }}</h3>
                    <p class="text-gray-300 text-base font-light leading-relaxed max-w-md">{{ $achievement->description }}</p>
                </div>

                <!-- Top Gold Line -->
                <div class="absolute top-0 left-0 h-[3px] bg-gradient-to-r from-gold-400 to-gold-600 transition-all duration-700"
                    :class="active === {{ $index }} ? 'w-full' : 'w-0'"></div>
            </div>
            @endforeach
        </div>

        <!-- MOBILE: Vertical Stack Cards -->
        <div class="md:hidden flex flex-col gap-3 max-w-lg mx-auto">
            @foreach($achievements as $index => $achievement)
            <div
                @click="toggle({{ $index }})"
                :class="active === {{ $index }} 
                    ? 'border-gold-400/50 shadow-[0_0_25px_rgba(255,215,0,0.15)]' 
                    : 'border-white/10'"
                class="relative rounded-2xl overflow-hidden cursor-pointer border bg-neutral-900 transition-all duration-500 ease-out"
                data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
                
                <!-- Card Header (Always Visible) -->
                <div class="relative h-24 overflow-hidden">
                    @if($achievement->image)
                        <img src="{{ asset('storage/' . $achievement->image) }}"
                            alt="{{ $achievement->title }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700"
                            :class="active === {{ $index }} ? 'scale-110' : 'scale-100'">
                    @endif
                    
                    <!-- Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/70 to-transparent"></div>
                    
                    <!-- Header Content -->
                    <div class="absolute inset-0 p-4 flex items-center justify-between">
                        <div>
                            <span class="text-gold-400 text-[10px] font-bold tracking-[0.2em] uppercase block mb-1">Achievement #{{ $index + 1 }}</span>
                            <h3 class="text-white font-rumonds tracking-wider uppercase text-lg leading-tight">{{ $achievement->title }}</h3>
                        </div>
                        
                        <!-- Expand Icon -->
                        <div class="w-10 h-10 rounded-full border border-gold-400/30 flex items-center justify-center transition-all duration-300"
                             :class="active === {{ $index }} ? 'bg-gold-400 rotate-180' : 'bg-transparent'">
                            <i class="fa-solid fa-chevron-down text-sm transition-colors duration-300"
                               :class="active === {{ $index }} ? 'text-black' : 'text-gold-400'"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Expandable Content -->
                <div class="overflow-hidden transition-all duration-500 ease-out"
                     :style="active === {{ $index }} ? 'max-height: 350px; opacity: 1;' : 'max-height: 0; opacity: 0;'">
                    
                    <!-- Full Image -->
                    <div class="relative h-48 overflow-hidden">
                        @if($achievement->image)
                            <img src="{{ asset('storage/' . $achievement->image) }}"
                                alt="{{ $achievement->title }}"
                                class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-neutral-900 via-transparent to-transparent"></div>
                    </div>
                    
                    <!-- Description -->
                    <div class="p-5 pt-0 -mt-8 relative z-10">
                        <p class="text-gray-300 text-sm font-light leading-relaxed">{{ $achievement->description }}</p>
                        
                        <!-- Decorative Line -->
                        <div class="mt-4 h-px w-full bg-gradient-to-r from-gold-400/50 via-gold-400/20 to-transparent"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Mobile Swipe Hint -->
        <div class="md:hidden text-center mt-6 text-gray-500 text-xs tracking-wider uppercase" data-aos="fade-up">
            <i class="fa-solid fa-hand-pointer mr-2"></i> Tap to expand
        </div>
    </div>
</section>
@endif