@extends('layouts.admin')

@section('title', 'Manajemen Tim - Admin')

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
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Personnel <span
                        style="color: var(--primary-color);">Directory</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen profil profesional, hierarki, dan
                    identitas digital anggota tim.</p>
            </div>

            <button @click="showCreate = !showCreate"
                class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm flex items-center gap-2 active:scale-95" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                <i :class="showCreate ? 'ri-close-line' : 'ri-user-add-line'" class="text-lg"></i>
                <span x-text="showCreate ? 'Batalkan' : 'Tambah Anggota Tim'"></span>
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
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8" style="color: var(--primary-color);">Registrasi Personel Baru
                </h2>
                <form action="{{ route('admin.cms.tim-kami.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Full
                                Name</label>
                            <input type="text" name="name" required
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Professional
                                Position</label>
                            <input type="text" name="position" required
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Profile
                                Photo</label>
                            <input type="file" name="photo" accept="image/*"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-[10px] file:font-black file:uppercase file:text-black file:cursor-pointer file:transition-all">
                        </div>
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Order</label>
                            <input type="number" name="order" value="0"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>

                        <div class="lg:col-span-4 space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Bio / Short Description</label>
                            <textarea name="bio" rows="3"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="Short biography or description about the team member..."></textarea>
                        </div>

                        <div
                            class="lg:col-span-4 grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100 dark:border-white/5">
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400"><i
                                        class="ri-facebook-fill mr-1" style="color: var(--primary-color);"></i> Facebook URL</label>
                                <input type="url" name="facebook_url"
                                    class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-xs text-slate-900 dark:text-white outline-none" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400"><i
                                        class="ri-instagram-line mr-1" style="color: var(--primary-color);"></i> Instagram URL</label>
                                <input type="url" name="instagram_url"
                                    class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-xs text-slate-900 dark:text-white outline-none" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[9px] font-black uppercase tracking-widest text-slate-400"><i
                                        class="ri-linkedin-fill mr-1" style="color: var(--primary-color);"></i> LinkedIn URL</label>
                                <input type="url" name="linkedin_url"
                                    class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-xs text-slate-900 dark:text-white outline-none" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                            </div>
                        </div>

                        <div
                            class="lg:col-span-4 flex items-center justify-between pt-6 border-t border-slate-100 dark:border-white/5">
                            <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: true }">
                                <input type="checkbox" name="is_active" checked value="1" class="sr-only peer" @change="isActive = !isActive">
                                <div
                                    class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                    :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }"></div>
                                <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Status
                                    Aktif</span>
                            </label>
                            <button type="submit"
                                class="text-black text-[10px] font-black uppercase tracking-widest py-4 px-12 rounded-md transition-all active:scale-95 shadow-sm" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                                Onboard Personnel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- PERSONNEL DIRECTORY -->
        <div class="space-y-6">
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500 mb-6">Active Team
                Directory</h2>

            <div class="grid grid-cols-1 gap-6">
                @foreach($timKami as $member)
                    <div
                        class="group bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden flex flex-col lg:flex-row transition-all duration-300" @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.5)'" @mouseleave="$el.style.borderColor = ''">

                        <!-- Profile Image Section -->
                        <div
                            class="w-full lg:w-48 h-48 lg:h-auto bg-slate-50 dark:bg-white/[0.02] flex items-center justify-center p-6 border-r border-slate-100 dark:border-white/5">
                            <div class="relative w-full aspect-square max-w-[120px]">
                                @if($member->photo)
                                    <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->name }}"
                                        class="w-full h-full rounded-lg object-cover border border-slate-200 dark:border-white/10 group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div
                                        class="w-full h-full rounded-lg bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-300">
                                        <i class="ri-user-3-line text-4xl"></i>
                                    </div>
                                @endif
                                <div
                                    class="absolute -bottom-2 -right-2 text-black w-6 h-6 rounded-md flex items-center justify-center text-[10px] font-bold border-2 border-white dark:border-[#0A0A0A]" style="background-color: var(--primary-color);">
                                    {{ $member->order }}
                                </div>
                            </div>
                        </div>

                        <!-- Data Form Section -->
                        <div class="flex-1 p-8">
                            <form action="{{ route('admin.cms.tim-kami.update', $member->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Full
                                            Name</label>
                                        <input type="text" name="name" value="{{ $member->name }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Position</label>
                                        <input type="text" name="position" value="{{ $member->position }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm font-bold outline-none" style="color: var(--primary-color);">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Update
                                            Photo</label>
                                        <input type="file" name="photo" accept="image/*"
                                            class="text-[10px] text-slate-400 file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-[9px] file:font-black file:uppercase file:text-black file:cursor-pointer file:transition-all">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Sort
                                            Order</label>
                                        <input type="number" name="order" value="{{ $member->order }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none">
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-4 space-y-2">
                                        <label
                                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Bio / Short Description</label>
                                        <textarea name="bio" rows="2"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">{{ $member->bio ?? '' }}</textarea>
                                    </div>

                                    <!-- Social Mini Inputs -->
                                    <div
                                        class="lg:col-span-4 flex flex-wrap gap-4 pt-4 border-t border-slate-100 dark:border-white/5">
                                        <div
                                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-white/[0.01] border border-slate-200 dark:border-white/10 rounded-md">
                                            <i class="ri-facebook-fill text-slate-400"></i>
                                            <input type="url" name="facebook_url" value="{{ $member->facebook_url }}"
                                                placeholder="Facebook URL"
                                                class="bg-transparent border-none p-0 text-[10px] text-slate-500 w-32 focus:ring-0">
                                        </div>
                                        <div
                                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-white/[0.01] border border-slate-200 dark:border-white/10 rounded-md">
                                            <i class="ri-instagram-line text-slate-400"></i>
                                            <input type="url" name="instagram_url" value="{{ $member->instagram_url }}"
                                                placeholder="Instagram URL"
                                                class="bg-transparent border-none p-0 text-[10px] text-slate-500 w-32 focus:ring-0">
                                        </div>
                                        <div
                                            class="flex items-center gap-2 px-3 py-1.5 bg-slate-50 dark:bg-white/[0.01] border border-slate-200 dark:border-white/10 rounded-md">
                                            <i class="ri-linkedin-fill text-slate-400"></i>
                                            <input type="url" name="linkedin_url" value="{{ $member->linkedin_url }}"
                                                placeholder="LinkedIn URL"
                                                class="bg-transparent border-none p-0 text-[10px] text-slate-500 w-32 focus:ring-0">
                                        </div>
                                    </div>

                                    <!-- Final Actions -->
                                    <div
                                        class="lg:col-span-4 flex items-center justify-between pt-6 mt-2 border-t border-slate-100 dark:border-white/5">
                                        <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: {{ $member->is_active ? 'true' : 'false' }} }">
                                            <input type="checkbox" name="is_active" {{ $member->is_active ? 'checked' : '' }}
                                                value="1" class="sr-only peer" @change="isActive = !isActive">
                                            <div
                                                class="w-10 h-5 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"
                                                :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }"></div>
                                            <span
                                                class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Visible
                                                on Site</span>
                                        </label>

                                        <div class="flex items-center gap-3">
                                            <button type="submit"
                                                class="bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-2.5 px-8 rounded-md hover:bg-slate-800 dark:hover:bg-slate-200 transition-all active:scale-95 shadow-sm">
                                                Update Profile
                                            </button>
                                            <button type="button"
                                                onclick="if(confirm('Akses personel akan dicabut permanen. Lanjutkan?')) document.getElementById('delete-form-{{ $member->id }}').submit();"
                                                class="bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md hover:bg-red-500 hover:text-white transition-all active:scale-95">
                                                Offboard
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form id="delete-form-{{ $member->id }}" action="{{ route('admin.cms.tim-kami.destroy', $member->id) }}"
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

        input:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px rgba(var(--primary-color-rgb), 0.1) !important;
        }
        
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
    </style>
@endsection