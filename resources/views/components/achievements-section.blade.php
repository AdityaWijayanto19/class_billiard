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
<section id="achievements" class="py-24 bg-[#0a0a0a] relative overflow-hidden">
    <!-- Diagonal Separator Top -->
    <div class="absolute top-0 left-0 w-full -mt-1">
        <div class="h-16 w-full bg-[#111111] transform -skew-y-2 origin-top-left"></div>
        <div class="h-2 w-full bg-gold-600 transform -skew-y-2 origin-top-left translate-y-[-0.5rem] opacity-70">
        </div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <h2 class="text-3xl md:text-4xl text-white text-center mb-16 font-rumonds tracking-widest uppercase">
            OUR ACHIEVEMENT
        </h2>

        <!-- Interactive Expanding Gallery (Accordion Style) -->
        <div class="flex flex-col md:flex-row gap-2 md:gap-4 h-[500px] max-w-7xl mx-auto px-4">
            @foreach($achievements as $index => $achievement)
            @php
                // Card ke-3 (index 2) adalah card tengah yang lebih besar
                $isCenterCard = $index == 2;
            @endphp
            <div
                class="relative {{ $isCenterCard ? 'flex-[3] hover:flex-[4]' : 'flex-1 hover:flex-[3]' }} transition-all duration-700 ease-in-out rounded-3xl overflow-hidden group cursor-pointer border {{ $isCenterCard ? 'border-2 border-gold-400 shadow-[0_0_20px_rgba(255,215,0,0.3)]' : 'border-gray-800 hover:border-gold-400' }}">
                @if($achievement->image)
                <img src="{{ asset('storage/' . $achievement->image) }}"
                    class="absolute inset-0 w-full h-full object-cover object-center transition-transform duration-700 group-hover:scale-110 {{ $isCenterCard ? '' : 'grayscale group-hover:grayscale-0' }}">
                @else
                <div class="absolute inset-0 w-full h-full bg-gray-900 flex items-center justify-center">
                    <p class="text-gray-600 text-sm">No image</p>
                </div>
                @endif
                <div class="absolute inset-0 {{ $isCenterCard ? 'bg-gradient-to-t from-black/80 via-transparent to-transparent' : 'bg-black/60 group-hover:bg-transparent transition-colors duration-500' }}">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full {{ $isCenterCard ? 'p-8' : 'p-6 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100' }} transition-all duration-500 bg-gradient-to-t from-black/90 to-transparent">
                    <h3 class="text-gold-400 font-bold {{ $isCenterCard ? 'text-3xl font-rumonds tracking-widest uppercase mb-1' : 'text-xl tracking-wider uppercase' }}">{{ $achievement->title ?? $achievement->label }}</h3>
                    <p class="text-white {{ $isCenterCard ? 'text-base' : 'text-sm' }} font-light">{{ $achievement->description ?? '' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

