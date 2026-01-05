{{-- Team Section Component --}}
@php
    /** 
     * KODE ANTI-GHAIB PRODUCTION
     * Ambil data tanpa filter awal untuk memastikan data ketarik.
     */
    try {
        $teamMembers = \App\Models\TimKami::orderBy('order', 'asc')->get();
        $proTeams = \App\Models\ProTeam::orderBy('order', 'asc')->get();
    } catch (\Exception $e) {
        $teamMembers = collect();
        $proTeams = collect();
    }
@endphp

{{-- BAR DEBUG VISUAL (Hapus jika sudah muncul) --}}
<div class="bg-red-600 text-white text-[10px] py-1 text-center relative z-[9999]">
    LIVE DEBUG: Elite({{ $teamMembers->count() }}) | Pro({{ $proTeams->count() }}) | 
    Status IsActive Elite 1: {{ $teamMembers->first()->is_active ?? 'N/A' }}
</div>

<section id="team" class="py-24 bg-[#050505] relative overflow-hidden" style="min-height: 500px;">
    
    <!-- BACKGROUND (Opacity dibuat sangat tipis agar tidak menindih) -->
    <div class="absolute inset-0 z-0 pointer-events-none opacity-10">
        <svg class="w-full h-full" viewBox="0 0 1440 1200" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M-100 200C200 400 600 -100 900 300C1200 700 1300 900 1600 800" stroke="#fbbf24" stroke-width="1" />
        </svg>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        {{-- SECTION 1: THE MASTERS --}}
        <div class="text-center mb-20">
            <span class="text-gold-400 font-bold tracking-[0.4em] text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-4xl md:text-6xl text-white font-serif uppercase italic">
                OUR <span class="text-gold-400">ELITE TEAM</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-32">
            @forelse($teamMembers as $index => $member)
                {{-- Kita paksa muncul tanpa cek is_active dulu untuk testing --}}
                <div class="relative h-[600px] w-full bg-neutral-900 border-2 border-gold-400/20 rounded-lg overflow-hidden flex flex-col justify-end p-8 {{ $index == 1 ? 'md:-mt-8' : '' }}">
                    
                    <!-- Nama Belakang (Silhouette) -->
                    <div class="absolute inset-0 flex items-center justify-center z-0 opacity-10">
                        <span class="text-8xl font-black text-white transform -rotate-90 uppercase">
                            {{ substr($member->name, 0, 1) }}
                        </span>
                    </div>

                    <!-- Image Handling -->
                    @php $photo = $member->photo ?? $member->image; @endphp
                    @if($photo)
                        <img src="{{ asset('storage/' . $photo) }}" 
                             alt="{{ $member->name }}"
                             class="absolute inset-0 w-full h-full object-cover z-10 grayscale">
                    @endif

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-20"></div>

                    <!-- Info (Z-30 agar di atas gambar) -->
                    <div class="relative z-30">
                        <div class="border-l-4 border-gold-400 pl-4">
                            <h3 class="text-3xl text-white font-serif italic mb-1">{{ $member->name }}</h3>
                            <p class="text-gold-400 text-[10px] font-black uppercase tracking-[0.3em] mb-4">{{ $member->position }}</p>
                            
                            <!-- Sosmed Sederhana -->
                            <div class="flex gap-4">
                                @if($member->instagram_url)<a href="{{ $member->instagram_url }}" class="text-white hover:text-gold-400"><i class="fab fa-instagram"></i></a>@endif
                                @if($member->facebook_url)<a href="{{ $member->facebook_url }}" class="text-white hover:text-gold-400"><i class="fab fa-facebook-f"></i></a>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-white opacity-50">Data Elite Team tidak terbaca di database.</div>
            @endforelse
        </div>

        {{-- SECTION 2: PRO TEAM --}}
        <div class="text-center mb-16">
            <span class="text-gold-400 font-bold tracking-[0.5em] text-xs uppercase mb-4 block">Rising Stars</span>
            <h3 class="text-3xl md:text-5xl text-white font-serif uppercase italic tracking-widest">PRO TEAM</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($proTeams as $team)
                <div class="bg-neutral-900/50 p-8 border border-gold-400/20 rounded-lg group">
                    <h4 class="text-xl text-white font-serif font-bold group-hover:text-gold-400 transition-colors mb-4">{{ $team->name }}</h4>
                    <div class="space-y-2 text-xs text-neutral-400 uppercase tracking-widest">
                        <div class="flex justify-between border-b border-white/5 pb-2">
                            <span>Age</span> <span class="text-white font-mono">{{ $team->age }}</span>
                        </div>
                        <div class="flex justify-between border-b border-white/5 pb-2">
                            <span>Origin</span> <span class="text-white">{{ $team->origin }}</span>
                        </div>
                        <div class="pt-2 normal-case tracking-normal text-neutral-500 italic">
                            {{ $team->address }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-4 text-center text-white opacity-50">Data Pro Team tidak terbaca.</div>
            @endforelse
        </div>

    </div>
</section>