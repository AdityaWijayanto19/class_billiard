@extends('layouts.admin')

@section('title', 'Event Management - Admin')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300"
        x-data="{ showCreate: false }">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-[color:var(--primary-color)] transition-all duration-300 mb-2">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Kembali ke Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Management <span
                        style="color: var(--primary-color);">Events</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Atur jadwal, publikasi, dan dokumentasi
                    event operasional.</p>
            </div>

            <button @click="showCreate = !showCreate"
                class="btn-primary text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm flex items-center gap-2 active:scale-95"
                style="background-color: var(--primary-color);">
                <i :class="showCreate ? 'ri-close-line' : 'ri-add-line'" class="text-lg"></i>
                <span x-text="showCreate ? 'Batalkan' : 'Tambah Event Baru'"></span>
            </button>
        </div>

        <!-- FLASH MESSAGE -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-8 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md animate-in fade-in slide-in-from-top-4 duration-300">
                <i class="ri-checkbox-circle-fill text-emerald-500"></i>
                <span class="text-[11px] font-black uppercase tracking-widest text-emerald-500">{{ session('success') }}</span>
            </div>
        @endif

        <!-- CREATION MODULE (Alpine.js Toggle) -->
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-12">
            <div class="bg-slate-50 dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8" style="color: var(--primary-color);">Informasi Event Baru</h2>
                <form action="{{ route('admin.cms.event.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Event
                                Title</label>
                            <input type="text" name="event_title" required
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                style="focus-color: var(--primary-color);">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Event
                                Date</label>
                            <input type="date" name="event_date"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                style="focus-color: var(--primary-color);">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Category</label>
                            <input type="text" name="category" placeholder="e.g. Tournament, Workshop, Exhibition"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                style="focus-color: var(--primary-color);">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Sort
                                Order</label>
                            <input type="number" name="order" value="0"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                style="focus-color: var(--primary-color);">
                        </div>
                        <div class="md:col-span-2 lg:col-span-3 space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Event
                                Description</label>
                            <textarea name="event_description" rows="3"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all\"
                                @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                                @blur=\"$el.style.borderColor = '\"\n></textarea>
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Poster
                                / Image</label>
                            <input type="file" name="image" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-black"
                                style="--file-bg: var(--primary-color);"
                                @change="$el.style.setProperty('--file-bg-current', window.getComputedStyle($el).getPropertyValue('--primary-color'))">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Link
                                URL (Optional)</label>
                            <input type="url" name="link_url"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                style="focus-color: var(--primary-color);">
                        </div>
                        <div class="flex items-center pt-6">
                            <label class="relative inline-flex items-center cursor-pointer"
                                x-data="{ isActive: true }">
                                <input type="checkbox" name="is_active" checked value="1" class="sr-only peer" @change="isActive = !isActive">
                                <div
                                    class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                    :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }">
                                </div>
                                <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Aktifkan
                                    Sekarang</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-10 flex justify-end">
                        <button type="submit"
                            class="btn-primary text-black text-[10px] font-black uppercase tracking-widest py-4 px-12 rounded-md transition-all active:scale-95 shadow-lg"
                            style="background-color: var(--primary-color); box-shadow: 0 10px 15px -3px rgba(var(--primary-color-rgb), 0.1);">
                            Publish Event
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EXISTING EVENTS LIST -->
        <div class="space-y-6">
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500 mb-6">Existing
                Events Library</h2>

            <div class="grid grid-cols-1 gap-6">
                @foreach($events as $event)
                    <div
                        class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden flex flex-col lg:flex-row group transition-all duration-300\"
                        @mouseenter=\"$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.5)'\"
                        @mouseleave=\"$el.style.borderColor = ''\">

                        <!-- Image Section -->
                        <div class="w-full lg:w-72 h-48 lg:h-auto bg-slate-100 dark:bg-white/5 relative overflow-hidden">
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->event_title }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 dark:text-white/5">
                                    <i class="ri-image-line text-4xl"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 left-4">
                                <span
                                    class="bg-black/60 backdrop-blur-md text-white text-[8px] font-black px-2 py-1 rounded uppercase tracking-widest border border-white/10">
                                    Order: {{ $event->order }}
                                </span>
                            </div>
                        </div>

                        <!-- Content Section -->
                        <div class="flex-1 p-8">
                            <form action="{{ route('admin.cms.event.update', $event->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Event
                                            Title</label>
                                        <input type="text" name="event_title" value="{{ $event->event_title }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                            style="focus-color: var(--primary-color);">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Event
                                            Date</label>
                                        <input type="date" name="event_date"
                                            value="{{ $event->event_date ? $event->event_date->format('Y-m-d') : '' }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                            style="focus-color: var(--primary-color);">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Category</label>
                                        <input type="text" name="category" value="{{ $event->category ?? '' }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                            style="focus-color: var(--primary-color);"
                                            placeholder="e.g. Tournament">
                                    </div>
                                    <div class="md:col-span-2 space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Event
                                            Description</label>
                                        <textarea name="event_description" rows="2"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all\"
                                            @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                                            @blur=\"$el.style.borderColor = ''\">{{ $event->event_description ?? $event->description }}</textarea>
                                    </div>

                                    <div
                                        class="md:col-span-2 flex flex-wrap items-center justify-between gap-6 pt-4 border-t border-slate-100 dark:border-white/5">
                                        <div class="flex items-center gap-6">
<label class="relative inline-flex items-center cursor-pointer"
                                                x-data="{ isActive: {{ $event->is_active ? 'true' : 'false' }} }">
                                                <input type="checkbox" name="is_active" {{ $event->is_active ? 'checked' : '' }}
                                                    value="1" class="sr-only peer" @change="isActive = !isActive">
                                                <div
                                                    class="w-10 h-5 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"
                                                    :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }">
                                                </div>
                                                <span
                                                    class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Active</span>
                                            </label>

                                            <div class="flex items-center gap-2">
                                                <i class="ri-link text-slate-400"></i>
                                                <input type="url" name="link_url" value="{{ $event->link_url }}"
                                                    class="bg-transparent border-b border-slate-200 dark:border-white/10 p-0 text-[11px] focus:ring-0 w-48 truncate outline-none transition-all"
                                                    style="color: var(--primary-color);"
                                                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                    @blur="$el.style.borderColor = ''"
                                                    placeholder="No link set">
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <button type="submit"
                                                class="bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md hover:bg-slate-800 dark:hover:bg-slate-200 transition-all active:scale-95">
                                                Save Changes
                                            </button>
                                            <button type="button"
                                                onclick="if(confirm('Data event akan dihapus permanen. Lanjutkan?')) document.getElementById('delete-form-{{ $event->id }}').submit();"
                                                class="bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md hover:bg-red-500 hover:text-white transition-all active:scale-95">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form id="delete-form-{{ $event->id }}" action="{{ route('admin.cms.event.destroy', $event->id) }}"
                                method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
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
    </style>
@endsection