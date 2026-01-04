{{-- About Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $about = $about ?? cache()->remember('component_about', 3600, function () {
        return \App\Models\TentangKami::select('id', 'title', 'subtitle', 'image', 'video_url', 'video_description', 'is_active')
            ->where('is_active', true)
            ->first();
    });
    // Tidak ada fallback values - hanya menampilkan data dari database
    $title = $about && $about->title && trim($about->title) !== '' ? trim($about->title) : '';
    $content = $about && $about->subtitle && trim($about->subtitle) !== '' ? trim($about->subtitle) : '';
    $image = $about && $about->image ? asset('storage/' . $about->image) : '';
    $videoUrl = $about && $about->video_url && trim($about->video_url) !== '' ? trim($about->video_url) : '';
    $videoDescription = $about && $about->video_description && trim($about->video_description) !== '' ? trim($about->video_description) : '';

    // Extract YouTube video ID from various URL formats
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

@if($about && ($title || $content || $image))
<section id="about" class="relative py-24 bg-[#111111] overflow-hidden -mt-10">
    <!-- Background Elements -->
    <div
        class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-[#1a1a1a] to-transparent skew-x-12 opacity-50">
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="flex flex-col md:flex-row items-center gap-12">
            <!-- Text Content -->
            <div class="md:w-1/2 text-left" data-aos="fade-right" data-aos-duration="1000">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-1 w-12 bg-gold-400"></div>
                    <h2 class="text-4xl md:text-5xl text-white font-rumonds tracking-wide">{{ $title }}</h2>
                </div>

                @if($content)
                <div class="text-gray-300 text-sm md:text-base leading-relaxed text-justify font-light space-y-6">
                    {!! nl2br(e($content)) !!}
                </div>
                @endif
            </div>

            <!-- Image/Visual -->
            @if($image)
            <div class="md:w-1/2 relative flex justify-center" data-aos="fade-left" data-aos-duration="1000">
                <div class="relative w-full max-w-[500px]">
                    <!-- Image -->
                    <img src="{{ $image }}" alt="About Class Billiard"
                        class="relative z-10 w-full h-[400px] object-cover rounded-3xl grayscale hover:grayscale-0 transition duration-500 shadow-2xl block">
                    <!-- Decorative Border (Overlay) -->
                    <div class="absolute inset-0 border-4 border-gold-400 rounded-3xl z-20 pointer-events-none">
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

{{-- Video Section (Below About) --}}
@if($about && $youtubeId)
<section class="relative py-24 bg-[#0a0a0a] overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-transparent via-gold-400/10 to-transparent"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="max-w-5xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-12" data-aos="fade-up">
                <div class="flex items-center justify-center gap-4 mb-6">
                    <div class="h-1 w-12 bg-gold-400"></div>
                    <h2 class="text-3xl md:text-4xl text-white font-rumonds tracking-wide">VIDEO PRODUCTION</h2>
                    <div class="h-1 w-12 bg-gold-400"></div>
                </div>
                @if($videoDescription)
                <p class="text-gray-400 text-sm md:text-base font-light max-w-2xl mx-auto">
                    {{ $videoDescription }}
                </p>
                @endif
            </div>

            <!-- Video Container -->
            <div class="relative w-full aspect-video rounded-2xl overflow-hidden border-4 border-gold-400/30 shadow-2xl group" data-aos="fade-up" data-aos-delay="200">
                <!-- YouTube Iframe with Autoplay -->
                <iframe
                    width="100%"
                    height="100%"
                    src="https://www.youtube.com/embed/{{ $youtubeId }}?autoplay=1&mute=1&loop=1&playlist={{ $youtubeId }}&controls=1&modestbranding=1"
                    class="w-full h-full absolute top-0 left-0"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    loading="lazy">
                </iframe>
                
                <!-- Decorative Border Glow Effect -->
                <div class="absolute inset-0 border-4 border-gold-400/0 group-hover:border-gold-400/50 rounded-2xl transition-all duration-500 pointer-events-none"></div>
            </div>
        </div>
    </div>
</section>
@endif

