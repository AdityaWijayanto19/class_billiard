{{-- Contact Section Component --}}
@php
    use App\Services\MapsUrlConverter;
    
    // Jika $contact tidak dikirim dari controller, cari yang aktif (optimized)
    if (!isset($contact)) {
        $contact = cache()->remember('component_contact', 3600, function () {
            return \App\Models\Contact::select('id', 'title', 'subtitle', 'description', 'location_name', 'address', 'phone', 'email', 'whatsapp', 'navbar_label', 'navbar_link', 'google_maps_url', 'map_url', 'opening_hours', 'facebook_url', 'instagram_url', 'twitter_url', 'youtube_url', 'is_active')
                ->where('is_active', true)
                ->first();
        });
    }
    
    // Contact section hanya muncul jika ada data DAN is_active = true
    $isActive = $contact && $contact->is_active ? true : false;
    
    // Tidak ada fallback values - hanya menampilkan data dari database
    $title = $contact && $contact->title && trim($contact->title) !== '' ? trim($contact->title) : '';
    $subtitle = $contact && $contact->subtitle && trim($contact->subtitle) !== '' ? trim($contact->subtitle) : '';
    $description = $contact && $contact->description && trim($contact->description) !== '' ? trim($contact->description) : '';
    $locationName = $contact && $contact->location_name && trim($contact->location_name) !== '' ? trim($contact->location_name) : '';
    $address = $contact && $contact->address && trim($contact->address) !== '' ? trim($contact->address) : '';
    $phone = $contact && $contact->phone && trim($contact->phone) !== '' ? trim($contact->phone) : '';
    $email = $contact && $contact->email && trim($contact->email) !== '' ? trim($contact->email) : '';
    $whatsapp = $contact && $contact->whatsapp && trim($contact->whatsapp) !== '' ? trim($contact->whatsapp) : '';
    $openingHours = $contact && $contact->opening_hours && trim($contact->opening_hours) !== '' ? trim($contact->opening_hours) : '';
    
    // Handle Maps URL conversion
    $mapUrl = '';
    $mapsShareLink = '';
    $isEmbeddable = false;
    
    if ($contact && $contact->google_maps_url && trim($contact->google_maps_url) !== '') {
        $mapsData = MapsUrlConverter::convert(trim($contact->google_maps_url));
        $isEmbeddable = $mapsData['isEmbeddable'];
        $mapUrl = $mapsData['embedUrl'] ?? '';
        $mapsShareLink = $mapsData['shareUrl'];
    } elseif ($contact && $contact->map_url && trim($contact->map_url) !== '') {
        $mapsData = MapsUrlConverter::convert(trim($contact->map_url));
        $isEmbeddable = $mapsData['isEmbeddable'];
        $mapUrl = $mapsData['embedUrl'] ?? '';
        $mapsShareLink = $mapsData['shareUrl'];
    }
    
    $facebook = $contact && $contact->facebook_url && trim($contact->facebook_url) !== '' ? trim($contact->facebook_url) : '';
    $instagram = $contact && $contact->instagram_url && trim($contact->instagram_url) !== '' ? trim($contact->instagram_url) : '';
    $twitter = $contact && $contact->twitter_url && trim($contact->twitter_url) !== '' ? trim($contact->twitter_url) : '';
    $youtube = $contact && $contact->youtube_url && trim($contact->youtube_url) !== '' ? trim($contact->youtube_url) : '';
@endphp

@if($isActive && $contact)
<section id="contact" class="relative py-24 bg-[#050505] text-white overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] bg-gold-400/20 rounded-full blur-[150px]"></div>
        <div class="absolute bottom-0 right-0 w-[40%] h-[40%] bg-gold-400/10 rounded-full blur-[100px]"></div>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-16" data-aos="fade-up">
            @if($title)
            <h2 class="text-5xl md:text-6xl font-rumonds mb-4 text-gold-400">{{ $title }}</h2>
            @endif
            @if($subtitle)
            <p class="text-xl text-gray-300 font-light tracking-wide">{{ $subtitle }}</p>
            @endif
            @if($description)
            <p class="text-gray-400 text-sm mt-4 max-w-2xl mx-auto leading-relaxed">{{ $description }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Left Column: Contact Information -->
            <div class="space-y-8" data-aos="fade-right">
                @if($locationName)
                <div>
                    <h3 class="text-2xl font-rumonds mb-4 text-gold-400">{{ $locationName }}</h3>
                    <div class="w-16 h-1 bg-gold-400 mb-6"></div>
                </div>
                @endif

                <div class="space-y-6">
                    @if($address)
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300 shrink-0">
                            <i class="ri-map-pin-line text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Address</p>
                            <p class="text-white font-light text-base leading-relaxed">{{ $address }}</p>
                        </div>
                    </div>
                    @endif

                    @if($phone)
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300 shrink-0">
                            <i class="ri-phone-line text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Phone</p>
                            <a href="tel:{{ $phone }}" class="text-white font-light text-base hover:text-gold-400 transition-colors">{{ $phone }}</a>
                        </div>
                    </div>
                    @endif

                    @if($email)
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300 shrink-0">
                            <i class="ri-mail-line text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Email</p>
                            <a href="mailto:{{ $email }}" class="text-white font-light text-base hover:text-gold-400 transition-colors">{{ $email }}</a>
                        </div>
                    </div>
                    @endif

                    @if($whatsapp)
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300 shrink-0">
                            <i class="ri-whatsapp-line text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">WhatsApp</p>
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" class="text-white font-light text-base hover:text-gold-400 transition-colors">Chat with Us</a>
                        </div>
                    </div>
                    @endif

                    @if($openingHours)
                    <div class="flex items-start gap-4 group">
                        <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center text-gold-400 group-hover:bg-gold-400 group-hover:text-black transition-all duration-300 shrink-0">
                            <i class="ri-time-line text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Opening Hours</p>
                            <p class="text-white font-light text-base">{{ $openingHours }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Social Media Links -->
                    @if($facebook || $instagram || $twitter || $youtube)
                    <div class="pt-6 border-t border-white/10">
                        <p class="text-xs text-gray-500 uppercase tracking-widest mb-4">Follow Us</p>
                        <div class="flex gap-4">
                            @if($facebook)
                            <a href="{{ $facebook }}" target="_blank" rel="noopener noreferrer" 
                               class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 hover:bg-gold-400 hover:text-black transition-all duration-300">
                                <i class="ri-facebook-box-fill text-xl"></i>
                            </a>
                            @endif
                            @if($instagram)
                            <a href="{{ $instagram }}" target="_blank" rel="noopener noreferrer" 
                               class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 hover:bg-gold-400 hover:text-black transition-all duration-300">
                                <i class="ri-instagram-line text-xl"></i>
                            </a>
                            @endif
                            @if($twitter)
                            <a href="{{ $twitter }}" target="_blank" rel="noopener noreferrer" 
                               class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 hover:bg-gold-400 hover:text-black transition-all duration-300">
                                <i class="ri-twitter-x-line text-xl"></i>
                            </a>
                            @endif
                            @if($youtube)
                            <a href="{{ $youtube }}" target="_blank" rel="noopener noreferrer" 
                               class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gold-400 hover:bg-gold-400 hover:text-black transition-all duration-300">
                                <i class="ri-youtube-line text-xl"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Map -->
            <div class="space-y-8" data-aos="fade-left">
                <div>
                    <h3 class="text-2xl font-rumonds mb-4 text-gold-400">Find Us</h3>
                    <div class="w-16 h-1 bg-gold-400 mb-6"></div>
                </div>

                <!-- Map Container -->
                <div class="relative w-full h-[400px] rounded-2xl overflow-hidden border border-white/10 group">
                    @if($isEmbeddable && $mapUrl)
                    <!-- Embeddable Maps (Google Embed URL) -->
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
                            <div class="w-16 h-16 rounded-full bg-gold-400/20 flex items-center justify-center group-hover:bg-gold-400/30 transition-all">
                                <i class="ri-map-pin-line text-3xl text-gold-400"></i>
                            </div>
                            <div>
                                <p class="text-white font-semibold mb-1">View on Google Maps</p>
                                <p class="text-gray-400 text-xs">Click to open location</p>
                            </div>
                        </div>
                    </a>
                    @else
                    <!-- No map data -->
                    <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center">
                        <div class="text-center space-y-4">
                            <i class="ri-map-pin-line text-5xl text-gray-600"></i>
                            <p class="text-gray-500 text-sm">Map location will appear here</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endif

