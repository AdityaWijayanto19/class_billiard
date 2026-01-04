{{-- Footer Section Component --}}
@php
    use App\Services\MapsUrlConverter;
    
    // Jika $footer tidak dikirim dari controller, cari yang aktif (optimized)
    // Tapi jika dikirim dari controller (meskipun is_active = false), gunakan yang dikirim
    if (!isset($footer)) {
        $footer = cache()->remember('component_footer', 3600, function () {
            return \App\Models\Footer::select('id', 'about_text', 'facebook_url', 'instagram_url', 'twitter_url', 'youtube_url', 'whatsapp', 'address', 'location_name', 'phone', 'email', 'google_maps_url', 'map_url', 'monday_friday_hours', 'saturday_sunday_hours', 'opening_hours', 'copyright', 'is_active')
                ->where('is_active', true)
                ->first();
        });
    }
    
    // Tidak ada fallback values - hanya menampilkan data dari database
    // Trim dan cek apakah string kosong setelah trim
    $address = $footer && $footer->address && trim($footer->address) !== '' ? trim($footer->address) : '';
    $locationName = $footer && $footer->location_name && trim($footer->location_name) !== '' ? trim($footer->location_name) : '';
    $phone = $footer && $footer->phone && trim($footer->phone) !== '' ? trim($footer->phone) : '';
    $email = $footer && $footer->email && trim($footer->email) !== '' ? trim($footer->email) : '';
    // Hanya gunakan opening_hours jika ada, tidak fallback ke monday_friday_hours/saturday_sunday_hours
    $openingHours = $footer && $footer->opening_hours && trim($footer->opening_hours) !== '' ? trim($footer->opening_hours) : '';
    
    // Handle Maps URL conversion
    $mapUrl = '';
    $mapsShareLink = '';
    $isEmbeddable = false;
    
    if ($footer && $footer->google_maps_url && trim($footer->google_maps_url) !== '') {
        $mapsData = MapsUrlConverter::convert(trim($footer->google_maps_url));
        $isEmbeddable = $mapsData['isEmbeddable'];
        $mapUrl = $mapsData['embedUrl'] ?? '';
        $mapsShareLink = $mapsData['shareUrl'];
    } elseif ($footer && $footer->map_url && trim($footer->map_url) !== '') {
        $mapsData = MapsUrlConverter::convert(trim($footer->map_url));
        $isEmbeddable = $mapsData['isEmbeddable'];
        $mapUrl = $mapsData['embedUrl'] ?? '';
        $mapsShareLink = $mapsData['shareUrl'];
    }
    
    $instagram = $footer && $footer->instagram_url && trim($footer->instagram_url) !== '' ? trim($footer->instagram_url) : '';
    $facebook = $footer && $footer->facebook_url && trim($footer->facebook_url) !== '' ? trim($footer->facebook_url) : '';
    $whatsapp = $footer && $footer->whatsapp && trim($footer->whatsapp) !== '' ? trim($footer->whatsapp) : '';
    $copyright = $footer && $footer->copyright && trim($footer->copyright) !== '' ? trim($footer->copyright) : '';
@endphp

@if($footer)
<footer class="bg-[#050505] text-white pt-24 pb-12 border-t border-white/5 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-gold-400/20 rounded-full blur-[150px]">
        </div>
        <div class="absolute bottom-0 right-0 w-[40%] h-[40%] bg-gold-400/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">

            <!-- Left Column: Map/Location (Taking up 5 columns) -->
            <div class="lg:col-span-5 space-y-8" data-aos="fade-right">
                <div>
                    <h3 class="text-3xl font-rumonds mb-2">FIND US</h3>
                    <div class="w-16 h-1 bg-gold-400"></div>
                </div>

                <!-- Stylized Map Container -->
                <div class="relative w-full h-[300px] rounded-2xl overflow-hidden border border-white/10 group">
                    <!-- Embeddable Maps (Google Embed URL) -->
                    @if($isEmbeddable && $mapUrl)
                    <iframe
                        src="{{ $mapUrl }}"
                        width="100%" height="100%"
                        style="border:0; filter: grayscale(100%) invert(92%) contrast(83%);" 
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="Location Map">
                    </iframe>
                    @elseif($mapsShareLink)
                    <!-- Non-Embeddable Maps (show link button instead) -->
                    <a href="{{ $mapsShareLink }}" target="_blank" rel="noopener noreferrer"
                        class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex flex-col items-center justify-center hover:from-slate-700 hover:to-slate-800 transition-all duration-300 group">
                        <div class="text-center space-y-4">
                            <div class="w-16 h-16 bg-gold-400/20 rounded-full flex items-center justify-center mx-auto group-hover:bg-gold-400/30 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-white font-bold">Open Location</p>
                                <p class="text-gray-400 text-sm">Click to view on Google Maps</p>
                            </div>
                        </div>
                    </a>
                    @else
                    <div class="w-full h-full bg-gray-900 flex items-center justify-center">
                        <div class="text-center">
                            <i class="ri-map-pin-line text-4xl text-gray-600 mb-2"></i>
                            <p class="text-gray-600 text-sm">No map URL provided</p>
                        </div>
                    </div>
                    @endif

                    <!-- Overlay Card -->
                    @if($locationName || $address || $mapsShareLink)
                    <div
                        class="absolute bottom-4 left-4 right-4 bg-black/90 backdrop-blur-md p-4 rounded-xl border border-white/10 flex items-center justify-between">
                        <div>
                            @if($locationName)
                            <p class="text-gold-400 text-xs font-bold tracking-widest uppercase">{{ $locationName }}</p>
                            @endif
                            @if($address)
                            <p class="text-gray-400 text-xs mt-1">{{ $address }}</p>
                            @endif
                        </div>
                        @if($mapsShareLink)
                        <a href="{{ $mapsShareLink }}" target="_blank"
                            class="w-10 h-10 bg-gold-400 rounded-full flex items-center justify-center text-black hover:scale-110 transition-transform flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Content (Taking up 7 columns) -->
            <div class="lg:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-12" data-aos="fade-left">

                <!-- Contact Info -->
                <div class="space-y-8">
                    <div>
                        <h3 class="text-2xl font-serif mb-6 text-white">Contact Info</h3>
                        <ul class="space-y-6">
                            @if($phone)
                            <li class="flex items-start gap-4 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Phone</p>
                                    <p class="text-white font-serif text-lg">{{ $phone }}</p>
                                </div>
                            </li>
                            @endif
                            @if($email)
                            <li class="flex items-start gap-4 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Email</p>
                                    <p class="text-white font-serif text-lg">{{ $email }}</p>
                                </div>
                            </li>
                            @endif
                            @if($openingHours)
                            <li class="flex items-start gap-4 group">
                                <div
                                    class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Opening Hours
                                    </p>
                                    <p class="text-white font-serif text-lg">{{ $openingHours }}</p>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>

                <!-- Quick Links & Social -->
                <div class="space-y-8">
                    <div>
                        <h3 class="text-2xl font-serif mb-6 text-white">Quick Links</h3>
                        <ul class="space-y-3">
                            <li><a href="#"
                                    class="text-gray-400 hover:text-gold-400 transition-colors text-sm tracking-wide">Home</a>
                            </li>
                            <li><a href="#about"
                                    class="text-gray-400 hover:text-gold-400 transition-colors text-sm tracking-wide">About
                                    Us</a></li>
                            <li><a href="#events"
                                    class="text-gray-400 hover:text-gold-400 transition-colors text-sm tracking-wide">Events
                                    & Tournaments</a></li>
                            <li><a href="#menu"
                                    class="text-gray-400 hover:text-gold-400 transition-colors text-sm tracking-wide">Food
                                    & Beverage</a></li>
                            <li><a href="#reservation"
                                    class="text-gray-400 hover:text-gold-400 transition-colors text-sm tracking-wide">Book
                                    a Table</a></li>
                        </ul>
                    </div>

                    @if($instagram || $facebook || $whatsapp)
                    <div>
                        <h3 class="text-2xl font-serif mb-6 text-white">Follow Us</h3>
                        <div class="flex gap-4">
                            @if($instagram)
                            <a href="{{ $instagram }}"
                                class="w-10 h-10 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if($facebook)
                            <a href="{{ $facebook }}"
                                class="w-10 h-10 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            @endif
                            @if($whatsapp)
                            <a href="{{ $whatsapp }}"
                                class="w-10 h-10 border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-gold-400 hover:border-gold-400 hover:text-black transition-all duration-300">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>

        <!-- Footer Bottom -->
        <div
            class="border-t border-white/10 mt-16 pt-8 flex flex-col md:flex-row items-center justify-between gap-4">
            @if($copyright)
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo"
                    class="w-8 h-8 object-contain opacity-50 grayscale hover:grayscale-0 transition-all">
                <p class="text-gray-500 text-xs tracking-widest">{{ $copyright }}</p>
            </div>
            @else
            <div></div>
            @endif
            <div class="flex gap-6">
                <a href="#"
                    class="text-gray-600 hover:text-white text-xs tracking-widest uppercase transition-colors">Privacy
                    Policy</a>
                <a href="#"
                    class="text-gray-600 hover:text-white text-xs tracking-widest uppercase transition-colors">Terms
                    of Service</a>
            </div>
        </div>
    </div>
</footer>
@endif

