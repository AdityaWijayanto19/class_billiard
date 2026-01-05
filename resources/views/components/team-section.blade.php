{{-- Team Section Component --}}
@php
    /** 
     * TEKNIK PEMANGGILAN DATA ANTI-GHAIB
     * Langsung tarik dari DB tanpa cache agar admin langsung sinkron.
     */
    try {
        $teamMembers = \App\Models\TimKami::where('is_active', 1)->orderBy('order', 'asc')->get();
        $proTeams = \App\Models\ProTeam::where('is_active', 1)->orderBy('order', 'asc')->get();
    } catch (\Exception $e) {
        $teamMembers = collect();
        $proTeams = collect();
    }
@endphp

@if($teamMembers->count() > 0 || $proTeams->count() > 0)
<section id="team" class="py-24 lg:py-32 bg-[#050505] relative overflow-hidden">
    
    <!-- LUXURY FLOW BACKGROUND (Silk & Energy Flow) -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <svg class="absolute top-0 left-0 w-full h-full opacity-25" viewBox="0 0 1440 1200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Elegant Silk Curves (Menghubungkan Section Master ke Pro) -->
            <path d="M-100 200C200 400 600 -100 900 300C1200 700 1300 900 1600 800" stroke="url(#gold_silk_grad)" stroke-width="1.5" stroke-dasharray="15 10" />
            <path d="M-50 250C250 450 650 -50 950 350C1250 750 1350 950 1650 850" stroke="url(#gold_silk_grad)" stroke-width="0.5" opacity="0.4" />
            
            <!-- Bottom Golden Glow -->
            <path d="M0 1000C300 950 600 1100 900 950C1200 800 1440 850 1440 850V1200H0V1000Z" fill="url(#gold_soft_glow)" opacity="0.15" />

            <defs>
                <linearGradient id="gold_silk_grad" x1="0" y1="0" x2="1440" y2="1000" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#fbbf24" stop-opacity="0" />
                    <stop offset="0.5" stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#fbbf24" stop-opacity="0" />
                </linearGradient>
                <radialGradient id="gold_soft_glow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(720 1200) rotate(-90) scale(400 1000)">
                    <stop stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#050505" stop-opacity="0" />
                </radialGradient>
            </defs>
        </svg>
        <div class="absolute top-0 left-0 w-full h-[500px] bg-gradient-to-b from-gold-400/5 to-transparent"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        {{-- SECTION 1: THE MASTERS (ELITE TEAM) --}}
        @if($teamMembers->count() > 0)
        <div class="text-center mb-20" data-aos="fade-up">
            <span class="text-gold-400 font-bold tracking-[0.3em] text-sm uppercase mb-4 block">The Masters</span>
            <h2 class="text-4xl md:text-6xl text-white font-serif tracking-wide uppercase italic">
                OUR <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white">ELITE TEAM</span>
            </h2>
        </div>

        <!-- Team Grid Masters (Desain Persis Yang Kamu Suka) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-40">
            @foreach($teamMembers as $index => $member)
            <div class="group relative h-[600px] overflow-hidden rounded-sm cursor-pointer {{ $index == 1 ? 'md:-mt-8' : '' }}"
                data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                
                <!-- Background Name (Vertical) -->
                <div class="absolute top-0 left-0 h-full w-full flex items-center justify-center z-0 opacity-10 group-hover:opacity-20 transition-opacity duration-500">
                    <span class="text-[120px] font-bold text-white transform -rotate-90 whitespace-nowrap font-serif uppercase tracking-tighter">
                        {{ strtoupper(explode(' ', $member->name)[0]) }}
                    </span>
                </div>

                <!-- Image Handling -->
                @php $photo = $member->image ?? $member->photo; @endphp
                @if($photo)
                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $member->name }}"
                    class="absolute inset-0 w-full h-full object-cover filter grayscale contrast-125 transition-all duration-700 group-hover:grayscale-0 group-hover:scale-110 z-10">
                @else
                <div class="absolute inset-0 w-full h-full bg-neutral-900 flex items-center justify-center z-10">
                    <p class="text-gray-600 text-sm italic font-serif">No Image</p>
                </div>
                @endif

                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-20 transition-opacity duration-500 group-hover:opacity-80"></div>

                <!-- Info Box (Animasinya Persis Yang Kamu Suka) -->
                <div class="absolute bottom-0 left-0 w-full p-8 z-30 transform translate-y-8 group-hover:translate-y-0 transition-transform duration-500">
                    <div class="border-l-4 border-gold-400 pl-4">
                        <h3 class="{{ $index == 1 ? 'text-4xl' : 'text-3xl' }} text-white font-serif italic mb-1">{{ $member->name }}</h3>
                        <p class="text-gold-400 text-xs font-bold tracking-[0.2em] uppercase mb-4">{{ $member->position }}</p>
                        
                        @if($member->bio)
                        <p class="text-gray-400 text-sm font-light opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2 mb-4">
                            {{ $member->bio }}
                        </p>
                        @endif
                        
                        <!-- Social Media Icons (Muncul Saat Hover) -->
                        @php
                            $fb = trim($member->facebook_url ?? '');
                            $ig = trim($member->instagram_url ?? '');
                            $li = trim($member->linkedin_url ?? '');
                        @endphp
                        @if($fb || $ig || $li)
                        <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-200">
                            @if($fb)<a href="{{ $fb }}" target="_blank" class="social-btn"><i class="fab fa-facebook-f text-xs"></i></a>@endif
                            @if($ig)<a href="{{ $ig }}" target="_blank" class="social-btn"><i class="fab fa-instagram text-xs"></i></a>@endif
                            @if($li)<a href="{{ $li }}" target="_blank" class="social-btn"><i class="fab fa-linkedin-in text-xs"></i></a>@endif
                        </div>
                        @endif
                    </div>
                </div>

                @if($index == 1)
                <!-- Gold Frame Effect for Center -->
                <div class="absolute inset-0 border border-gold-400/0 group-hover:border-gold-400/50 transition-colors duration-500 z-30 pointer-events-none"></div>
                @endif
            </div>
            @endforeach
        </div>
        @endif


        {{-- ELEGANT CONNECTOR (Menghubungkan Dua Section) --}}
        <div class="relative flex flex-col items-center mb-32" data-aos="zoom-in">
            <div class="w-px h-32 bg-gradient-to-b from-gold-400 via-gold-400/20 to-transparent"></div>
            <div class="absolute -bottom-4 w-1.5 h-1.5 rounded-full bg-gold-400 shadow-[0_0_15px_#fbbf24]"></div>
        </div>


        {{-- SECTION 2: PRO TEAM (RISING STARS) --}}
        @if($proTeams->count() > 0)
        <div class="relative">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-gold-400 font-bold tracking-[0.3em] text-sm uppercase mb-4 block">Rising Stars</span>
                <h3 class="text-3xl md:text-5xl text-white font-serif tracking-wide uppercase italic">PRO <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white">TEAM</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                @foreach($proTeams as $team)
                <div class="bg-white/[0.03] backdrop-blur-md p-10 rounded-sm border border-white/5 hover:border-gold-400/30 transition-all duration-700 group hover:-translate-y-3"
                    data-aos="fade-up" data-aos-delay="{{ $loop->index * 150 }}">
                    
                    <div class="flex items-start justify-between mb-8">
                        <h4 class="text-2xl text-white font-serif font-bold group-hover:text-gold-400 transition-colors leading-tight">
                            {!! nl2br(e($team->name)) !!}
                        </h4>
                        <div class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center text-neutral-600 group-hover:border-gold-400 group-hover:text-gold-400 transition-all duration-500">
                            <i class="fas fa-user-tie text-base"></i>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-[9px] text-neutral-500 uppercase tracking-widest">Age</span>
                            <span class="text-white text-sm font-mono tracking-tighter">{{ $team->age }} Yrs</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/5 pb-3">
                            <span class="text-[9px] text-neutral-500 uppercase tracking-widest">Origin</span>
                            <span class="text-white text-sm tracking-wide">{{ $team->origin }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="text-[9px] text-neutral-500 uppercase tracking-widest block mb-1">Address</span>
                            <p class="text-neutral-400 text-xs font-light leading-relaxed line-clamp-2">
                                {{ $team->address ?? 'Location info unavailable' }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>
@endif