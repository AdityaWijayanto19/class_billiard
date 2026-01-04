{{-- Events Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $events = $events ?? cache()->remember('component_events', 1800, function () {
        return \App\Models\Event::select('id', 'title', 'subtitle', 'event_title', 'event_description', 'description', 'category', 'event_date', 'image', 'link_url', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('event_date')
            ->get();
    });
@endphp

@if($events->count() > 0)
<section id="events" class="py-24 bg-[#050505] relative overflow-hidden">
    <!-- Decorative Background Glow -->
    <div
        class="absolute top-0 right-0 w-[500px] h-[500px] bg-gold-400/5 rounded-full blur-[100px] pointer-events-none">
    </div>
    <div
        class="absolute bottom-0 left-0 w-[300px] h-[300px] bg-white/5 rounded-full blur-[80px] pointer-events-none">
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6" data-aos="fade-up">
            <div>
                <h2 class="text-4xl md:text-6xl text-white font-rumonds tracking-wide mb-2">UPCOMING <span
                        class="text-gold-400">EVENTS</span></h2>
                <p class="text-gray-400 font-light tracking-[0.2em] text-sm uppercase">Join the excitement at Class
                    Billiard</p>
            </div>
            <!-- Navigation Buttons -->
            <div class="flex gap-4">
                <button id="events-prev-btn"
                    class="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:text-black hover:border-gold-400 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button id="events-next-btn"
                    class="w-12 h-12 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:text-black hover:border-gold-400 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Events Slider (Horizontal Scroll) -->
        <div id="events-slider" class="flex overflow-x-auto gap-8 pb-12 snap-x snap-mandatory scrollbar-hide scroll-smooth"
            style="-ms-overflow-style: none; scrollbar-width: none;">
            @foreach($events as $index => $event)
            <div class="min-w-[320px] md:min-w-[400px] h-[550px] relative rounded-sm overflow-hidden group snap-center cursor-pointer"
                data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <!-- Image -->
                @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->event_title ?? $event->title ?? 'Event' }}"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 filter brightness-75 group-hover:brightness-100">
                @else
                <div class="w-full h-full bg-gray-900 flex items-center justify-center">
                    <p class="text-gray-600 text-sm">No image</p>
                </div>
                @endif

                <!-- Overlay Gradient -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>

                <!-- Content Box -->
                <div class="absolute inset-0 p-8 flex flex-col justify-end">
                    <!-- Date Badge -->
                    @php
                        $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : now();
                        $month = strtoupper($eventDate->format('M'));
                        $day = $eventDate->format('d');
                    @endphp
                    <div
                        class="absolute top-6 left-6 {{ $index == 0 ? 'bg-gold-400/90 backdrop-blur-md px-4 py-3 text-center shadow-[0_0_20px_rgba(255,215,0,0.3)] transform transition-transform duration-300 group-hover:-translate-y-1' : 'bg-[#1a1a1a]/90 backdrop-blur-md px-4 py-3 text-center border border-white/10' }}">
                        <span class="block text-xs font-bold {{ $index == 0 ? 'text-black' : 'text-gold-400' }} uppercase tracking-wider mb-1">{{ $month }}</span>
                        <span class="block text-3xl font-rumonds {{ $index == 0 ? 'text-black' : 'text-white' }} leading-none">{{ $day }}</span>
                    </div>

                    <!-- Text Info -->
                    <div
                        class="transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                        <div
                            class="flex items-center gap-3 mb-3 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                            <span
                                class="px-3 py-1 border border-gold-400 rounded-full text-[10px] font-bold text-gold-400 uppercase tracking-widest">{{ $event->category ?? 'Event' }}</span>
                        </div>
                        <h3
                            class="text-3xl text-white font-serif mb-3 leading-tight group-hover:text-gold-400 transition-colors duration-300">
                            {{ $event->event_title ?? $event->title }}</h3>
                        <p
                            class="text-gray-300 text-sm font-light line-clamp-2 mb-6 opacity-80 group-hover:opacity-100">
                            {{ $event->description ?? $event->event_description }}
                        </p>
                        <span
                            class="inline-flex items-center gap-2 text-white text-xs font-bold tracking-[0.2em] uppercase border-b border-gold-400 pb-1 group-hover:text-gold-400 transition-colors">
                            View Details
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </div>
                </div>
                <!-- Hover Border -->
                <div
                    class="absolute inset-0 border border-white/10 group-hover:border-gold-400/50 transition-colors duration-500 pointer-events-none">
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('events-slider');
        const prevBtn = document.getElementById('events-prev-btn');
        const nextBtn = document.getElementById('events-next-btn');
        
        if (slider && prevBtn && nextBtn) {
            const scrollAmount = 420; // Width of card + gap (400px + 32px gap)
            
            prevBtn.addEventListener('click', function() {
                slider.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            });
            
            nextBtn.addEventListener('click', function() {
                slider.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>
@endif

