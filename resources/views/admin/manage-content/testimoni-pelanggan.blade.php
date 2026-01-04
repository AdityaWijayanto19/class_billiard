@extends('layouts.admin')

@section('title', 'Edit Testimoni Pelanggan - Admin')

@section('content')
        <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Customer <span
                        style="color: var(--primary-color);">Testimonials</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Kelola testimoni yang dikirim oleh pelanggan. Testimoni baru akan muncul setelah diaktifkan.</p>
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


        <!-- TESTIMONIALS LIBRARY -->
        <div class="space-y-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500">Customer Testimonials Library</h2>
                <span class="text-[9px] text-slate-500 dark:text-gray-600">({{ $testimonis->count() }} testimoni)</span>
            </div>
            
            @if($testimonis->count() == 0)
            <div class="text-center py-12 bg-slate-50 dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg">
                <i class="ri-inbox-line text-4xl text-slate-400 dark:text-gray-600 mb-4"></i>
                <p class="text-sm text-slate-500 dark:text-gray-500">Belum ada testimoni. Testimoni akan muncul setelah pelanggan mengirim melalui form di website.</p>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-8">
                @foreach($testimonis as $testimoni)
                    <div
                        class="group bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden transition-all duration-300 p-8" @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.5)'" @mouseleave="$el.style.borderColor = ''">

                        <div class="flex flex-col lg:flex-row gap-10">
                            <!-- Left: Profile & Visual -->
                            <div class="lg:w-64 space-y-4 shrink-0">
                                <div class="relative w-24 h-24 mx-auto lg:mx-0">
                                    @if($testimoni->photo)
                                        <img src="{{ asset('storage/' . $testimoni->photo) }}" alt="{{ $testimoni->customer_name }}"
                                            class="w-full h-full rounded-md object-cover border border-slate-200 dark:border-white/10">
                                    @else
                                        <div
                                            class="w-full h-full rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400">
                                            <i class="ri-user-3-line text-3xl"></i>
                                        </div>
                                    @endif
                                    <div
                                        class="absolute -bottom-2 -right-2 text-black w-6 h-6 rounded-md flex items-center justify-center text-xs border-2 border-white dark:border-[#0A0A0A]" style="background-color: var(--primary-color);">
                                        <i class="ri-chat-quote-line"></i>
                                    </div>
                                </div>
                                <div class="text-center lg:text-left space-y-1">
                                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                        {{ $testimoni->customer_name }}</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        {{ $testimoni->customer_role }}</p>
                                    <div class="flex justify-center lg:justify-start gap-0.5 pt-2" style="color: var(--primary-color);">
                                        @for($i = 0; $i < 5; $i++)
                                            <i
                                                class="{{ $i < $testimoni->rating ? 'ri-star-fill' : 'ri-star-line opacity-30' }} text-xs"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Edit Form -->
                            <div class="flex-1">
                                <form action="{{ route('admin.cms.testimoni-pelanggan.update', $testimoni->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="space-y-2">
                                            <label
                                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Update
                                                Content</label>
                                            <textarea name="testimonial" rows="3"
                                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all leading-relaxed"
                                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                @blur="$el.style.borderColor = ''">{{ $testimoni->testimonial }}</textarea>
                                        </div>
                                        <div class="space-y-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Rating</label>
                                                    <input type="number" name="rating" min="1" max="5"
                                                        value="{{ $testimoni->rating }}"
                                                        class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-1.5 text-sm font-bold text-slate-900 dark:text-white outline-none transition-all"
                                                        style="color: var(--primary-color);"
                                                        @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                        @blur="$el.style.borderColor = ''">
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Order</label>
                                                    <input type="number" name="order" value="{{ $testimoni->order }}"
                                                        class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-1.5 text-sm font-bold text-slate-900 dark:text-white outline-none transition-all"
                                                        @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                        @blur="$el.style.borderColor = ''">
                                                </div>
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Photo Update</label>
                                                <input type="file" name="photo" accept="image/*"
                                                    class="text-[10px] text-slate-400 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[9px] file:font-black file:uppercase file:text-black file:cursor-pointer file:transition-all">
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Image (Alternative)</label>
                                                <input type="file" name="image" accept="image/*"
                                                    class="text-[10px] text-slate-400 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[9px] file:font-black file:uppercase file:text-black file:cursor-pointer file:transition-all">
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Name (Alternative)</label>
                                                <input type="text" name="name" value="{{ $testimoni->name ?? '' }}"
                                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-1.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                    @blur="$el.style.borderColor = ''">
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Role (Alternative)</label>
                                                <input type="text" name="role" value="{{ $testimoni->role ?? '' }}"
                                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-1.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                    @blur="$el.style.borderColor = ''">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer Actions -->
                                    <div
                                        class="mt-8 flex flex-wrap items-center justify-between gap-6 pt-6 border-t border-slate-100 dark:border-white/5">
                                        <div class="flex items-center gap-6">
                                            <label class="relative inline-flex items-center cursor-pointer"
                                                x-data="{ isActive: {{ $testimoni->is_active ? 'true' : 'false' }} }">
                                                <input type="checkbox" name="is_active" {{ $testimoni->is_active ? 'checked' : '' }} value="1" class="sr-only peer" @change="isActive = !isActive">
                                                <div
                                                    class="w-10 h-5 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"
                                                    :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }">
                                                </div>
                                                <span
                                                    class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Visible</span>
                                            </label>

                                            <div class="hidden md:block">
                                                <p class="text-[9px] text-slate-400 font-medium uppercase tracking-tight">
                                                    Identity: <span
                                                        class="text-slate-900 dark:text-white font-bold">{{ $testimoni->customer_name }}</span>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <button type="submit"
                                                style="background-color: var(--primary-color);"
                                                class="text-black text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md transition-all active:scale-95 shadow-sm"
                                                @mouseenter="$el.style.opacity = '0.85'"
                                                @mouseleave="$el.style.opacity = '1'">
                                                Update Feedback
                                            </button>
                                            <button type="button"
                                                onclick="if(confirm('Data testimoni akan dihapus permanen. Lanjutkan?')) document.getElementById('delete-form-{{ $testimoni->id }}').submit();"
                                                style="background-color: rgba(239, 68, 68, 0.2); color: #ef4444;"
                                                class="text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md transition-all active:scale-95"
                                                @mouseenter="$el.style.backgroundColor = '#ef4444'; $el.style.color = 'white'"
                                                @mouseleave="$el.style.backgroundColor = 'rgba(239, 68, 68, 0.2)'; $el.style.color = '#ef4444'">
                                                Purge
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <form id="delete-form-{{ $testimoni->id }}"
                                    action="{{ route('admin.cms.testimoni-pelanggan.destroy', $testimoni->id) }}" method="POST"
                                    class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        input:focus,
        textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px rgba(250, 154, 8, 0.1) !important;
        }

        input[type="file"]::file-selector-button {
            background-color: var(--primary-color);
            color: black;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        input[type="file"]::file-selector-button:hover {
            opacity: 0.85;
        }

        input[type="file"]::file-selector-button:active {
            transform: scale(0.95);
        }
    </style>
@endsection