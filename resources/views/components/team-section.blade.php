{{-- Team Section Component --}}
@php
    try {
        $teamMembers = \App\Models\TimKami::where('is_active', 1)->orderBy('order', 'asc')->get();
        $proTeams = \App\Models\ProTeam::where('is_active', 1)->orderBy('order', 'asc')->get();
    } catch (\Exception $e) {
        $teamMembers = collect();
        $proTeams = collect();
    }
@endphp

@if($teamMembers->count() > 0 || $proTeams->count() > 0)
<section id="team" class="py-20 lg:py-32 bg-[#050505] relative overflow-hidden selection:bg-gold-400 selection:text-black">
    
    <!-- LUXURY FLOW BACKGROUND (Silk & Energy Flow) -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <svg class="absolute top-0 left-0 w-full h-full opacity-30" viewBox="0 0 1440 1600" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Flowing Path from Master to Pro -->
            <path d="M-100 300C200 500 400 100 800 400C1200 700 1100 1000 1500 1100" stroke="url(#silk_grad)" stroke-width="1.5" stroke-dasharray="20 15" class="animate-silk-flow" />
            <path d="M1500 400C1200 500 900 300 600 800C300 1300 0 1100 -200 1400" stroke="url(#silk_grad)" stroke-width="1" opacity="0.3" />
            
            <defs>
                <linearGradient id="silk_grad" x1="0" y1="0" x2="1440" y2="1600" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#fbbf24" stop-opacity="0" />
                    <stop offset="0.5" stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#fbbf24" stop-opacity="0" />
                </linearGradient>
            </defs>
        </svg>
        <!-- Soft Ambient Atmosphere -->
        <div class="absolute top-1/4 -left-20 w-[600px] h-[600px] bg-gold-400/5 rounded-full blur-[160px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-[600px] h-[600px] bg-gold-400/5 rounded-full blur-[160px]"></div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 relative z-10">
        
        {{-- SECTION 1: THE MASTERS --}}
        <div class="text-center mb-16 lg:mb-24 animate-reveal">
            <span class="text-gold-400 font-bold tracking-[0.5em] text-[10px] md:text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-4xl md:text-6xl lg:text-7xl text-white font-serif tracking-tight uppercase italic leading-none">
                OUR <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 via-white to-gold-400" style="color: #fbbf24;">ELITE TEAM</span>
            </h2>
        </div>

        {{-- ELITE GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-10 mb-32 lg:mb-48">
            @foreach($teamMembers as $index => $member)
            <div class="group relative aspect-[3/4] md:h-[650px] overflow-hidden rounded-sm bg-neutral-950 border border-white/5 transition-all duration-700 hover:border-gold-400/40 hover:shadow-[0_0_40px_rgba(251,191,36,0.1)] animate-reveal"
                 style="animation-delay: {{ ($index + 1) * 200 }}ms;">
                
                <!-- Silhouette Background Name -->
                <div class="absolute inset-0 flex items-center justify-center z-0 opacity-[0.05] group-hover:opacity-10 transition-opacity duration-1000">
                    <span class="text-[100px] lg:text-[150px] font-black text-white transform -rotate-90 whitespace-nowrap select-none uppercase font-serif tracking-tighter">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </span>
                </div>

                <!-- Profile Image -->
                @php $photo = $member->photo ?? $member->image; @endphp
                @if($photo)
                <img src="{{ asset('storage/' . $photo) }}" alt="{{ $member->name }}"
                    class="absolute inset-0 w-full h-full object-cover filter grayscale contrast-[1.1] brightness-75 transition-all duration-1000 group-hover:grayscale-0 group-hover:scale-110 group-hover:brightness-100 z-10">
                @else
                <div class="absolute inset-0 w-full h-full bg-neutral-900 flex items-center justify-center z-10">
                    <i class="fas fa-user-circle text-8xl text-white/5"></i>
                </div>
                @endif

                <!-- Inner Luxury Glow on Hover -->
                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 z-15 shadow-[inset_0_0_100px_rgba(251,191,36,0.2)]"></div>

                <!-- Luxury Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-20"></div>

                <!-- INFO BOX (Handling Overflow) -->
                <div class="absolute bottom-0 left-0 w-full p-6 lg:p-10 z-30 transition-all duration-700 translate-y-4 group-hover:translate-y-0">
                    <div class="border-l-[3px] border-gold-400/30 group-hover:border-gold-400 pl-5 transition-colors duration-500">
                        <h3 class="text-2xl lg:text-4xl text-white font-serif italic mb-1 lg:mb-2 tracking-wide">{{ $member->name }}</h3>
                        <p class="text-gold-400 text-[10px] font-black uppercase tracking-[0.3em] mb-4">{{ $member->position }}</p>
                        
                        {{-- TEXT OVERFLOW AREA --}}
                        <div class="max-h-0 group-hover:max-h-40 transition-all duration-700 ease-in-out overflow-hidden relative">
                            @if($member->bio)
                            <div class="text-gray-300 text-xs lg:text-sm font-light leading-relaxed pr-2 overflow-y-auto custom-scrollbar h-full">
                                {{ $member->bio }}
                                <div class="h-4 w-full"></div> {{-- Spacer for scroll --}}
                            </div>
                            {{-- Fade effect for long text --}}
                            <div class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-black to-transparent pointer-events-none"></div>
                            @endif
                        </div>
                        
                        <!-- Socials -->
                        <div class="flex items-center gap-4 mt-6 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-700 delay-200">
                            @if($member->facebook_url)<a href="{{ $member->facebook_url }}" target="_blank" class="social-btn"><i class="fab fa-facebook-f"></i></a>@endif
                            @if($member->instagram_url)<a href="{{ $member->instagram_url }}" target="_blank" class="social-btn"><i class="fab fa-instagram"></i></a>@endif
                            @if($member->linkedin_url)<a href="{{ $member->linkedin_url }}" target="_blank" class="social-btn"><i class="fab fa-linkedin-in"></i></a>@endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>


        {{-- ELEGANT FLOW CONNECTOR --}}
        <div class="flex flex-col items-center mb-24 lg:mb-32">
            <div class="w-px h-32 bg-gradient-to-b from-gold-400 via-gold-400/20 to-transparent"></div>
            <div class="mt-4 text-gold-400 text-[8px] uppercase tracking-[1em] opacity-50">Rising stars</div>
        </div>


        {{-- SECTION 2: PRO TEAM --}}
        <div class="text-center mb-16 lg:mb-20">
            <h3 class="text-3xl md:text-5xl lg:text-6xl text-white font-serif tracking-widest uppercase italic leading-none">
                PRO <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white" style="color: #fbbf24;">TEAM</span>
            </h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">
            @foreach($proTeams as $team)
            <div class="bg-gradient-to-br from-white/[0.04] to-transparent backdrop-blur-md p-8 lg:p-10 rounded-sm border border-white/5 hover:border-gold-400/40 transition-all duration-700 group hover:-translate-y-3 relative overflow-hidden animate-reveal"
                style="animation-delay: {{ $loop->index * 150 }}ms;">
                
                <div class="flex items-start justify-between mb-8 relative z-10">
                    <h4 class="text-xl lg:text-2xl text-white font-serif font-bold group-hover:text-gold-400 transition-colors leading-tight">
                        {!! nl2br(e($team->name)) !!}
                    </h4>
                    <i class="fas fa-user-tie text-neutral-700 group-hover:text-gold-400 group-hover:rotate-12 transition-all duration-500"></i>
                </div>

                <div class="space-y-4 relative z-10">
                    <div class="flex items-center justify-between border-b border-white/5 pb-2">
                        <span class="text-[9px] text-neutral-500 uppercase tracking-widest">Age</span>
                        <span class="text-white text-sm font-mono">{{ $team->age }} Yrs</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-white/5 pb-2">
                        <span class="text-[9px] text-neutral-500 uppercase tracking-widest">Origin</span>
                        <span class="text-white text-sm">{{ $team->origin }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="text-[9px] text-neutral-500 uppercase tracking-widest block mb-1">Address</span>
                        <p class="text-neutral-400 text-[11px] font-light leading-relaxed line-clamp-2 italic">
                            {{ $team->address ?? 'Location info unavailable' }}
                        </p>
                    </div>
                </div>

                <!-- Hover Decoration -->
                <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-gold-400/5 rounded-full blur-2xl group-hover:bg-gold-400/10 transition-all duration-700"></div>
            </div>
            @endforeach
        </div>

    </div>
</section>

<style>
    /* TYPOGRAPHY */
    #team { font-family: 'Playfair Display', serif; }

    /* CUSTOM SOCIAL BUTTON */
    .social-btn {
        @apply w-9 h-9 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black hover:shadow-[0_0_20px_rgba(251,191,36,0.4)] transition-all duration-500;
    }

    /* CUSTOM SCROLLBAR FOR TEXT OVERFLOW */
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fbbf24; border-radius: 10px; }

    /* ANIMATIONS */
    @keyframes reveal {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-reveal {
        animation: reveal 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        opacity: 0;
    }

    @keyframes silk-flow {
        from { stroke-dashoffset: 1000; }
        to { stroke-dashoffset: 0; }
    }
    .animate-silk-flow {
        stroke-dasharray: 500;
        animation: silk-flow 20s linear infinite;
    }

    /* PREVENT LAYOUT SHIFT ON MOBILE */
    @media (max-width: 768px) {
        .animate-reveal { animation-duration: 0.8s; }
    }
</style>
@endif