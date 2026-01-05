{{-- Team Section Component --}}
@php
    // Ambil data tanpa embel-embel select agar aman dari typo kolom
    try {
        $dataMasters = \App\Models\TimKami::where('is_active', 1)->orderBy('order', 'asc')->get();
        $dataProTeams = \App\Models\ProTeam::where('is_active', 1)->orderBy('order', 'asc')->get();
    } catch (\Exception $e) {
        $dataMasters = collect();
        $dataProTeams = collect();
    }
@endphp

<section id="team" class="py-20 bg-[#050505] relative overflow-hidden" style="display: block !important; opacity: 1 !important;">
    
    <!-- BACKGROUND SVG -->
    <div class="absolute inset-0 z-0 pointer-events-none opacity-20">
        <svg width="100%" height="100%" viewBox="0 0 1440 1200" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 200C300 400 600 0 900 200C1200 400 1500 800 1800 700" stroke="#fbbf24" stroke-width="1" fill="none" />
        </svg>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        {{-- HEADER --}}
        <div class="text-center mb-20">
            <span class="text-gold-400 font-bold uppercase tracking-widest text-xs block mb-4">The Masters</span>
            <h2 class="text-5xl md:text-7xl text-white font-serif uppercase italic">OUR <span style="color: #fbbf24;">ELITE TEAM</span></h2>
        </div>

        {{-- ELITE TEAM GRID --}}
        <div class="flex flex-wrap -mx-4 mb-20">
            @forelse($dataMasters as $member)
                <div class="w-full md:w-1/3 px-4 mb-10">
                    {{-- PAKAI STYLE HEIGHT TETAP SUPAYA TIDAK JADI NOL --}}
                    <div class="relative bg-neutral-900 border border-white/10 rounded-sm overflow-hidden" style="height: 550px; display: block !important;">
                        
                        <!-- Gambar (Pengecekan Path Tanpa Storage Link) -->
                        @php 
                            $photo = $member->photo ?? $member->image; 
                            // Jika kamu simpan di public/uploads, ganti 'storage' jadi 'uploads'
                            $imgSrc = asset('storage/' . $photo); 
                        @endphp

                        @if($photo)
                            <img src="{{ $imgSrc }}" alt="{{ $member->name }}" class="absolute inset-0 w-full h-full object-cover z-0">
                        @endif

                        <!-- Overlay agar teks terbaca -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-10"></div>

                        <!-- Data Teks -->
                        <div class="absolute bottom-0 left-0 w-full p-8 z-20">
                            <div class="border-l-4 border-gold-400 pl-4" style="border-left-color: #fbbf24;">
                                <h3 class="text-3xl text-white font-serif italic mb-1">{{ $member->name }}</h3>
                                <p class="text-gold-400 text-[10px] font-black uppercase tracking-widest" style="color: #fbbf24;">{{ $member->position }}</p>
                                
                                <div class="mt-4 flex gap-4 text-white opacity-70">
                                    @if($member->instagram_url) <a href="{{ $member->instagram_url }}"><i class="fab fa-instagram"></i></a> @endif
                                    @if($member->facebook_url) <a href="{{ $member->facebook_url }}"><i class="fab fa-facebook-f"></i></a> @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="w-full text-center text-white/50">Data Elite Team Kosong di Database.</div>
            @endforelse
        </div>

        {{-- SECTION 2: PRO TEAM --}}
        <div class="text-center mb-16">
            <span class="text-gold-400 font-bold uppercase tracking-widest text-xs block mb-4">Rising Stars</span>
            <h3 class="text-4xl md:text-6xl text-white font-serif uppercase italic">PRO <span style="color: #fbbf24;">TEAM</span></h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($dataProTeams as $team)
                <div class="bg-white/[0.03] p-8 border border-white/5 rounded-sm">
                    <h4 class="text-2xl text-white font-serif font-bold mb-4">{{ $team->name }}</h4>
                    <div class="space-y-3 text-xs uppercase tracking-widest text-neutral-500">
                        <div class="flex justify-between border-b border-white/10 pb-2">
                            <span>Age</span> <span class="text-white">{{ $team->age }}</span>
                        </div>
                        <div class="flex justify-between border-b border-white/10 pb-2">
                            <span>Origin</span> <span class="text-white">{{ $team->origin }}</span>
                        </div>
                        <div class="pt-2 normal-case tracking-normal text-neutral-400">
                            {{ $team->address }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FORCE CSS --}}
<style>
    #team .text-gold-400 { color: #fbbf24 !important; }
    #team .border-gold-400 { border-color: #fbbf24 !important; }
</style>