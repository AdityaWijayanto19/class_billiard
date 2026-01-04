@extends('layouts.admin')

@section('title', 'Portfolio & Achievement - Admin')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300"
        x-data="{ showCreate: false }">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Portfolio <span style="color: var(--primary-color);">& Achievements</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen rekam jejak, pencapaian numerik,
                    dan galeri visual entitas.</p>
            </div>

            <button @click="showCreate = !showCreate"
                class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm flex items-center gap-2 active:scale-95" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                <i :class="showCreate ? 'ri-close-line' : 'ri-add-line'" class="text-lg"></i>
                <span x-text="showCreate ? 'Discard Item' : 'Create New Item'"></span>
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

        <!-- CREATION MODULE -->
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-12">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8">
                <h2 class="text-sm font-black uppercase tracking-[0.2em] mb-8" style="color: var(--primary-color);">Add New Achievement</h2>
                <form action="{{ route('admin.cms.portfolio-achievement.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="gallery">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Image Upload -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Achievement Image <span class="text-red-500">*</span></label>
                            <input type="file" name="image" accept="image/*" required
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:text-black file:cursor-pointer file:transition-all">
                            <p class="text-[9px] text-slate-400 dark:text-gray-600 italic">Image akan ditampilkan di dashboard achievements section</p>
                        </div>

                        <!-- Title -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                @blur="$el.style.borderColor = ''"
                                placeholder="e.g. Championship Winner">
                            <p class="text-[9px] text-slate-400 dark:text-gray-600 italic">Judul yang ditampilkan di card achievement</p>
                        </div>

                        <!-- Description -->
                        <div class="space-y-2 md:col-span-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                @blur="$el.style.borderColor = ''"
                                placeholder="Deskripsi pencapaian..."></textarea>
                        </div>

                        <!-- Order -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Sort Order</label>
                            <input type="number" name="order" value="0"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white outline-none transition-all" 
                                @focus="$el.style.borderColor = 'var(--primary-color)'" 
                                @blur="$el.style.borderColor = ''">
                            <p class="text-[9px] text-slate-400 dark:text-gray-600 italic">Urutan tampil (0 = pertama, semakin besar semakin akhir)</p>
                        </div>

                        <!-- Is Active -->
                        <div class="space-y-2 flex items-end">
                            <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: true }">
                                <input type="checkbox" name="is_active" value="1" checked class="sr-only peer" @change="isActive = !isActive">
                                <div
                                    class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                    :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }">
                                </div>
                                <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="mt-10 flex justify-end">
                        <button type="submit"
                            class="text-black text-[10px] font-black uppercase tracking-widest py-4 px-12 rounded-md transition-all active:scale-95 shadow-sm" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                            Confirm Registration
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DATA GRID -->
        <div class="space-y-8">
            <!-- ACHIEVEMENTS SECTION -->
            <section>
                <h2
                    class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500 mb-8 flex items-center gap-4">
                    Achievements Library
                    <span class="h-px flex-1 bg-slate-100 dark:bg-white/5"></span>
                    <span class="text-[9px] text-slate-500 dark:text-gray-600">({{ $allAchievements->count() }} items)</span>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($allAchievements as $item)
                        <div
                            class="group bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden flex flex-col transition-all duration-300" @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.5)'" @mouseleave="$el.style.borderColor = ''">
                            <!-- Image Preview -->
                            <div class="aspect-video bg-slate-100 dark:bg-white/5 relative overflow-hidden">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400 dark:text-gray-600">
                                        <i class="ri-image-line text-4xl"></i>
                                    </div>
                                @endif
                                <div
                                    class="absolute top-2 right-2 bg-black/70 text-white text-[8px] font-black uppercase px-2 py-1 rounded">
                                    Order: {{ $item->order }}
                                </div>
                            </div>
                            
                            <!-- Form Content -->
                            <div class="p-6 flex flex-col flex-1 space-y-6">
                                <form action="{{ route('admin.cms.portfolio-achievement.update', $item->id) }}" method="POST"
                                    enctype="multipart/form-data" class="space-y-6 flex-1 flex flex-col">
                                    @csrf
                                    <input type="hidden" name="type" value="gallery">
                                    
                                    <div class="space-y-4 flex-1">
                                        <!-- Title -->
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Title <span class="text-red-500">*</span></label>
                                            <input type="text" name="title" value="{{ $item->title ?? $item->label }}" required
                                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                @blur="$el.style.borderColor = ''"
                                                placeholder="e.g. Championship Winner">
                                        </div>

                                        <!-- Description -->
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Description</label>
                                            <textarea name="description" rows="2"
                                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-2 text-xs text-slate-900 dark:text-white outline-none transition-all resize-none"
                                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                @blur="$el.style.borderColor = ''">{{ $item->description ?? '' }}</textarea>
                                        </div>

                                        <!-- Order -->
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Sort Order</label>
                                            <input type="number" name="order" value="{{ $item->order }}"
                                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-3 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all"
                                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                                @blur="$el.style.borderColor = ''">
                                        </div>

                                        <!-- Image Upload -->
                                        <div class="space-y-2">
                                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Change Image</label>
                                            <input type="file" name="image" accept="image/*"
                                                class="w-full text-[8px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[8px] file:font-black file:uppercase file:tracking-widest file:text-black file:cursor-pointer file:transition-all">
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex flex-col gap-4 pt-4 border-t border-slate-100 dark:border-white/5">
                                        <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: {{ $item->is_active ? 'true' : 'false' }} }">
                                            <input type="checkbox" name="is_active" {{ $item->is_active ? 'checked' : '' }}
                                                value="1" class="sr-only peer" @change="isActive = !isActive">
                                            <div
                                                class="w-10 h-5 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"
                                                :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }">
                                            </div>
                                            <span class="ml-2 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Active</span>
                                        </label>
                                        <div class="flex gap-2 w-full">
                                            <button type="submit"
                                                class="flex-1 text-black text-[9px] font-black uppercase tracking-widest py-2 px-6 rounded-md transition-all active:scale-95" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">Update</button>
                                            <button type="button"
                                                onclick="if(confirm('Delete achievement?')) document.getElementById('delete-form-{{ $item->id }}').submit();"
                                                class="flex-1 bg-red-500/10 text-red-500 text-[9px] font-black uppercase tracking-widest py-2 px-6 rounded-md transition-all active:scale-95 hover:bg-red-500/20">Delete</button>
                                        </div>
                                    </div>
                                </form>
                                <form id="delete-form-{{ $item->id }}"
                                    action="{{ route('admin.cms.portfolio-achievement.destroy', $item->id) }}" method="POST"
                                    class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <i class="ri-inbox-line text-4xl text-slate-400 dark:text-gray-600 mb-4"></i>
                            <p class="text-sm text-slate-500 dark:text-gray-500">No achievements yet. Create your first achievement above.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        input:focus,
        select:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px rgba(var(--primary-color-rgb), 0.1) !important;
        }
        
        input[type="file"]::file-selector-button {
            background-color: var(--primary-color);
            color: black;
            font-weight: bold;
        }
    </style>
@endsection