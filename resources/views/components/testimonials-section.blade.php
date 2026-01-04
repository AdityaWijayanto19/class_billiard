{{-- Testimonials Section Component --}}
@php
    // Optimized: Use cached data with select specific columns if not passed from controller
    $testimonials = $testimonials ?? cache()->remember('component_testimonials', 1800, function () {
        return \App\Models\TestimoniPelanggan::select('id', 'title', 'subtitle', 'customer_name', 'name', 'customer_role', 'role', 'testimonial', 'rating', 'photo', 'image', 'order', 'is_active')
            ->where('is_active', true)
            ->orderBy('order')
            ->limit(4)
            ->get();
    });
@endphp

<section id="testimonials" class="py-24 bg-[#080808] relative overflow-hidden">
    <!-- Background Watermark -->
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full text-center pointer-events-none select-none opacity-[0.02]">
        <h2 class="text-[150px] md:text-[300px] font-bold text-white leading-none">REVIEWS</h2>
    </div>

    <div class="container mx-auto px-6 relative z-10">
        <!-- Section Header -->
        <div class="text-center mb-12" data-aos="fade-up">
            <span class="text-gold-400 font-bold tracking-[0.3em] text-sm uppercase mb-4 block">Client
                Feedback</span>
            <h2 class="text-4xl md:text-6xl text-white font-rumonds tracking-wide">
                TESTIMONIALS <br>
                <span class="text-gray-500 text-2xl md:text-4xl font-serif italic lowercase">that speak to our
                    quality</span>
            </h2>
        </div>

        <!-- Testimonials Grid - Moved to Top -->
        @if($testimonials->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto mb-20">
            @foreach($testimonials as $index => $testimonial)
            <div class="bg-[#111] p-8 md:p-10 rounded-2xl relative group hover:bg-[#151515] transition-colors duration-300"
                data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                <!-- Gold Accent Line -->
                <div class="absolute top-10 left-0 w-1 h-16 bg-gold-400 rounded-r-full"></div>

                <!-- Header: Avatar & Info -->
                <div class="flex items-center gap-4 mb-6 pl-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-gold-400/30 p-1">
                        @if($testimonial->image || $testimonial->photo)
                        <img src="{{ $testimonial->image ? asset('storage/' . $testimonial->image) : asset('storage/' . $testimonial->photo) }}" alt="{{ $testimonial->name ?? $testimonial->customer_name }}"
                            class="w-full h-full object-cover rounded-full filter grayscale group-hover:grayscale-0 transition-all duration-300">
                        @else
                        <div class="w-full h-full bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="ri-user-line text-gray-500"></i>
                        </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="text-white text-xl font-bold font-serif">{{ $testimonial->name ?? $testimonial->customer_name }}</h4>
                        <p class="text-gold-400 text-xs uppercase tracking-wider">{{ $testimonial->role ?? $testimonial->customer_role }}</p>
                        <!-- Stars -->
                        <div class="flex text-gold-400 gap-1 mt-2 text-xs">
                            @for($i = 0; $i < ($testimonial->rating ?? 5); $i++)
                            <i class="fas fa-star"></i>
                            @endfor
                        </div>
                    </div>
             
                </div>

                <!-- Content -->
                <p class="text-gray-400 font-light leading-relaxed pl-4 relative z-10">
                    {{ $testimonial->testimonial }}
                </p>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="text-center py-12 max-w-2xl mx-auto mb-20" data-aos="fade-up">
            <p class="text-gray-400 font-light text-sm">Belum ada testimoni. Jadilah yang pertama untuk berbagi pengalaman Anda!</p>
        </div>
        @endif

        <!-- Review Submission Form -->
        <div class="max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="200">
            <div class="relative bg-[#111] rounded-3xl p-6 md:p-8 border border-white/5 overflow-hidden">
                <!-- Decorative Background -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gold-400/5 rounded-full blur-[80px] pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full blur-[50px] pointer-events-none">
                </div>

                <div class="relative z-10">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl text-white font-serif mb-2">Share Your Experience</h3>
                        <p class="text-gray-400 font-light text-sm">We value your feedback. Tell us about your time
                            with us.</p>
                    </div>

                    <form action="{{ route('testimonial.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="testimonial-form">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name Input -->
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gold-400 uppercase tracking-widest mb-2 ml-1">Your
                                    Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required
                                    class="w-full bg-black/50 border border-white/10 rounded-lg px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-gold-400 transition-colors duration-300"
                                    placeholder="John Doe" value="{{ old('name') }}">
                                @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role Input -->
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gold-400 uppercase tracking-widest mb-2 ml-1">Occupation
                                    / Role (Optional)</label>
                                <input type="text" name="role"
                                    class="w-full bg-black/50 border border-white/10 rounded-lg px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-gold-400 transition-colors duration-300"
                                    placeholder="e.g. Amateur Player" value="{{ old('role') }}">
                            </div>
                        </div>

                        <!-- Rating Selection -->
                        <div class="group">
                            <label
                                class="block text-[10px] font-bold text-gold-400 uppercase tracking-widest mb-2 ml-1">Rating <span class="text-red-500">*</span></label>
                            <div
                                class="flex gap-4 items-center bg-black/50 border border-white/10 rounded-lg px-4 py-3">
                                <div class="flex gap-2 text-lg transition-colors" id="star-rating">
                                    @for($i = 1; $i <= 5; $i++)
                                    <button type="button" data-rating="{{ $i }}"
                                        class="star-btn text-gray-600 hover:text-gold-400 focus:text-gold-400 transition-colors cursor-pointer focus:outline-none">★</button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', 5) }}" required>
                                <span class="text-gray-500 text-xs ml-2 font-light" id="rating-text">(5 stars selected)</span>
                            </div>
                            @error('rating')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message Input -->
                        <div class="group">
                            <label
                                class="block text-[10px] font-bold text-gold-400 uppercase tracking-widest mb-2 ml-1">Your
                                Review <span class="text-red-500">*</span></label>
                            <textarea name="testimonial" rows="3" required
                                class="w-full bg-black/50 border border-white/10 rounded-lg px-4 py-3 text-sm text-white placeholder-gray-600 focus:outline-none focus:border-gold-400 transition-colors duration-300 resize-none"
                                placeholder="Write your thoughts here...">{{ old('testimonial') }}</textarea>
                            @error('testimonial')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Success/Error Messages -->
                        @if(session('testimonial_success'))
                        <div class="bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-lg">
                            <p class="text-emerald-500 text-sm">{{ session('testimonial_success') }}</p>
                        </div>
                        @endif

                        @if(session('testimonial_error'))
                        <div class="bg-red-500/10 border border-red-500/20 px-4 py-3 rounded-lg">
                            <p class="text-red-500 text-sm">{{ session('testimonial_error') }}</p>
                        </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="text-center pt-2">
                            <button type="submit"
                                class="inline-block bg-gold-400 text-black px-8 py-3 rounded-full font-bold text-sm tracking-widest uppercase hover:bg-white transition-all duration-300 transform hover:scale-105 shadow-[0_0_20px_rgba(255,215,0,0.3)]">
                                Submit Review
                            </button>
                        </div>
                    </form>

                    <script>
                        // Star Rating Handler
                        document.addEventListener('DOMContentLoaded', function() {
                            const starButtons = document.querySelectorAll('.star-btn');
                            const ratingInput = document.getElementById('rating-input');
                            const ratingText = document.getElementById('rating-text');
                            
                            // Initialize stars on load
                            const initialRating = parseInt(ratingInput.value) || 5;
                            updateStars(initialRating);
                            
                            // Add click event to each star
                            starButtons.forEach((btn, index) => {
                                btn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const rating = index + 1;
                                    ratingInput.value = rating;
                                    updateStars(rating);
                                    updateRatingText(rating);
                                });
                            });
                            
                            function updateStars(rating) {
                                starButtons.forEach((star, i) => {
                                    if (i < rating) {
                                        star.classList.add('text-gold-400');
                                        star.classList.remove('text-gray-600');
                                    } else {
                                        star.classList.remove('text-gold-400');
                                        star.classList.add('text-gray-600');
                                    }
                                });
                            }
                            
                            function updateRatingText(rating) {
                                ratingText.textContent = `(${rating} ${rating === 1 ? 'star' : 'stars'} selected)`;
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>

