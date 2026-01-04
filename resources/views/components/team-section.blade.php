{{-- Team Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $teamMembers = $teamMembers ?? cache()->remember('component_team', 1800, function () {
        return \App\Models\TimKami::select('id', 'title', 'subtitle', 'name', 'position', 'bio', 'photo', 'image', 'facebook_url', 'instagram_url', 'linkedin_url', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    });
@endphp

@if($teamMembers->count() > 0)
<section id="team" class="py-24 bg-[#0a0a0a] relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
        <div
            class="absolute top-0 left-1/4 w-px h-full bg-gradient-to-b from-transparent via-white/10 to-transparent">
        </div>
        <div
            class="absolute top-0 right-1/4 w-px h-full bg-gradient-to-b from-transparent via-white/10 to-transparent">
        </div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-20" data-aos="fade-up">
            <span class="text-gold-400 font-bold tracking-[0.3em] text-sm uppercase mb-4 block">The Masters</span>
            <h2 class="text-4xl md:text-6xl text-white font-rumonds tracking-wide">OUR <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white">ELITE TEAM</span>
            </h2>
        </div>

        <!-- Team Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($teamMembers as $index => $member)
            <div class="group relative h-[600px] overflow-hidden rounded-sm cursor-pointer {{ $index == 1 ? 'md:-mt-8' : '' }}"
                data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <!-- Background Name (Vertical) -->
                <div
                    class="absolute top-0 left-0 h-full w-full flex items-center justify-center z-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                    <span
                        class="text-[120px] font-bold text-white transform -rotate-90 whitespace-nowrap font-rumonds">{{ strtoupper(explode(' ', $member->name)[0]) }}</span>
                </div>

                <!-- Image -->
                @if($member->image || $member->photo)
                <img src="{{ $member->image ? asset('storage/' . $member->image) : asset('storage/' . $member->photo) }}" alt="{{ $member->name }}"
                    class="absolute inset-0 w-full h-full object-cover filter grayscale contrast-125 transition-all duration-700 group-hover:grayscale-0 group-hover:scale-110 z-10">
                @else
                <div class="absolute inset-0 w-full h-full bg-gray-900 flex items-center justify-center z-10">
                    <p class="text-gray-600 text-sm">No image</p>
                </div>
                @endif

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-20"></div>

                <!-- Info Box -->
                <div
                    class="absolute bottom-0 left-0 w-full p-8 z-30 transform translate-y-8 group-hover:translate-y-0 transition-transform duration-500">
                    <div class="border-l-4 border-gold-400 pl-4">
                        <h3 class="{{ $index == 1 ? 'text-4xl' : 'text-3xl' }} text-white font-serif italic mb-1">{{ $member->name }}</h3>
                        <p class="text-gold-400 text-xs font-bold tracking-[0.2em] uppercase mb-4">{{ $member->position }}</p>
                        @if($member->bio)
                        <p
                            class="text-gray-400 text-sm font-light opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2 mb-3">
                            {{ $member->bio }}
                        </p>
                        @endif
                        
                        <!-- Social Media Icons -->
                        @php
                            $facebookUrl = $member->facebook_url && trim($member->facebook_url) !== '' ? trim($member->facebook_url) : '';
                            $instagramUrl = $member->instagram_url && trim($member->instagram_url) !== '' ? trim($member->instagram_url) : '';
                            $linkedinUrl = $member->linkedin_url && trim($member->linkedin_url) !== '' ? trim($member->linkedin_url) : '';
                        @endphp
                        @if($facebookUrl || $instagramUrl || $linkedinUrl)
                        <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-200">
                            @if($facebookUrl)
                            <a href="{{ $facebookUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-8 h-8 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-facebook-f text-xs"></i>
                            </a>
                            @endif
                            @if($instagramUrl)
                            <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-8 h-8 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-instagram text-xs"></i>
                            </a>
                            @endif
                            @if($linkedinUrl)
                            <a href="{{ $linkedinUrl }}" target="_blank" rel="noopener noreferrer"
                                class="w-8 h-8 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-linkedin-in text-xs"></i>
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                @if($index == 1)
                <!-- Gold Frame Effect for Center -->
                <div
                    class="absolute inset-0 border border-gold-400/0 group-hover:border-gold-400/50 transition-colors duration-500 z-30 pointer-events-none">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

