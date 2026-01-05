{{-- Team Section Component --}}
@php
    // LOGIKA PEMANGGILAN DATA (Pakai code kamu yang sudah pasti jalan)
    $teamMembers = $teamMembers ?? cache()->remember('component_team', 1800, function () {
        return \App\Models\TimKami::select('id', 'title', 'subtitle', 'name', 'position', 'bio', 'photo', 'image', 'facebook_url', 'instagram_url', 'linkedin_url', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    });

    // Pemanggilan Pro Team (Direct agar admin cepat sinkron)
    $proTeams = \App\Models\ProTeam::where('is_active', true)->orderBy('order', 'asc')->get();
@endphp

@if($teamMembers->count() > 0 || $proTeams->count() > 0)
<section id="team" class="py-32 bg-[#050505] relative overflow-hidden">
    
    <!-- LUXURY FLOW BACKGROUND ELEMENTS -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <svg class="absolute top-0 left-0 w-full h-full opacity-20" viewBox="0 0 1440 1200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-100 200C200 400 600 -100 900 300C1200 700 1300 900 1600 800" stroke="url(#gold_line_grad)" stroke-width="1.5" stroke-dasharray="15 10" />
            <path d="M-50 250C250 450 650 -50 950 350C1250 750 1350 950 1650 850" stroke="url(#gold_line_grad)" stroke-width="0.5" opacity="0.4" />
            <path d="M0 1000C300 950 600 1100 900 950C1200 800 1440 850 1440 850V1200H0V1000Z" fill="url(#gold_bottom_glow)" opacity="0.15" />
            <defs>
                <linearGradient id="gold_line_grad" x1="0" y1="0" x2="1440" y2="1000" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#fbbf24" stop-opacity="0" />
                    <stop offset="0.5" stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#fbbf24" stop-opacity="0" />
                </linearGradient>
                <radialGradient id="gold_bottom_glow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(720 1200) rotate(-90) scale(400 1000)">
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
        <div class="text-center mb-24" data-aos="fade-up" data-aos-duration="1000">
            <span class="text-gold-400 font-bold tracking-[0.5em] text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-5xl md:text-7xl text-white font-rumonds tracking-tight uppercase">
                OUR <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 via-white to-gold-400">ELITE TEAM</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-40">
            @foreach($teamMembers as $index => $member)
            <div class="group relative h-[650px] overflow-hidden rounded-sm cursor-pointer {{ $index == 1 ? 'md:-mt-12' : '' }}"
                data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                
                <!-- Silhouette Background Name -->
                <div class="absolute inset-0 flex items-center justify-center z-0 opacity-[0.07] group-hover:opacity-15 transition-opacity duration-700">
                    <span class="text-[140px] font-black text-white transform -rotate-90 whitespace-nowrap select-none uppercase font-rumonds tracking-tighter">
                        {{ strtoupper(explode(' ', $member->name)[0]) }}
                    </span>
                </div>

                <!-- Profile Image (Logika path dari code kamu) -->
                @php $photoPath = $member->image ? $member->image : $member->photo; @endphp
                @if($photoPath)
                <img src="{{ asset('storage/' . $photoPath) }}" alt="{{ $member->name }}"
                    class="absolute inset-0 w-full h-full object-cover filter grayscale contrast-[1.1] transition-all duration-1000 group-hover:grayscale-0 group-hover:scale-110 z-10">
                @else
                <div class="absolute inset-0 w-full h-full bg-neutral-900 flex items-center justify-center z-10">
                    <i class="fas fa-user text-neutral-800 text-6xl"></i>
                </div>
                @endif

                <!-- Luxury Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/30 to-transparent z-20 transition-opacity duration-700 group-hover:opacity-90"></div>

                <!-- Info Box -->
                <div class="absolute bottom-0 left-0 w-full p-10 z-30 transform translate-y-6 group-hover:translate-y-0 transition-transform duration-700">
                    <div class="border-l-[3px] border-gold-400 pl-6">
                        <h3 class="{{ $index == 1 ? 'text-4xl' : 'text-3xl' }} text-white font-serif italic mb-2 tracking-wide leading-tight">{{ $member->name }}</h3>
                        <p class="text-gold-400 text-[10px] font-black uppercase tracking-[0.3em] mb-5">{{ $member->position }}</p>
                        
                        @if($member->bio)
                        <p class="text-gray-300 text-sm font-light opacity-0 group-hover:opacity-100 transition-all duration-500 delay-100 line-clamp-3 mb-6 leading-relaxed">
                            {{ $member->bio }}
                        </p>
                        @endif
                        
                        <!-- Socials (Logika trim dari code kamu) -->
                        @php
                            $fb = $member->facebook_url && trim($member->facebook_url) !== '' ? trim($member->facebook_url) : '';
                            $ig = $member->instagram_url && trim($member->instagram_url) !== '' ? trim($member->instagram_url) : '';
                            $li = $member->linkedin_url && trim($member->linkedin_url) !== '' ? trim($member->linkedin_url) : '';
                        @endphp
                        @if($fb || $ig || $li)
                        <div class="flex items-center gap-4 opacity-0 group-hover:opacity-100 transition-all duration-700 delay-200">
                            @if($fb)<a href="{{ $fb }}" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-facebook-f text-xs"></i></a>@endif
                            @if($ig)<a href="{{ $ig }}" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-instagram text-xs"></i></a>@endif
                            @if($li)<a href="{{ $li }}" target="_blank" rel="noopener" class="social-btn"><i class="fab fa-linkedin-in text-xs"></i></a>@endif
                        </div>
                        @endif
                    </div>
                </div>

                @if($index == 1) 
                    <div class="absolute inset-0 border border-gold-400/0 group-hover:border-gold-400/30 transition-all duration-700 z-30 pointer-events-none"></div> 
                @endif
            </div>
            @endforeach
        </div>
        @endif


        {{-- ELEGANT CONNECTOR --}}
        <div class="relative flex flex-col items-center mb-32" data-aos="zoom-in">
            <div class="w-px h-32 bg-gradient-to-b from-gold-400 via-gold-400/20 to-transparent"></div>
            <div class="absolute -bottom-4 w-1.5 h-1.5 rounded-full bg-gold-400 shadow-[0_0_15px_#fbbf24]"></div>
        </div>


        {{-- SECTION 2: PRO TEAM (RISING STARS) --}}
        @if($proTeams->count() > 0)
        <div class="relative">
            <div class="text-center mb-20" data-aos="fade-up">
                <span class="text-gold-400 font-bold tracking-[0.5em] text-xs uppercase mb-4 block">Rising Stars</span>
                <h3 class="text-4xl md:text-6xl text-white font-rumonds tracking-wide uppercase">PRO <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white">TEAM</span>
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
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
                            <span class="text-white text-sm">{{ $team->origin }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="text-[9px] text-neutral-500 uppercase tracking-widest block mb-1">Address</span>
                            <p class="text-neutral-400 text-xs font-light leading-relaxed line-clamp-2 italic">
                                {{ $team->address }}
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
    .social-btn {
        @apply w-9 h-9 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black hover:shadow-[0_0_20px_rgba(251,191,36,0.4)] transition-all duration-500;
    }
</style>
@endif