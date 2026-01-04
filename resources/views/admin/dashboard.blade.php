@extends('layouts.admin')

@section('title', 'System Overview')

@section('content')
    <div class="space-y-10 animate-in fade-in duration-700">

        <!-- ERROR ALERT: Shift Not Assigned -->
        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-lg flex items-start gap-3">
                <i class="ri-error-warning-fill text-red-500 text-xl mt-0.5"></i>
                <div>
                    <p class="text-sm font-bold text-red-600 dark:text-red-400">Perhatian!</p>
                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">{{ session('error') }}</p>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Shift Belum Di-Assign',
                        html: '<div class="text-left"><p class="text-sm text-gray-700 dark:text-gray-300 mb-3">Anda belum di-assign ke shift apapun.</p><p class="text-sm text-gray-700 dark:text-gray-300">Silakan hubungi Super Admin untuk di-assign ke shift terlebih dahulu sebelum dapat mengakses fitur operasional.</p></div>',
                        confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                        confirmButtonText: 'Mengerti',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        background: document.documentElement.classList.contains('dark') ? '#0A0A0A' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                        customClass: {
                            popup: 'rounded-lg border border-white/5',
                            confirmButton: 'rounded-md text-xs font-bold px-5 py-2.5'
                        }
                    });
                });
            </script>
        @endif

        <!-- HEADER: Professional & Utility-Focused -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8">
            <div class="space-y-1">
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    Dashboard Overview
                </h1>
                <p class="text-sm text-slate-500 dark:text-gray-400 font-medium">
                    Sistem Manajemen Konten Billiard Class versi 1.0.4
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-emerald-500/10 border border-emerald-500/20 rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <span
                        class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">System
                        Live</span>
                </div>
                <div class="h-8 w-[1px] bg-slate-200 dark:bg-white/10 mx-2"></div>
                <span class="text-xs font-medium text-slate-400">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>

        <!-- MODULE GRID: Clean & Precise Borders -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-gray-500">
                    Core Management Modules
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
                @php
                    $modules = [
                        ['r' => 'admin.cms.hero', 'i' => 'ri-image-2-line', 't' => 'Hero Section', 'd' => 'Konfigurasi visual utama dan headline halaman depan.'],
                        ['r' => 'admin.cms.tentang-kami', 'i' => 'ri-information-line', 'l' => 'Website CMS', 't' => 'Tentang Kami', 'd' => 'Visi, misi, dan manajemen video profil perusahaan.'],
                        ['r' => 'admin.cms.about-founder', 'i' => 'ri-user-star-line', 't' => 'About Founder', 'd' => 'Detail biografi dan profil profesional pendiri.'],
                        ['r' => 'admin.cms.keunggulan-fasilitas', 'i' => 'ri-medal-line', 't' => 'Keunggulan', 'd' => 'Daftar fasilitas pendukung dan nilai unik kelas.'],
                        ['r' => 'admin.cms.portfolio-achievement', 'i' => 'ri-trophy-line', 't' => 'Portfolio', 'd' => 'Dokumentasi prestasi dan galeri pencapaian.'],
                        ['r' => 'admin.cms.tim-kami', 'i' => 'ri-team-line', 't' => 'Tim Kami', 'd' => 'Manajemen data instruktur dan staf operasional.'],
                        ['r' => 'admin.cms.testimoni-pelanggan', 'i' => 'ri-chat-quote-line', 't' => 'Testimoni', 'd' => 'Moderasi ulasan dan feedback dari pelanggan.'],
                        ['r' => 'admin.cms.event', 'i' => 'ri-calendar-event-line', 't' => 'Event & Promo', 'd' => 'Penjadwalan turnamen dan penawaran khusus.'],
                        ['r' => 'admin.cms.footer', 'i' => 'ri-layout-bottom-line', 't' => 'Footer Info', 'd' => 'Informasi kontak, media sosial, dan copyright.'],
                    ];
                @endphp

                @foreach($modules as $m)
                    <a href="{{ route($m['r']) }}"
                        class="group relative bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 p-6 rounded-lg transition-all duration-300 flex items-start gap-5"
                        @mouseenter="$el.style.borderColor = 'var(--primary-color)'; $el.style.boxShadow = '0 10px 25px -5px rgba(var(--primary-color-rgb), 0.1)'"
                        @mouseleave="$el.style.borderColor = ''; $el.style.boxShadow = ''">

                        <!-- Icon: Sharp & Minimalist -->
                        <div
                            class="w-12 h-12 shrink-0 bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-md flex items-center justify-center text-slate-400 transition-all duration-300"
                            @mouseenter="$el.style.color = 'var(--primary-color)'; $el.style.backgroundColor = 'rgba(var(--primary-color-rgb), 0.05)'"
                            @mouseleave="$el.style.color = ''; $el.style.backgroundColor = ''">
                            <i class="{{ $m['i'] }} text-xl"></i>
                        </div>

                        <div class="space-y-1">
                            <h3
                                class="text-sm font-bold text-slate-900 dark:text-white transition-colors"
                                @mouseenter="$el.style.color = 'var(--primary-color)'"
                                @mouseleave="$el.style.color = ''">
                                {{ $m['t'] }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-gray-500 leading-relaxed font-medium">
                                {{ $m['d'] }}
                            </p>
                        </div>

                        <!-- Indicator: Subtle Corner Arrow -->
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="ri-arrow-right-up-line text-sm" style="color: var(--primary-color);"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- ADVANCED MANAGEMENT: Super Admin Only -->
        @if(auth()->user()->role === 'super_admin')
        <div class="space-y-6 pt-8 border-t border-slate-200 dark:border-white/5">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold uppercase tracking-widest text-slate-400 dark:text-gray-500">
                    Advanced Administration
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
                @php
                    $advancedModules = [
                        ['r' => 'admin.permissions.select-user', 'i' => 'ri-shield-check-line', 't' => 'Kelola Permissions', 'd' => 'Berikan hak akses lebih kepada user dengan mengelola permissions mereka secara detail.'],
                        ['r' => 'admin.manage-users.index', 'i' => 'ri-group-line', 't' => 'Manage Users', 'd' => 'Kelola user accounts, assign roles, dan atur shift untuk operasional.'],
                    ];
                @endphp

                @foreach($advancedModules as $m)
                    <a href="{{ route($m['r']) }}"
                        class="group relative bg-gradient-to-br from-purple-500/10 to-indigo-500/10 border border-purple-200 dark:border-purple-500/30 p-6 rounded-lg transition-all duration-300 hover:border-purple-500 hover:shadow-lg dark:hover:shadow-purple-500/20 flex items-start gap-5">

                        <!-- Icon: Sharp & Minimalist -->
                        <div
                            class="w-12 h-12 shrink-0 bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-500/30 rounded-md flex items-center justify-center text-purple-600 dark:text-purple-300 group-hover:text-purple-700 dark:group-hover:text-purple-200 group-hover:bg-purple-200 dark:group-hover:bg-purple-500/30 transition-all duration-300">
                            <i class="{{ $m['i'] }} text-xl"></i>
                        </div>

                        <div class="space-y-1">
                            <h3
                                class="text-sm font-bold text-purple-900 dark:text-purple-200 transition-colors group-hover:text-purple-700 dark:group-hover:text-purple-100">
                                {{ $m['t'] }}
                            </h3>
                            <p class="text-xs text-purple-700 dark:text-purple-300/70 leading-relaxed font-medium">
                                {{ $m['d'] }}
                            </p>
                        </div>

                        <!-- Indicator: Subtle Corner Arrow -->
                        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                            <i class="ri-arrow-right-up-line text-purple-600 dark:text-purple-300 text-sm"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- SYSTEM FOOTER: Balanced Spacing -->
        <div
            class="mt-12 p-8 border border-slate-200 dark:border-white/5 rounded-lg flex flex-col md:flex-row items-center justify-between gap-6 bg-slate-50/50 dark:bg-white/[0.02]">
            <div class="flex items-center gap-4 text-center md:text-left">
                <div
                    class="p-3 bg-white dark:bg-[#111] border border-slate-200 dark:border-white/10 rounded-md hidden sm:block">
                    <i class="ri-terminal-box-line text-2xl" style="color: var(--primary-color);"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold dark:text-white">Butuh Bantuan Teknis?</h4>
                    <p class="text-xs text-slate-500 dark:text-gray-500 font-medium mt-1">Tim developer kami siap membantu
                        operasional dashboard Anda.</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('home') }}"
                    class="px-5 py-2.5 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 text-xs font-bold rounded-md hover:bg-slate-50 dark:hover:bg-white/10 transition-all">
                    Preview Site
                </a>
                <button
                    class="px-5 py-2.5 text-black text-xs font-bold rounded-md transition-all shadow-md"
                    style="background-color: var(--primary-color);"
                    @mouseenter="$el.style.opacity = '0.85'"
                    @mouseleave="$el.style.opacity = '1'">
                    Contact Support
                </button>
            </div>
        </div>
    </div>

@endsection