{{-- Achievements Section Component --}}
@php
    $achievements = $achievements ?? cache()->remember('component_achievements', 1800, function () {
        return \App\Models\PortfolioAchievement::select(
            'id','title','subtitle','type','icon','number','label',
            'description','image','order','is_active'
        )
        ->where('is_active', true)
        ->orderBy('order')
        ->get();
    });
@endphp

@if($achievements->count() > 0)
<section
    id="achievements"
    class="py-16 md:py-24 bg-[#0a0a0a] relative overflow-hidden"
    x-data="{
        scroll(el, dir) {
            el.scrollBy({ left: dir * 380, behavior: 'smooth' })
        }
    }"
>
    <!-- Diagonal Top -->
    <div class="absolute top-0 left-0 w-full -mt-1">
        <div class="h-16 w-full bg-[#111111] -skew-y-2 origin-top-left"></div>
        <div class="h-2 w-full bg-gold-600 -skew-y-2 origin-top-left -translate-y-2 opacity-70"></div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <h2 class="text-3xl md:text-4xl text-white text-center mb-10 md:mb-14 font-rumonds tracking-widest uppercase">
            Our Achievement
        </h2>

        <!-- DESKTOP NAV -->
        <div class="hidden md:flex justify-end gap-3 mb-4">
            <button
                @click="scroll($refs.carousel, -1)"
                class="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:text-black hover:border-gold-400 transition-all duration-300">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button
                @click="scroll($refs.carousel, 1)"
                class="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:text-black hover:border-gold-400 transition-all duration-300">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>

        <!-- CAROUSEL -->
        <div
            x-ref="carousel"
            class="flex gap-6 overflow-x-auto scroll-smooth snap-x snap-mandatory pb-6 no-scrollbar"
        >
            @foreach($achievements as $index => $achievement)
            <div
                class="snap-center shrink-0 w-[85%] sm:w-[70%] md:w-[420px] h-[420px]
                       relative rounded-3xl overflow-hidden border border-gray-800
                       bg-neutral-900 group transition hover:border-gold-400"
            >
                @if($achievement->image)
                    <img
                        src="{{ asset('storage/'.$achievement->image) }}"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    >
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                <div class="absolute bottom-0 left-0 w-full p-6">
                    <span class="text-gold-400 text-xs tracking-[0.25em] uppercase block mb-2">
                        Achievement #{{ $index + 1 }}
                    </span>
                    <h3 class="text-white text-2xl font-rumonds tracking-widest uppercase mb-2">
                        {{ $achievement->title ?? $achievement->label }}
                    </h3>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        {{ $achievement->description }}
                    </p>

                    <div class="mt-4 h-px w-full bg-gradient-to-r from-gold-400/60 via-gold-400/20 to-transparent"></div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- MOBILE HINT -->
        <div class="md:hidden text-center mt-6 text-gray-500 text-xs tracking-widest uppercase">
            <i class="fa-solid fa-arrows-left-right mr-2"></i> Swipe left / right
        </div>
    </div>
</section>

<style>
/* Hide scrollbar */
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endif
