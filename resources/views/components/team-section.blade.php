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




<section id="team" class="py-24 bg-[#050505] relative overflow-hidden" style="min-height: 500px;">
    <!-- BACKGROUND: Gradient + SVG Wave for smooth transition -->
    <div class="absolute inset-0 z-0 pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-b from-[#050505] via-[#18120a] to-[#181818] opacity-90"></div>
        <svg class="w-full h-32 md:h-48 absolute top-0 left-0" viewBox="0 0 1440 320" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#fbbf24" fill-opacity="0.08" d="M0,160L60,154.7C120,149,240,139,360,154.7C480,171,600,213,720,197.3C840,181,960,107,1080,101.3C1200,96,1320,160,1380,192L1440,224L1440,0L1380,0C1320,0,1200,0,1080,0C960,0,840,0,720,0C600,0,480,0,360,0C240,0,120,0,60,0L0,0Z"></path>
        </svg>
    </div>


    <div class="container mx-auto px-2 sm:px-4 md:px-6 relative z-10">
        {{-- SECTION 1: THE MASTERS --}}
        <div class="text-center mb-20" data-aos="fade-down" data-aos-duration="900">
            <span class="text-gold-400 font-bold tracking-[0.4em] text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-4xl md:text-6xl text-white font-serif uppercase italic">
                OUR <span class="text-gold-400">ELITE TEAM</span>
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 md:gap-10 mb-32">
            @forelse($teamMembers as $index => $member)
                <div class="relative min-h-[420px] max-h-[650px] w-full bg-neutral-900 border-2 border-gold-400/20 rounded-lg overflow-hidden flex flex-col justify-end p-6 md:p-8 shadow-xl hover:scale-[1.03] transition-transform duration-300 group {{ $index == 1 ? 'md:-mt-8' : '' }}"
                    data-aos="fade-up" data-aos-delay="{{ 100 + $index*100 }}" data-aos-duration="900">
                    <!-- Nama Belakang (Silhouette) -->
                    <div class="absolute inset-0 flex items-center justify-center z-0 opacity-10 pointer-events-none">
                        <span class="text-8xl md:text-9xl font-black text-white transform -rotate-90 uppercase select-none">
                            {{ substr($member->name, 0, 1) }}
                        </span>
                    </div>

                    <!-- Image Handling -->
                    @php $photo = $member->photo ?? $member->image; @endphp
                    @if($photo)
                        <img src="{{ asset('storage/' . $photo) }}" 
                             alt="{{ $member->name }}"
                             class="absolute inset-0 w-full h-full object-cover z-10 grayscale group-hover:grayscale-0 transition duration-500">
                    @endif

                    <!-- Gradient Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent z-20"></div>

                    <!-- Info (Z-30 agar di atas gambar) -->
                    <div class="relative z-30 max-h-[320px] overflow-y-auto scrollbar-thin scrollbar-thumb-gold-400/30 scrollbar-track-transparent pr-2">
                        <div class="border-l-4 border-gold-400 pl-4">
                            <h3 class="text-2xl md:text-3xl text-white font-serif italic mb-1">{{ $member->name }}</h3>
                            <p class="text-gold-400 text-[10px] font-black uppercase tracking-[0.3em] mb-2">{{ $member->position }}</p>
                            @if($member->bio)
                                <p class="text-neutral-300 text-xs mb-2 line-clamp-3 md:line-clamp-4">{{ $member->bio }}</p>
                            @endif
                            @if($member->achievement)
                                <div class="text-gold-400 text-xs mb-2"><span class="font-bold">Prestasi:</span> {{ $member->achievement }}</div>
                            @endif
                            @if($member->experience)
                                <div class="text-neutral-400 text-xs mb-2"><span class="font-bold">Pengalaman:</span> {{ $member->experience }}</div>
                            @endif
                            <!-- Sosmed Sederhana -->
                            <div class="flex gap-4 mt-2">
                                @if($member->instagram_url)<a href="{{ $member->instagram_url }}" class="text-white hover:text-gold-400" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>@endif
                                @if($member->facebook_url)<a href="{{ $member->facebook_url }}" class="text-white hover:text-gold-400" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>@endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-white opacity-50">Data Elite Team tidak terbaca di database.</div>
            @endforelse
        </div>


        {{-- SECTION 2: PRO TEAM --}}
        <div class="text-center mb-16" data-aos="fade-down" data-aos-delay="200" data-aos-duration="900">
            <span class="text-gold-400 font-bold tracking-[0.5em] text-xs uppercase mb-4 block">Rising Stars</span>
            <h3 class="text-3xl md:text-5xl text-white font-serif uppercase italic tracking-widest">PRO TEAM</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            @forelse($proTeams as $idx => $team)
                <div class="bg-neutral-900/70 p-6 md:p-8 border border-gold-400/20 rounded-lg group shadow-lg hover:scale-[1.03] transition-transform duration-300 flex flex-col justify-between min-h-[320px] max-h-[480px] overflow-hidden"
                    data-aos="fade-up" data-aos-delay="{{ 100 + $idx*80 }}" data-aos-duration="900">
                    <div>
                        <h4 class="text-lg md:text-xl text-white font-serif font-bold group-hover:text-gold-400 transition-colors mb-2 md:mb-4">{{ $team->name }}</h4>
                        <div class="space-y-1 text-xs text-neutral-400 uppercase tracking-widest">
                            <div class="flex justify-between border-b border-white/5 pb-1">
                                <span>Age</span> <span class="text-white font-mono">{{ $team->age }}</span>
                            </div>
                            <div class="flex justify-between border-b border-white/5 pb-1">
                                <span>Origin</span> <span class="text-white">{{ $team->origin }}</span>
                            </div>
                            @if($team->achievement)
                                <div class="flex justify-between border-b border-white/5 pb-1">
                                    <span>Prestasi</span> <span class="text-gold-400">{{ $team->achievement }}</span>
                                </div>
                            @endif
                            @if($team->experience)
                                <div class="flex justify-between border-b border-white/5 pb-1">
                                    <span>Pengalaman</span> <span class="text-white">{{ $team->experience }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="pt-2 normal-case tracking-normal text-neutral-500 italic text-xs line-clamp-2 md:line-clamp-3">
                            {{ $team->address }}
                        </div>
                    </div>
                    @if($team->instagram_url || $team->facebook_url)
                        <div class="flex gap-4 mt-4">
                            @if($team->instagram_url)<a href="{{ $team->instagram_url }}" class="text-white hover:text-gold-400" target="_blank" rel="noopener"><i class="fab fa-instagram"></i></a>@endif
                            @if($team->facebook_url)<a href="{{ $team->facebook_url }}" class="text-white hover:text-gold-400" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>@endif
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-4 text-center text-white opacity-50">Data Pro Team tidak terbaca.</div>
            @endforelse
        </div>

    </div>
</section>

<!-- AOS INIT (jika belum ada di layout, tambahkan di layout utama) -->
@push('scripts')
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>AOS.init({ once: true, duration: 900 });</script>
@endpush