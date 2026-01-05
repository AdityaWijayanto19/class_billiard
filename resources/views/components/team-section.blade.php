{{-- Team Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $teamMembers = $teamMembers ?? cache()->remember('component_team', 1800, function () {
        return \App\Models\TimKami::select('id', 'title', 'subtitle', 'name', 'position', 'bio', 'photo', 'image', 'facebook_url', 'instagram_url', 'linkedin_url', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    });

    // Ensure $proTeams is always defined as a collection
    $proTeams = $proTeams ?? collect();
@endphp

@if($teamMembers->count() > 0 || $proTeams->count() > 0)
<section id="team" class="py-32 bg-[#050505] relative overflow-hidden">
    
    <!-- LUXURY FLOW BACKGROUND ELEMENTS -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <!-- Main Elegant Curve Top to Middle -->
        <svg class="absolute top-0 left-0 w-full h-full opacity-30" viewBox="0 0 1440 1200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-100 100C200 300 500 0 800 200C1100 400 1300 800 1600 700" stroke="url(#gold_gradient_1)" stroke-width="2" stroke-dasharray="10 5" />
            <path d="M-50 150C250 350 550 50 850 250C1150 450 1350 850 1650 750" stroke="url(#gold_gradient_1)" stroke-width="1" opacity="0.5" />
            
            <!-- Flowing Silhouette Shape -->
            <path d="M1440 400C1200 350 1000 600 800 700C600 800 400 750 0 900V1200H1440V400Z" fill="url(#gold_soft_glow)" opacity="0.1" />
            
            <defs>
                <linearGradient id="gold_gradient_1" x1="0" y1="0" x2="1440" y2="1200" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#fbbf24" stop-opacity="0" />
                    <stop offset="0.5" stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#fbbf24" stop-opacity="0" />
                </linearGradient>
                <radialGradient id="gold_soft_glow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(720 800) rotate(90) scale(400 1000)">
                    <stop stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#050505" stop-opacity="0" />
                </radialGradient>
            </defs>
        </svg>

        <!-- Dynamic Glow Spots -->
        <div class="absolute top-[20%] -left-20 w-[500px] h-[500px] bg-gold-400/10 rounded-full blur-[120px]"></div>
        <div class="absolute top-[50%] -right-20 w-[600px] h-[600px] bg-gold-400/5 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-0 left-1/2 -translate-x-1/2 w-full h-[300px] bg-gradient-to-t from-gold-400/10 to-transparent"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        {{-- PART 1: THE MASTERS (ELITE TEAM) --}}
        @if($teamMembers->count() > 0)
        <div class="text-center mb-24" data-aos="fade-up">
            <span class="text-gold-400 font-bold tracking-[0.4em] text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-5xl md:text-7xl text-white font-rumonds tracking-wide">OUR <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 via-white to-gold-400">ELITE TEAM</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-40">
            @foreach($teamMembers as $index => $member)
            <div class="group relative h-[650px] overflow-hidden rounded-sm cursor-pointer {{ $index == 1 ? 'md:-mt-12' : '' }}"
                data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                
                <!-- Background Name (Vertical Silhouette) -->
                <div class="absolute top-0 left-0 h-full w-full flex items-center justify-center z-0 opacity-10 group-hover:opacity-25 transition-all duration-700">
                    <span class="text-[140px] font-bold text-white transform -rotate-90 whitespace-nowrap font-rumonds select-none uppercase tracking-tighter">
                        {{ strtoupper(explode(' ', $member->name)[0]) }}
                    </span>
                </div>

                <!-- Image Card Design (Tetap Sesuai Permintaan) -->
                @php $photo = $member->image ?? $member->photo; @endphp
                @if($photo)
                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $member->name }}"
                    class="absolute inset-0 w-full h-full object-cover filter grayscale contrast-125 transition-all duration-1000 group-hover:grayscale-0 group-hover:scale-110 z-10">
                @else
                <div class="absolute inset-0 w-full h-full bg-neutral-900 flex items-center justify-center z-10">
                    <i class="fas fa-user text-neutral-800 text-6xl"></i>
                </div>
                @endif

                <!-- Overlays -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-20 transition-opacity duration-500 group-hover:opacity-80"></div>

                <!-- Info Box -->
                <div class="absolute bottom-0 left-0 w-full p-10 z-30 transform translate-y-6 group-hover:translate-y-0 transition-transform duration-700">
                    <div class="border-l-[3px] border-gold-400 pl-6">
                        <h3 class="{{ $index == 1 ? 'text-4xl' : 'text-3xl' }} text-white font-serif italic mb-2 tracking-wide">{{ $member->name }}</h3>
                        <p class="text-gold-400 text-[10px] font-black uppercase tracking-[0.3em] mb-5">{{ $member->position }}</p>
                        
                        @if($member->bio)
                        <p class="text-gray-300 text-sm font-light opacity-0 group-hover:opacity-100 transition-all duration-500 delay-100 line-clamp-3 mb-6 leading-relaxed">
                            {{ $member->bio }}
                        </p>
                        @endif
                        
                        <!-- Social Icons -->
                        <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-all duration-700 delay-200">
                            @if($member->facebook_url)<a href="{{ $member->facebook_url }}" class="social-btn"><i class="fab fa-facebook-f text-xs"></i></a>@endif
                            @if($member->instagram_url)<a href="{{ $member->instagram_url }}" class="social-btn"><i class="fab fa-instagram text-xs"></i></a>@endif
                            @if($member->linkedin_url)<a href="{{ $member->linkedin_url }}" class="social-btn"><i class="fab fa-linkedin-in text-xs"></i></a>@endif
                        </div>
                    </div>
                </div>
                @if($index == 1) 
                    <div class="absolute inset-0 border border-gold-400/0 group-hover:border-gold-400/40 transition-all duration-700 z-30 pointer-events-none"></div> 
                @endif
            </div>
            @endforeach
        </div>
        @endif


        {{-- ELEGANT FLOW CONNECTOR --}}
        <div class="relative flex flex-col items-center mb-32" data-aos="fade-up">
            <div class="w-px h-32 bg-gradient-to-b from-gold-400 via-gold-400/50 to-transparent"></div>
            <div class="absolute -bottom-4 w-2 h-2 rounded-full bg-gold-400 shadow-[0_0_15px_#fbbf24]"></div>
        </div>


        {{-- PART 2: PRO TEAM (RISING STARS) --}}
        @if($proTeams->count() > 0)
        <div class="relative" data-aos="fade-up">
            <div class="text-center mb-20">
                <span class="text-gold-400 font-bold tracking-[0.4em] text-xs uppercase mb-4 block">Rising Stars</span>
                <h3 class="text-4xl md:text-6xl text-white font-rumonds tracking-wide uppercase">PRO <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white">TEAM</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($proTeams as $team)
                <div class="bg-gradient-to-b from-white/[0.05] to-transparent p-10 rounded-sm border border-white/5 hover:border-gold-400/40 transition-all duration-700 group hover:-translate-y-3 backdrop-blur-sm">
                    <div class="flex items-start justify-between mb-8">
                        <h4 class="text-2xl text-white font-serif font-bold group-hover:text-gold-400 transition-colors leading-tight">
                            {!! nl2br(e($team->name)) !!}
                        </h4>
                        <div class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center text-neutral-600 group-hover:border-gold-400 group-hover:text-gold-400 group-hover:rotate-[360deg] transition-all duration-1000">
                            <i class="fas fa-user-tie text-lg"></i>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-[10px] text-neutral-500 uppercase tracking-[0.2em]">Age</span>
                            <span class="text-white text-sm font-mono tracking-tighter">{{ $team->age }} Yrs</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-white/10 pb-3">
                            <span class="text-[10px] text-neutral-500 uppercase tracking-[0.2em]">Origin</span>
                            <span class="text-white text-sm tracking-wide">{{ $team->origin }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="text-[10px] text-neutral-500 uppercase tracking-[0.2em] block mb-2">Location</span>
                            <p class="text-neutral-400 text-xs font-light leading-relaxed line-clamp-2 italic">
                                "{{ $team->address }}"
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

<style>
    /* Custom Social Button Style */
    .social-btn {
        @apply w-9 h-9 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black hover:shadow-[0_0_15px_rgba(251,191,36,0.5)] transition-all duration-500;
    }

    /* Additional Typography Enhancements */
    #team {
        --gold-primary: #fbbf24;
    }

    .font-rumonds {
        /* Ganti dengan font serif/display mewah Anda jika tersedia */
        letter-spacing: 0.05em;
    }

    /* Smooth Image Grayscale Transition */
    img {
        will-change: transform, filter;
    }
</style>
@endif