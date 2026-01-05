{{-- Team Section Component --}}
@php
    try {
        // Ambil data tanpa cache untuk memastikan sinkronisasi admin
        $teamMembers = \App\Models\TimKami::where('is_active', 1)->orderBy('order', 'asc')->get();
        $proTeams = \App\Models\ProTeam::where('is_active', 1)->orderBy('order', 'asc')->get();
    } catch (\Exception $e) {
        $teamMembers = collect();
        $proTeams = collect();
    }
@endphp

{{-- Debug Ringkas (Hapus jika sudah muncul) --}}
<div class="hidden">Data Terdeteksi: {{ $teamMembers->count() }} elite, {{ $proTeams->count() }} pro</div>

<section id="team" class="py-24 bg-[#050505] relative overflow-hidden">
    
    <!-- BACKGROUND (Disederhanakan agar tidak menutupi konten) -->
    <div class="absolute inset-0 z-0 pointer-events-none opacity-30">
        <svg class="w-full h-full" viewBox="0 0 1440 1200" fill="none">
            <path d="M-100 200C200 400 600 -100 900 300C1200 700 1300 900 1600 800" stroke="#fbbf24" stroke-width="1" stroke-dasharray="10 5" />
        </svg>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        
        {{-- SECTION 1: THE MASTERS --}}
        <div class="text-center mb-20">
            <span class="text-gold-400 font-bold tracking-[0.5em] text-xs uppercase mb-4 block">The Masters</span>
            <h2 class="text-5xl md:text-7xl text-white font-serif uppercase italic">
                OUR <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 via-white to-gold-400">ELITE TEAM</span>
            </h2>
        </div>

        {{-- ELITE GRID - Dibuat "Bandel" (Pasti Muncul) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-32">
            @foreach($teamMembers as $index => $member)
            <div class="group relative h-[550px] w-full bg-neutral-900 rounded-sm overflow-hidden border border-white/10 {{ $index == 1 ? 'md:-mt-8' : '' }}">
                
                <!-- Background Name (Bisa dihapus jika mengganggu) -->
                <div class="absolute inset-0 flex items-center justify-center opacity-10 z-0">
                    <span class="text-8xl font-black text-white transform -rotate-90 uppercase select-none">
                        {{ substr($member->name, 0, 1) }}
                    </span>
                </div>

                <!-- IMAGE HANDLING (Custom Path) -->
                @php 
                    $photo = $member->photo ?? $member->image;
                    // Jika kamu tidak pakai storage:link, sesuaikan path di bawah ini
                    // Misal: asset('uploads/' . $photo) atau lainnya
                    $imageSource = $photo ? asset('storage/' . $photo) : null; 
                @endphp

                @if($imageSource)
                    <img src="{{ $imageSource }}" alt="{{ $member->name }}"
                        class="absolute inset-0 w-full h-full object-cover z-10 grayscale group-hover:grayscale-0 transition-all duration-700">
                @else
                    <div class="absolute inset-0 flex items-center justify-center z-10 bg-neutral-800">
                        <i class="fas fa-user-circle text-6xl text-white/10"></i>
                    </div>
                @endif

                <!-- Content Always Visible -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent z-20"></div>

                <div class="absolute bottom-0 left-0 w-full p-8 z-30">
                    <div class="border-l-4 border-gold-400 pl-4">
                        <h3 class="text-2xl md:text-3xl text-white font-serif italic mb-1">{{ $member->name }}</h3>
                        <p class="text-gold-400 text-[10px] font-black uppercase tracking-widest mb-3">{{ $member->position }}</p>
                        
                        @if($member->bio)
                        <p class="text-gray-400 text-xs font-light line-clamp-2 mb-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ $member->bio }}
                        </p>
                        @endif

                        <div class="flex gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            @if($member->instagram_url)
                                <a href="{{ $member->instagram_url }}" class="text-white hover:text-gold-400"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if($member->facebook_url)
                                <a href="{{ $member->facebook_url }}" class="text-white hover:text-gold-400"><i class="fab fa-facebook-f"></i></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- SECTION 2: PRO TEAM --}}
        <div class="text-center mb-16">
            <span class="text-gold-400 font-bold tracking-[0.5em] text-xs uppercase mb-4 block">Rising Stars</span>
            <h3 class="text-4xl md:text-6xl text-white font-serif uppercase italic">PRO <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-400 to-white">TEAM</span></h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($proTeams as $team)
            <div class="bg-white/[0.03] p-8 border border-white/5 hover:border-gold-400/30 transition-all group">
                <div class="flex justify-between items-start mb-6">
                    <h4 class="text-xl text-white font-serif font-bold group-hover:text-gold-400 transition-colors">{{ $team->name }}</h4>
                    <i class="fas fa-user-tie text-white/10 group-hover:text-gold-400"></i>
                </div>
                <div class="space-y-3 text-[11px] uppercase tracking-widest text-neutral-500">
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span>Age</span> <span class="text-white">{{ $team->age }}</span>
                    </div>
                    <div class="flex justify-between border-b border-white/5 pb-2">
                        <span>Origin</span> <span class="text-white">{{ $team->origin }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="block mb-1">Address</span>
                        <p class="text-neutral-400 normal-case tracking-normal">{{ $team->address }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>