@extends('layouts.admin')

@section('title', 'Manajemen Profil')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Account <span
                        style="color: var(--primary-color);">Profile</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen identitas kredensial dan
                    preferensi keamanan sistem.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- SIDEBAR: PROFILE SUMMARY -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Profile Card -->
                <div
                    class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 text-center group transition-all duration-300">
                    <div class="relative inline-block mb-6">
                        <div
                            class="w-24 h-24 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/[0.02] p-1.5 transition-transform duration-500 group-hover:scale-105">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=transparent&color={{ str_replace('#', '', auth()->user()->primary_color) }}&size=128&bold=true"
                                class="w-full h-full rounded-md object-cover grayscale group-hover:grayscale-0 transition-all duration-500"
                                alt="User Avatar">
                        </div>
                        <div
                            class="absolute -bottom-2 -right-2 w-6 h-6 rounded-md flex items-center justify-center text-black border-2 border-white dark:border-[#0A0A0A]"
                            style="background-color: var(--primary-color);">
                            <i class="ri-shield-check-line text-xs"></i>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">{{ $user->name }}</h3>
                    <p class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-[0.2em] mt-1">
                        {{ $user->email }}</p>

                    @php
                        $role = $user->getRoleNames()->first() ?? 'no-role';
                    @endphp
                    <div
                        class="mt-6 inline-flex items-center px-4 py-1.5 rounded-md bg-slate-50 dark:bg-white/[0.03] border border-slate-200 dark:border-white/10 text-[9px] font-black uppercase tracking-widest"
                        style="color: var(--primary-color);">
                        {{ ucfirst(str_replace('_', ' ', $role)) }} ACCESS LEVEL
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-6">
                    <h4 class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-6">
                        Account Metadata</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span
                                class="text-[11px] font-bold text-slate-500 dark:text-gray-400 uppercase tracking-tight">Registered
                                Since</span>
                            <span
                                class="text-xs text-slate-900 dark:text-white font-black">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-slate-100 dark:border-white/5">
                            <span
                                class="text-[11px] font-bold text-slate-500 dark:text-gray-400 uppercase tracking-tight">Security
                                Status</span>
                            <span
                                class="flex items-center gap-2 text-emerald-500 text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Verified
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN FORMS -->
            <div class="lg:col-span-8 space-y-8">

                <!-- Information Form -->
                <div
                    class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-slate-200 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/[0.01]">
                        <div class="space-y-1">
                            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">
                                Informasi Dasar</h2>
                            <p class="text-[10px] font-medium text-slate-500 dark:text-gray-500 uppercase tracking-tight">
                                Identitas publik administratif sistem.</p>
                        </div>
                        <i class="ri-user-smile-line text-2xl opacity-20" style="color: var(--primary-color);"></i>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" class="p-8 space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">Nama
                                    Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all duration-300\"
                                    @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                                    @blur=\"$el.style.borderColor = ''\">
                                    @focus=\"$el.style.borderColor = 'var(--primary-color)'\"
                                    @blur=\"$el.style.borderColor = ''\">
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">Email
                                    System</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all duration-300" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-10 rounded-md transition-all active:scale-95 shadow-sm\"
                                style=\"background-color: var(--primary-color);\"
                                @mouseenter=\"$el.style.opacity = '0.85'\"
                                @mouseleave=\"$el.style.opacity = '1'\">
                                Commit Changes
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Color Preference Form -->
                <div
                    class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-slate-200 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/[0.01]">
                        <div class="space-y-1">
                            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">
                                Preferensi Warna</h2>
                            <p class="text-[10px] font-medium text-slate-500 dark:text-gray-500 uppercase tracking-tight">
                                Pilih warna accent utama dashboard sesuai preferensi.</p>
                        </div>
                        <i class="ri-palette-line text-2xl opacity-20" style="color: var(--primary-color);"></i>
                    </div>

                    <form action="{{ route('admin.profile.update') }}" method="POST" class="p-8 space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">
                                Pilih Warna Accent Utama
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Color Option 1: Gold -->
                                <label class="group relative cursor-pointer">
                                    <input type="radio" name="primary_color" value="#fbbf24" 
                                        {{ (auth()->user()->primary_color ?? '#fa9a08') === '#fbbf24' ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="p-6 border-2 border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-white/[0.02] transition-all duration-300 peer-checked:border-[#fbbf24] peer-checked:bg-yellow-50 dark:peer-checked:bg-yellow-950/20 hover:border-yellow-300 dark:hover:border-yellow-700">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-full h-16 rounded-md bg-gradient-to-r from-yellow-300 to-yellow-400 border border-yellow-200 dark:border-yellow-900 shadow-md"></div>
                                            <div class="text-center">
                                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Gold</p>
                                                <p class="text-[10px] text-slate-500 dark:text-gray-400 uppercase tracking-widest font-medium mt-1">#fbbf24</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-[#fbbf24] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="ri-check-line text-white text-xs"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Color Option 2: Orange (Current) -->
                                <label class="group relative cursor-pointer">
                                    <input type="radio" name="primary_color" value="#fa9a08" 
                                        {{ (auth()->user()->primary_color ?? '#fa9a08') === '#fa9a08' ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="p-6 border-2 border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-white/[0.02] transition-all duration-300 peer-checked:border-[#fa9a08] peer-checked:bg-orange-50 dark:peer-checked:bg-orange-950/20 hover:border-orange-300 dark:hover:border-orange-700">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-full h-16 rounded-md bg-gradient-to-r from-orange-400 to-orange-500 border border-orange-300 dark:border-orange-800 shadow-md"></div>
                                            <div class="text-center">
                                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Orange</p>
                                                <p class="text-[10px] text-slate-500 dark:text-gray-400 uppercase tracking-widest font-medium mt-1">#fa9a08 (Current)</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-[#fa9a08] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="ri-check-line text-white text-xs"></i>
                                        </div>
                                    </div>
                                </label>

                                <!-- Color Option 3: Elegant Teal -->
                                <label class="group relative cursor-pointer">
                                    <input type="radio" name="primary_color" value="#2f7d7a" 
                                        {{ (auth()->user()->primary_color ?? '#fa9a08') === '#2f7d7a' ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="p-6 border-2 border-slate-200 dark:border-white/10 rounded-lg bg-slate-50 dark:bg-white/[0.02] transition-all duration-300 peer-checked:border-[#2f7d7a] peer-checked:bg-teal-50 dark:peer-checked:bg-teal-950/20 hover:border-teal-300 dark:hover:border-teal-700">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="w-full h-16 rounded-md bg-gradient-to-r from-teal-600 to-teal-700 border border-teal-500 dark:border-teal-900 shadow-md"></div>
                                            <div class="text-center">
                                                <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">Elegant</p>
                                                <p class="text-[10px] text-slate-500 dark:text-gray-400 uppercase tracking-widest font-medium mt-1">#2f7d7a</p>
                                            </div>
                                        </div>
                                        <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-[#2f7d7a] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                            <i class="ri-check-line text-white text-xs"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="btn-primary text-black text-[10px] font-black uppercase tracking-widest py-3 px-10 rounded-md transition-all active:scale-95 shadow-sm"
                                style="background-color: var(--primary-color);">
                                Simpan Preferensi Warna
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Password Form -->
                <div
                    class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden">
                    <div
                        class="px-8 py-6 border-b border-slate-200 dark:border-white/5 flex justify-between items-center bg-slate-50/50 dark:bg-white/[0.01]">
                        <div class="space-y-1">
                            <h2 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest">Keamanan
                                Password</h2>
                            <p class="text-[10px] font-medium text-slate-500 dark:text-gray-500 uppercase tracking-tight">
                                Gunakan kombinasi alfanumerik yang kompleks.</p>
                        </div>
                        <i class="ri-lock-password-line text-2xl text-red-500 opacity-20"></i>
                    </div>

                    <form action="{{ route('admin.profile.password') }}" method="POST" class="p-8 space-y-8">
                        @csrf
                        @method('PUT')

                        <div class="space-y-8">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">Current
                                    Password</label>
                                <input type="password" name="current_password" required
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all duration-300" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">New
                                        Password</label>
                                    <input type="password" name="password" required
                                        class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all duration-300" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">Confirm
                                        New Password</label>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md py-3 px-4 text-sm text-slate-900 dark:text-white outline-none transition-all duration-300" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-3 px-10 rounded-md border border-slate-900 dark:border-white hover:bg-slate-800 dark:hover:bg-slate-100 transition-all active:scale-95 shadow-sm">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Override SweetAlert2 styling to match Manifesto */
        .swal2-popup-custom {
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
            border-radius: 8px !important;
            padding: 2rem !important;
        }

        .swal2-confirm-custom {
            font-size: 10px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.1em !important;
            border-radius: 6px !important;
            padding: 0.8rem 2rem !important;
        }
    </style>
@endsection

@push('scripts')
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: '<span class="text-white font-bold tracking-tight uppercase">SYTEM UPDATED</span>',
                html: '<p class="text-slate-500 text-[11px] font-bold uppercase tracking-widest leading-relaxed">{{ session('success') }}</p>',
                background: '#0A0A0A',
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                customClass: {
                    popup: 'swal2-popup-custom',
                    confirmButton: 'swal2-confirm-custom'
                }
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: '<span class="text-white font-bold tracking-tight uppercase">VALIDATION ERROR</span>',
                html: '<ul class="text-red-400 text-[10px] text-left list-none space-y-1 uppercase font-black tracking-widest">@foreach($errors->all() as $error)<li>— {{ $error }}</li>@endforeach</ul>',
                background: '#0A0A0A',
                confirmButtonColor: '#ef4444',
                customClass: {
                    popup: 'swal2-popup-custom',
                    confirmButton: 'swal2-confirm-custom'
                }
            });
        </script>
    @endif
@endpush