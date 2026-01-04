@extends('layouts.admin')

@section('title', 'Edit About Founder - Admin')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Back to Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Edit <span
                        style="color: var(--primary-color);">About Founder</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen profil naratif dan identitas
                    digital pendiri perusahaan.</p>
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

        <form action="{{ route('admin.cms.about-founder.update') }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            @csrf

            <!-- LEFT COLUMN: VISUAL IDENTITY -->
            <div class="lg:col-span-4 space-y-8">
                <div class="space-y-2">
                    <label
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Founder
                        Photo</label>
                    <div
                        class="relative group aspect-[3/4] overflow-hidden rounded-lg border border-slate-200 dark:border-white/5 bg-slate-50 dark:bg-white/[0.02]">
                        <input type="file" name="photo" id="photoInput"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                        <div id="preview"
                            class="w-full h-full flex flex-col items-center justify-center text-slate-400 transition-all duration-300" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                            @if($aboutFounder && ($aboutFounder->photo || $aboutFounder->image))
                                <img src="{{ asset('storage/' . ($aboutFounder->image ?? $aboutFounder->photo)) }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                    <i class="ri-camera-line text-3xl text-white"></i>
                                </div>
                            @else
                                <i class="ri-image-add-line text-4xl mb-3"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Upload Portrait</span>
                            @endif
                        </div>
                    </div>
                    <p class="text-[9px] text-slate-400 dark:text-gray-600 italic tracking-tight">*Format portrait (3:4)
                        direkomendasikan untuk hasil terbaik.</p>
                </div>

                <!-- Image Upload (Alternative) -->
                <div class="space-y-2">
                    <label
                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Image (Alternative)</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-slate-200 dark:file:bg-white/10 file:text-black">
                    <p class="text-[9px] text-slate-400 dark:text-gray-600 italic tracking-tight">*Alternatif field untuk image.</p>
                </div>

                <!-- STATUS TOGGLE -->
                <div class="p-6 rounded-lg border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01]">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-900 dark:text-white">
                                Module Status</p>
                            <p class="text-[9px] text-slate-400 uppercase tracking-tighter">Visibility on frontend</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ ($aboutFounder && $aboutFounder->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-slate-200 dark:bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all" style="background-color: var(--primary-color);" @change="$el.style.backgroundColor = $el.previousElementSibling.checked ? 'var(--primary-color)' : ''">
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: BIOGRAPHY & SOCIALS -->
            <div class="lg:col-span-8 space-y-8">
                <div
                    class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Title -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Section
                                Title</label>
                            <input type="text" name="title" value="{{ $aboutFounder->title ?? 'Tentang Founder' }}"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all font-bold" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. Meet Our Visionary">
                        </div>

                        <!-- Founder Name -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Founder
                                Full Name</label>
                            <input type="text" name="name" value="{{ $aboutFounder->name ?? '' }}"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all font-bold" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="Enter name here">
                        </div>

                        <!-- Subtitle -->
                        <div class="space-y-2 md:col-span-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Subtitle
                                / Short Catchphrase</label>
                            <textarea name="subtitle" rows="2"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all leading-relaxed" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">{{ $aboutFounder->subtitle ?? '' }}</textarea>
                        </div>

                        <!-- Position -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Position / Role</label>
                            <input type="text" name="position" value="{{ $aboutFounder->position ?? 'Founder & CEO' }}"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all font-bold" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. Founder & CEO">
                        </div>

                        <!-- Signature -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Signature</label>
                            <input type="text" name="signature" value="{{ $aboutFounder->signature ?? '' }}"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all font-serif italic" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. L.Ipsum">
                        </div>

                        <!-- YouTube Video URL -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">YouTube Video URL</label>
                            <input type="url" name="video_url" value="{{ $aboutFounder->video_url ?? '' }}"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="e.g. https://www.youtube.com/watch?v=VIDEO_ID atau https://youtu.be/VIDEO_ID">
                        </div>

                        <!-- Quote -->
                        <div class="space-y-2 md:col-span-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Quote / Statement</label>
                            <textarea name="quote" rows="4"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all leading-relaxed" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="Billiards is not just a game; it is an art of precision, patience, and strategy.">{{ $aboutFounder->quote ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- SOCIAL MEDIA SECTION -->
                    <div class="mt-10 pt-10 border-t border-slate-100 dark:border-white/5">
                        <label class="text-[10px] font-black uppercase tracking-widest block mb-6" style="color: var(--primary-color);">Social
                            Connectors</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400"><i
                                        class="ri-facebook-fill mr-1"></i> Facebook</label>
                                <input type="url" name="facebook_url" value="{{ $aboutFounder->facebook_url ?? '' }}"
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-[11px] text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                    placeholder="https://facebook.com/...">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400"><i
                                        class="ri-instagram-line mr-1"></i> Instagram</label>
                                <input type="url" name="instagram_url" value="{{ $aboutFounder->instagram_url ?? '' }}"
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-[11px] text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                    placeholder="https://instagram.com/...">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400"><i
                                        class="ri-linkedin-box-fill mr-1"></i> LinkedIn</label>
                                <input type="url" name="linkedin_url" value="{{ $aboutFounder->linkedin_url ?? '' }}"
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-[11px] text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                    placeholder="https://linkedin.com/in/...">
                            </div>
                        </div>
                    </div>

                    <!-- SAVE BUTTON -->
                    <div class="mt-12 flex justify-end">
                        <button type="submit"
                            class="w-full md:w-auto text-black text-[10px] font-black uppercase tracking-widest py-4 px-12 rounded-md transition-all shadow-sm active:scale-95" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                            Commit Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* File Input Dynamic Color */
        input[type="file"]::file-selector-button {
            background-color: var(--primary-color);
            color: black;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        input[type="file"]:hover::file-selector-button {
            opacity: 0.85;
            transform: translateY(-1px);
        }

        input[type="file"]:active::file-selector-button {
            transform: scale(0.95);
        }

        /* Focus states handled by Alpine directives @focus/@blur for dynamic colors */
        /* No hardcoded styles needed - using CSS variables instead */
    </style>

    <script>
        // Simple Preview Handler
        const photoInput = document.getElementById('photoInput');
        if (photoInput) {
            photoInput.onchange = evt => {
                const [file] = evt.target.files
                if (file) {
                    const preview = document.getElementById('preview');
                    if (preview) {
                        preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover animate-in fade-in duration-500">`;
                    }
                }
            }
        }
    </script>
@endsection