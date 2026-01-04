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
    class="py-24 bg-[#0a0a0a] relative overflow-hidden"
    x-data="{ active: 0 }"> {{-- Mengatur index 0 sebagai default terbuka --}}
    
    <!-- Decorative Diagonal Background -->
    <div class="absolute top-0 left-0 w-full -mt-1 pointer-events-none">
        <div class="h-20 w-full bg-[#111111] transform -skew-y-2 origin-top-left"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-3xl md:text-5xl text-white font-rumonds tracking-[0.2em] uppercase">
                Our <span class="text-gold-400">Achievements</span>
            </h2>
            <div class="h-1 w-20 bg-gold-400 mx-auto mt-4"></div>
        </div>

        <!-- Interactive Accordion Gallery -->
        <div class="flex flex-col md:flex-row gap-3 md:gap-4 h-[700px] md:h-[500px] max-w-7xl mx-auto">
            @foreach($achievements as $index => $achievement)
            <div
                @click="active = {{ $index }}"
                @mouseenter="if(window.innerWidth > 768) active = {{ $index }}"
                :class="active === {{ $index }} ? 'flex-[5] md:flex-[4] border-gold-400/50 shadow-[0_0_30px_rgba(255,215,0,0.15)]' : 'flex-1 md:flex-1 border-white/5 grayscale opacity-60 hover:opacity-100'"
                class="relative transition-all duration-700 ease-[cubic-bezier(0.25,1,0.5,1)] rounded-3xl overflow-hidden cursor-pointer border group bg-neutral-900"
                data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                
                <!-- Image with Zoom Effect -->
                @if($achievement->image)
                    <img src="{{ asset('storage/' . $achievement->image) }}"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 ease-out"
                        :class="active === {{ $index }} ? 'scale-110' : 'scale-100'">
                @endif

                <!-- Elegant Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent transition-opacity duration-700"
                    :class="active === {{ $index }} ? 'opacity-90' : 'opacity-60'">
                </div>

                <!-- Content Wrapper -->
                <div class="absolute inset-0 p-6 md:p-8 flex flex-col justify-end">
                    
                    <!-- Vertical Text (Desktop Only, shown when card is closed) -->
                    <h3 x-show="active !== {{ $index }}" 
                        class="hidden md:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 -rotate-90 whitespace-nowrap text-white/40 font-rumonds tracking-[0.3em] uppercase text-xl transition-all">
                        {{ $achievement->title }}
                    </h3>

                    <!-- Active Content Details -->
                    <div class="relative transition-all duration-500 transform"
                         :class="active === {{ $index }} ? 'translate-y-0 opacity-100' : 'translate-y-10 opacity-0 md:hidden'">
                        
                        <span class="block text-gold-400 text-[10px] md:text-xs font-bold tracking-[0.3em] uppercase mb-2">
                            Honorable Mention
                        </span>
                        
                        <h3 class="text-white font-rumonds tracking-widest uppercase text-2xl md:text-3xl mb-3 leading-tight">
                            {{ $achievement->title }}
                        </h3>

                        <!-- Expandable Description -->
                        <div class="overflow-hidden transition-all duration-700"
                             x-show="active === {{ $index }}"
                             x-transition:enter="transition ease-out duration-500 delay-200"
                             x-transition:enter-start="opacity-0 translate-y-4"
                             x-transition:enter-end="opacity-100 translate-y-0">
                            <p class="text-gray-300 text-sm md:text-base font-light leading-relaxed max-w-md line-clamp-3 md:line-clamp-none">
                                {{ $achievement->description }}
                            </p>
                            
                            <div class="mt-4 inline-flex items-center gap-2 text-gold-400 text-[10px] font-bold tracking-widest uppercase border-b border-gold-400/30 pb-1">
                                View Gallery 
                                <i class="fa-solid fa-arrow-right-long transition-transform group-hover:translate-x-2"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Active Line -->
                <div class="absolute top-0 left-0 h-[2px] bg-gold-400 transition-all duration-700"
                    :class="active === {{ $index }} ? 'w-full' : 'w-0'">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif