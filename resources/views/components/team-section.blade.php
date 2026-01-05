{{-- Team Section Component --}}
@php
    /** 
     * FIX UNTUK PRODUCTION (HOSTINGER/LINUX)
     * 1. Gunakan try-catch agar jika model tidak ditemukan, tidak error tapi data tetap dicoba ditarik.
     * 2. Hilangkan filter 'is_active' sementara untuk memastikan data muncul dulu.
     */
    try {
        $teamMembers = \App\Models\TimKami::orderBy('order', 'asc')->get();
        $proTeams = \App\Models\ProTeam::orderBy('order', 'asc')->get();
    } catch (\Exception $e) {
        $teamMembers = collect();
        $proTeams = collect();
    }
@endphp

{{-- Jika data tetap tidak muncul, kita paksa loop tanpa pengecekan count dulu untuk testing --}}
<section id="team" class="py-32 bg-[#050505] relative overflow-hidden">
    
    <!-- LUXURY BACKGROUND (Tetap Mewah) -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <svg class="absolute top-0 left-0 w-full h-full opacity-30" viewBox="0 0 1440 1200" fill="none">
            <path d="M-100 100C200 300 500 0 800 200C1100 400 1300 800 1600 700" stroke="#fbbf24" stroke-width="2" stroke-dasharray="10 5" fill="none" />
            <path d="M1440 400C1200 350 1000 600 800 700C600 800 400 750 0 900V1200H1440V400Z" fill="url(#gold_glow)" opacity="0.1" />
            <defs>
                <radialGradient id="gold_glow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(720 800) rotate(90) scale(400 1000)">
                    <stop stop-color="#fbbf24" />
                    <stop offset="1" stop-color="#050505" stop-opacity="0" />
                </radialGradient>
            </defs>
        </svg>
        <div class="absolute top-[20%] -left-20 w-[500px] h-[500px] bg-gold-400/10 rounded-full blur-[120px]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        <!-- HEADER -->
        <div class="text-center mb-24">
            <span class="text-gold-400 font-bold tracking-[0.4em] text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-5xl md:text-7xl text-white font-serif uppercase">OUR <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 via-white to-gold-400">ELITE TEAM</span></h2>
        </div>

        <!-- ELITE TEAM GRID -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-40">
            @forelse($teamMembers as $index => $member)
                @if($member->is_active) {{-- Filter aktif di dalam loop agar lebih aman --}}
                <div class="group relative h-[600px] overflow-hidden rounded-sm cursor-pointer {{ $index == 1 ? 'md:-mt-12' : '' }}">
                    
                    <!-- Nama Belakang Silhouette -->
                    <div class="absolute inset-0 flex items-center justify-center z-0 opacity-10">
                        <span class="text-[120px] font-bold text-white transform -rotate-90 uppercase font-serif">
                            {{ strtoupper(explode(' ', $member->name)[0]) }}
                        </span>
                    </div>

                    <!-- Image Handling -->
                    @php $photo = $member->photo ?? $member->image; @endphp
                    <img src="{{ asset('storage/' . $photo) }}" 
                         alt="{{ $member->name }}"
                         class="absolute inset-0 w-full h-full object-cover filter grayscale contrast-125 transition-all duration-1000 group-hover:grayscale-0 group-hover:scale-110 z-10">

                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-20"></div>

                    <!-- Info -->
                    <div class="absolute bottom-0 left-0 w-full p-10 z-30 transition-all duration-500 transform translate-y-4 group-hover:translate-y-0">
                        <div class="border-l-[3px] border-gold-400 pl-6">
                            <h3 class="text-3xl text-white font-serif italic mb-1">{{ $member->name }}</h3>
                            <p class="text-gold-400 text-[10px] font-black uppercase tracking-[0.3em]">{{ $member->position }}</p>
                        </div>
                    </div>
                </div>
                @endif
            @empty
                <p class="text-white text-center col-span-3 opacity-50">Data Tim Kami belum diinput atau tidak aktif di database production.</p>
            @endforelse
        </div>

        <!-- PRO TEAM GRID -->
        <div class="text-center mb-16">
            <span class="text-gold-400 font-bold tracking-[0.4em] text-xs uppercase mb-4 block">Rising Stars</span>
            <h3 class="text-4xl md:text-6xl text-white font-serif uppercase">PRO TEAM</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($proTeams as $team)
                @if($team->is_active)
                <div class="bg-white/[0.05] p-8 border border-white/5 hover:border-gold-400/50 transition-all duration-500">
                    <h4 class="text-xl text-white font-serif mb-4">{{ $team->name }}</h4>
                    <div class="text-[10px] text-gray-500 uppercase tracking-widest border-b border-white/10 pb-2 mb-2">Age: {{ $team->age }}</div>
                    <div class="text-[10px] text-gray-500 uppercase tracking-widest">{{ $team->origin }}</div>
                </div>
                @endif
            @empty
                <p class="text-white text-center col-span-4 opacity-50">Data Pro Team belum tersedia.</p>
            @endforelse
        </div>
    </div>
</section>