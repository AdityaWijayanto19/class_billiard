{{-- About Founder Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $founder = $founder ?? cache()->remember('component_founder', 3600, function () {
        return \App\Models\AboutFounder::select('id', 'title', 'subtitle', 'name', 'position', 'description', 'quote', 'signature', 'photo', 'image', 'video_url', 'facebook_url', 'instagram_url', 'linkedin_url', 'is_active')
            ->where('is_active', true)
            ->first();
    });
    // Tidak ada fallback values - hanya menampilkan data dari database
    $name = $founder && $founder->name && trim($founder->name) !== '' ? trim($founder->name) : '';
    $position = $founder && $founder->position && trim($founder->position) !== '' ? trim($founder->position) : '';
    $subtitle = $founder && $founder->subtitle && trim($founder->subtitle) !== '' ? trim($founder->subtitle) : '';
    $quote = $founder && $founder->quote && trim($founder->quote) !== '' ? trim($founder->quote) : '';
    $image = ($founder && $founder->image ? asset('storage/' . $founder->image) : ($founder && $founder->photo ? asset('storage/' . $founder->photo) : ''));
    $videoUrl = $founder && $founder->video_url && trim($founder->video_url) !== '' ? trim($founder->video_url) : '';
    $signature = $founder && $founder->signature && trim($founder->signature) !== '' ? trim($founder->signature) : '';
    $facebookUrl = $founder && $founder->facebook_url && trim($founder->facebook_url) !== '' ? trim($founder->facebook_url) : '';
    $instagramUrl = $founder && $founder->instagram_url && trim($founder->instagram_url) !== '' ? trim($founder->instagram_url) : '';
    $linkedinUrl = $founder && $founder->linkedin_url && trim($founder->linkedin_url) !== '' ? trim($founder->linkedin_url) : '';

    // Extract YouTube video ID from URL
    $youtubeId = '';
    if ($videoUrl) {
        // Support multiple YouTube URL formats
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
            $youtubeId = $matches[1];
        } elseif (preg_match('/([a-zA-Z0-9_-]{11})/', $videoUrl, $matches)) {
            // Fallback: if it looks like a video ID
            $youtubeId = $matches[1];
        }
    }
@endphp

@if($founder && ($name || $position || $subtitle || $quote || $image || $videoUrl))
<section id="founder" class="py-24 bg-[#0F0F0F] relative overflow-hidden">
    <!-- Background Typography (Watermark) -->
    <div class="absolute top-10 left-0 w-full select-none pointer-events-none overflow-hidden leading-none"
        data-aos="fade-down" data-aos-duration="1500" data-aos-anchor-placement="top-bottom">
        <h2
            class="text-[150px] md:text-[250px] font-bold text-white/[0.03] text-center whitespace-nowrap tracking-tighter">
            THE FOUNDER
        </h2>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-center relative">

            <!-- Image Section (Right Side, but rendered first for stacking context usually, but here we want text on top) -->
            @if($image || $youtubeId)
            <div class="md:w-1/2 relative order-1 md:order-2 group md:-mt-16" data-aos="fade-up"
                data-aos-duration="1200">
                <div class="relative w-full h-[450px] md:h-[550px] overflow-hidden rounded-lg shadow-2xl">
                    {{-- YouTube Video (Priority if video_url exists) --}}
                    @if($youtubeId)
                    <iframe 
                        width="100%" 
                        height="100%" 
                        src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1&mute=1&loop=1&playlist={{ $youtubeId }}&controls=1&modestbranding=1"
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen
                        class="absolute top-0 left-0 w-full h-full"
                        style="border-radius: 8px;">
                    </iframe>
                    @else
                    {{-- Fallback to Image --}}
                    <!-- Main Image -->
                    <img src="{{ $image }}" alt="Founder"
                        class="w-full h-full object-cover filter grayscale contrast-125 transition-all duration-1000 group-hover:grayscale-0 group-hover:scale-105"
                        style="object-position: top center;">

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-l from-transparent via-black/20 to-black/80"></div>
                    @endif

                    <!-- Gold Border Frame -->
                    <div
                        class="absolute top-4 right-4 w-full h-full border-2 border-gold-400/50 rounded-lg -z-10 translate-x-4 translate-y-4 hidden md:block transition-transform duration-500 group-hover:translate-x-6 group-hover:translate-y-6">
                    </div>
                </div>
            </div>
            @endif

            <!-- Text Content (Left Side - Overlapping) -->
            <div class="md:w-1/2 relative order-2 md:order-1 mt-[-80px] md:mt-0 md:mr-[-50px] z-20"
                data-aos="fade-right" data-aos-delay="300" data-aos-duration="1200">
                <div
                    class="bg-[#1a1a1a]/95 backdrop-blur-sm p-8 md:p-12 border-l-4 border-gold-400 shadow-2xl rounded-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="h-px w-12 bg-gold-400"></span>
                        <span class="text-gold-400 font-bold tracking-[0.2em] uppercase text-sm">Visionary</span>
                    </div>

                    @if($subtitle)
                    <h3 class="text-lg md:text-3xl text-white font-serif italic mb-6 leading-tight">
                        {!! nl2br(e($subtitle)) !!}
                    </h3>
                    @endif

                    @if($quote)
                    <p class="text-gray-400 text-base leading-relaxed mb-8 font-light">
                        "{{ $quote }}"
                    </p>
                    @endif

                    <div class="border-t border-white/10 pt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                @if($name)
                                <h4 class="text-white text-xl font-serif font-bold">{{ $name }}</h4>
                                @endif
                                @if($position)
                                <p class="text-gray-500 text-xs uppercase tracking-wider mt-1">{{ $position }}</p>
                                @endif
                            </div>
                            <!-- Signature (Visual representation) -->
                            @if($signature)
                            <div class="text-gold-400/50 font-serif italic text-2xl pr-4">{{ $signature }}</div>
                            @endif
                        </div>
                        
                        <!-- Social Media Icons -->
                        @if($facebookUrl || $instagramUrl || $linkedinUrl)
                        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-white/5">
                            @if($facebookUrl)
                            <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-10 h-10 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif
                            @if($instagramUrl)
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-10 h-10 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if($linkedinUrl)
                            <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-10 h-10 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endif

