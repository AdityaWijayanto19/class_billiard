@extends('layouts.admin')

@section('title', 'Edit Tentang Kami - Admin')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2\"
                    @mouseenter=\"$el.style.color = 'var(--primary-color)'\"
                    @mouseleave=\"$el.style.color = ''\">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Tentang <span
                        class="text-white\">Tentang <span style=\"color: var(--primary-color);\">Kami</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen narasi utama, visi perusahaan,
                    dan integrasi media video.</p>
            </div>
        </div>

        <!-- FEEDBACK MESSAGES (Alpine.js) -->
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)">
            @if(session('success'))
                <div x-show="show" x-transition
                    class="mb-8 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md">
                    <i class="ri-checkbox-circle-fill text-emerald-500"></i>
                    <span
                        class="text-[11px] font-black uppercase tracking-widest text-emerald-500">{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div x-show="show" x-transition class="mb-8 p-4 bg-red-500/10 border border-red-500/20 rounded-md">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="ri-error-warning-fill text-red-500"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Koreksi Diperlukan</span>
                    </div>
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-[11px] text-red-400 font-medium tracking-tight">— {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <form action="{{ route('admin.cms.tentang-kami') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
            @csrf

            <!-- SECTION 1: NARRATIVE CONTENT -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-slate-900 dark:text-white">Brand Narrative
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-gray-500 mt-2 leading-relaxed">Pengaturan judul dan deskripsi
                        filosofis perusahaan yang akan tampil di halaman utama.</p>
                </div>
                <div
                    class="lg:col-span-8 bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 space-y-8">
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Section
                            Title</label>
                        <input type="text" name="title" value="{{ $tentangKami->title ?? 'Tentang Kami' }}"
                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm font-bold text-slate-900 dark:text-white outline-none transition-all duration-300\"
                            @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                            @blur=\"$el.style.borderColor = ''\">
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Narrative
                            Subtitle</label>
                        <textarea name="subtitle" rows="3"
                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all leading-relaxed\"
                            @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                            @blur=\"$el.style.borderColor = ''\">{{ $tentangKami->subtitle ?? '' }}</textarea>
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">About Image</label>
                        <div class="relative group aspect-video rounded-lg border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/[0.02] overflow-hidden flex items-center justify-center p-8 transition-all duration-500\"
                             @mouseenter=\"$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.3)'\"
                             @mouseleave=\"$el.style.borderColor = ''\">
                            <input type="file" name="image" id="imageInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept="image/*">
                            
                            <div id="imagePreview" class="w-full h-full flex items-center justify-center">
                                @if($tentangKami && $tentangKami->image)
                                    <img src="{{ asset('storage/' . $tentangKami->image) }}" class="max-w-full max-h-full object-cover group-hover:scale-105 transition-transform duration-700 rounded-lg">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                                        <i class="ri-camera-switch-line text-3xl text-white"></i>
                                    </div>
                                @else
                                    <div class="text-center space-y-3">
                                        <i class="ri-image-add-line text-4xl text-slate-300 dark:text-white/10"></i>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Upload About Image</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <p class="text-[9px] text-slate-400 dark:text-gray-600 italic tracking-tight leading-relaxed">
                            *Gambar untuk section About (direkomendasikan resolusi tinggi).
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: VIDEO INTEGRATION -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                <div class="lg:col-span-4">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em]" style="color: var(--primary-color);">Video Production</h2>
                    <p class="text-xs text-slate-500 dark:text-gray-500 mt-2 leading-relaxed">Sematkan profil video untuk
                        memberikan impresi visual yang lebih mendalam kepada pengunjung.</p>
                </div>
                <div
                    class="lg:col-span-8 bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 space-y-8">
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">YouTube
                            Source URL</label>
                        <div class="relative">
                            <input type="url" name="video_url" value="{{ $tentangKami->video_url ?? '' }}"
                                placeholder="e.g. https://youtu.be/..."
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md pl-12 pr-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                            <i
                                class="ri-youtube-fill absolute left-4 top-1/2 -translate-y-1/2 text-2xl text-slate-300 dark:text-white/10"></i>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Production
                            Description</label>
                        <textarea name="video_description" rows="2"
                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all leading-relaxed" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">{{ $tentangKami->video_description ?? '' }}</textarea>
                    </div>

                    <!-- MODULE STATUS -->
                    <div class="pt-8 border-t border-slate-100 dark:border-white/5">
                        <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: {{ ($tentangKami && $tentangKami->is_active) ? 'true' : 'false' }} }">
                            <input type="checkbox" name="is_active" value="1" {{ ($tentangKami && $tentangKami->is_active) ? 'checked' : '' }} class="sr-only peer" @change="isActive = !isActive">
                            <div
                                class="w-11 h-6 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all transition-colors" 
                                :style="{ backgroundColor: isActive ? 'var(--primary-color)' : 'var(--tw-bg-slate-200)' }"
                                class="w-11 h-6 bg-slate-200 dark:bg-white/10">
                            </div>
                            <span
                                class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 italic">Publish
                                Module to Frontend</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- STICKY ACTION AREA -->
            <div class="pt-10 border-t border-slate-200 dark:border-white/5 flex justify-end">
                <button type="submit"
                    class="w-full md:w-auto text-black text-[10px] font-black uppercase tracking-widest py-4 px-16 rounded-md transition-all active:scale-95 flex items-center justify-center gap-3" style="background-color: var(--primary-color); box-shadow: 0 10px 15px -3px rgba(var(--primary-color-rgb), 0.1);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                    <i class="ri-save-3-line text-lg"></i>
                    Simpan Konfigurasi
                </button>
            </div>
        </form>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Focus Standard per Manifesto */
        input:focus,
        textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px rgba(var(--primary-color-rgb), 0.1) !important;
        }
    </style>

    <script>
        // Preview Handler for Image
        const imageInput = document.getElementById('imageInput');
        if (imageInput) {
            imageInput.onchange = evt => {
                const [file] = evt.target.files
                if (file) {
                    const preview = document.getElementById('imagePreview');
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