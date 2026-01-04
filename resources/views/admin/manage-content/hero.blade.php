@extends('layouts.admin')

@section('title', 'Edit Hero Section - Admin')

@section('content')
<div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">
    
    <!-- HEADER STANDARD -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
        <div class="space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Hero <span style="color: var(--primary-color);">Configuration</span></h1>
            <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Atur identitas utama, logo brand, dan pesan sambutan di halaman muka.</p>
        </div>
    </div>

    <!-- FLASH MESSAGE -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
             class="mb-8 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
            <i class="ri-checkbox-circle-fill text-emerald-500"></i>
            <span class="text-[11px] font-black uppercase tracking-widest text-emerald-500">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.cms.hero') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        @csrf

        <!-- LEFT COLUMN: VISUAL ASSETS -->
        <div class="lg:col-span-5 space-y-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Brand Logo Asset</label>
                <div class="relative group aspect-video rounded-lg border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/[0.02] overflow-hidden flex items-center justify-center p-8 transition-all duration-500" @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.3)'" @mouseleave="$el.style.borderColor = ''">
                    <input type="file" name="logo_image" id="logoInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept="image/*">
                    
                    <div id="preview" class="w-full h-full flex items-center justify-center">
                        @if($hero && $hero->logo_image)
                            <img src="{{ asset('storage/' . $hero->logo_image) }}" class="max-w-full max-h-full object-contain group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                                <i class="ri-camera-switch-line text-3xl text-white"></i>
                            </div>
                        @else
                            <div class="text-center space-y-3">
                                <i class="ri-image-add-line text-4xl text-slate-300 dark:text-white/10"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Upload Brand Logo</p>
                            </div>
                        @endif
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 dark:text-gray-600 italic tracking-tight leading-relaxed">
                    *Logo dengan Crown & 8-Ball direkomendasikan dalam format PNG Transparan.
                </p>
            </div>

            <!-- Background Image Upload -->
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Background Image</label>
                <div class="relative group aspect-video rounded-lg border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/[0.02] overflow-hidden flex items-center justify-center p-8 transition-all duration-500" @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.3)'" @mouseleave="$el.style.borderColor = ''">
                    <input type="file" name="background_image" id="backgroundInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept="image/*">
                    
                    <div id="backgroundPreview" class="w-full h-full flex items-center justify-center">
                        @if($hero && $hero->background_image)
                            <img src="{{ asset('storage/' . $hero->background_image) }}" class="max-w-full max-h-full object-cover group-hover:scale-105 transition-transform duration-700 rounded-lg">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                                <i class="ri-camera-switch-line text-3xl text-white"></i>
                            </div>
                        @else
                            <div class="text-center space-y-3">
                                <i class="ri-image-add-line text-4xl text-slate-300 dark:text-white/10"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Upload Background Image</p>
                            </div>
                        @endif
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 dark:text-gray-600 italic tracking-tight leading-relaxed">
                    *Gambar background untuk hero section (direkomendasikan resolusi tinggi).
                </p>
            </div>

            <!-- STATUS TOGGLE -->
            <div class="p-6 rounded-lg border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01]">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">Visibility Status</p>
                        <p class="text-[9px] text-slate-400 uppercase tracking-tighter">Enable/Disable Hero Section</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ ($hero && $hero->is_active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all" style="background-color: var(--primary-color);" @change="$el.style.backgroundColor = $el.previousElementSibling.checked ? 'var(--primary-color)' : ''"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: CONTENT CONFIGURATION -->
        <div class="lg:col-span-7 space-y-8">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                <div class="space-y-8">
                    <!-- Main Title -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Main Display Title</label>
                            <span class="text-[9px] font-bold uppercase tracking-widest" style="color: var(--primary-color);">Large Text</span>
                        </div>
                        <input type="text" name="title" placeholder="e.g. CLASS" value="{{ $hero->title ?? 'CLASS' }}" required
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-4 text-2xl font-bold tracking-tight text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                    </div>

                    <!-- Subtitle -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Banner Subtitle</label>
                            <span class="text-[9px] font-bold uppercase tracking-widest" style="color: var(--primary-color);">Red Banner Text</span>
                        </div>
                        <input type="text" name="subtitle" placeholder="e.g. BILLIARD" value="{{ $hero->subtitle ?? 'BILLIARD' }}" required
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm font-bold text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                    </div>

                    <!-- Tagline -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Tagline</label>
                        <input type="text" name="tagline" placeholder="e.g. Premium Billiard Lounge & Bar" value="{{ $hero->tagline ?? 'Premium Billiard Lounge & Bar' }}"
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                    </div>

                    <!-- CTA Text 1 -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">CTA Button 1 Text</label>
                        <input type="text" name="cta_text_1" placeholder="e.g. BOOK A TABLE" value="{{ $hero->cta_text_1 ?? 'BOOK A TABLE' }}"
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                    </div>

                    <!-- CTA Link 1 (Group WA / Booking) -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">CTA Button 1 Link (Group WA / Booking)</label>
                        <input type="text" name="cta_link_1" placeholder="e.g. https://wa.me/6281234567890 or 081234567890" value="{{ $hero->cta_link_1 ?? '' }}"
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        <p class="text-xs text-slate-400 mt-1">Masukkan link grup WhatsApp atau nomor (contoh: <code>081234567890</code>) untuk tombol "{{ $hero->cta_text_1 ?? 'BOOK A TABLE' }}". Sistem akan menormalisasi nomor menjadi <code>https://wa.me/&lt;number&gt;</code> saat disimpan.</p>
                    </div>

                    <!-- CTA Text 2 -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">CTA Button 2 Text</label>
                        <input type="text" name="cta_text_2" value="{{ $hero->cta_text_2 ?? 'EXPLORE' }}"
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                               placeholder="e.g. EXPLORE" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                    </div>
                </div>

                <!-- SAVE BUTTON -->
                <div class="mt-12 pt-8 border-t border-slate-100 dark:border-white/5">
                    <button type="submit" class="w-full text-black text-[10px] font-black uppercase tracking-widest py-4 rounded-md transition-all shadow-lg active:scale-95 flex items-center justify-center gap-3" style="background-color: var(--primary-color); box-shadow: 0 10px 15px -3px rgba(var(--primary-color-rgb), 0.1);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                        <i class="ri-save-3-line text-lg"></i>
                        Update Hero Masterpiece
                    </button>
                </div>
            </div>
            
            <p class="text-[10px] text-center text-slate-400 dark:text-gray-600 font-medium uppercase tracking-widest">
                *Changes will be reflected immediately on the landing page.
            </p>
        </div>
    </form>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* Focus State - handled by Alpine directives @focus/@blur for dynamic colors */
    /* No hardcoded focus styles needed - using CSS variables instead */
</style>

<script>
    // Preview Handler for Logo
    const logoInput = document.getElementById('logoInput');
    if (logoInput) {
        logoInput.onchange = evt => {
            const [file] = evt.target.files
            if (file) {
                const preview = document.getElementById('preview');
                if (preview) {
                    preview.style.opacity = '0';
                    setTimeout(() => {
                        preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="max-w-full max-h-full object-contain animate-in zoom-in-95 duration-500">`;
                        preview.style.opacity = '1';
                    }, 300);
                }
            }
        }
    }

    // Preview Handler for Background
    const backgroundInput = document.getElementById('backgroundInput');
    if (backgroundInput) {
        backgroundInput.onchange = evt => {
            const [file] = evt.target.files
            if (file) {
                const preview = document.getElementById('backgroundPreview');
                if (preview) {
                    preview.style.opacity = '0';
                    setTimeout(() => {
                        preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="max-w-full max-h-full object-cover group-hover:scale-105 transition-transform duration-700 rounded-lg">`;
                        preview.style.opacity = '1';
                    }, 300);
                }
            }
        }
    }
</script>
@endsection