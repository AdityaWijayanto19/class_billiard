@extends('layouts.admin')

@section('title', 'Manajemen Pro Tim - Admin')

@section('content')
    <!-- SWEETALERT2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300"
        x-data="{ showCreate: {{ $errors->any() ? 'true' : 'false' }} }">

        <!-- HEADER STANDARD -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" @mouseenter="$el.style.color = 'var(--primary-color)'" @mouseleave="$el.style.color = ''">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Professional <span
                        style="color: var(--primary-color);">Team</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Manajemen anggota pro tim, kualifikasi, dan urutan tampilan publik.</p>
            </div>

            <button @click="showCreate = !showCreate"
                class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm flex items-center gap-2 active:scale-95" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                <i :class="showCreate ? 'ri-close-line' : 'ri-user-add-line'" class="text-lg"></i>
                <span x-text="showCreate ? 'Batalkan' : 'Tambah Pro Tim'"></span>
            </button>
        </div>

        <!-- FLASH MESSAGE HANDLING WITH SWEETALERT2 -->
        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#fbbf24',
                    timer: 3000
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'GAGAL',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444'
                });
            </script>
        @endif

        @if($errors->any())
            <div class="mb-8 bg-red-500/10 border border-red-500/20 px-4 py-3 rounded-md">
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-error-warning-fill text-red-500"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest text-red-500">Validation Error</span>
                </div>
                <ul class="list-disc list-inside text-xs text-red-500">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- CREATION MODULE -->
        <div x-show="showCreate" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0" class="mb-12" style="display: none;">
            <div class="bg-slate-50 dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8">
                <h2 class="text-[10px] font-black uppercase tracking-[0.2em] mb-8" style="color: var(--primary-color);">Registrasi Pro Tim Baru</h2>
                <form action="{{ route('admin.cms.pro-tim.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Nama Lengkap</label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Umur</label>
                            <input type="number" name="age" value="{{ old('age') }}"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Asal / Kota</label>
                            <input type="text" name="origin" value="{{ old('origin') }}"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Order</label>
                            <input type="number" name="order" value="{{ old('order', 0) }}"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                        </div>

                        <div class="lg:col-span-4 space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Alamat Lengkap</label>
                            <textarea name="address" rows="2"
                                class="w-full bg-white dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2.5 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''"
                                placeholder="Tuliskan alamat lengkap anggota...">{{ old('address') }}</textarea>
                        </div>

                        <div class="lg:col-span-4 flex items-center justify-between pt-6 border-t border-slate-100 dark:border-white/5">
                            <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: true }">
                                <input type="checkbox" name="is_active" checked value="1" class="sr-only peer" @change="isActive = !isActive">
                                <div class="w-11 h-6 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all"
                                    :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }"></div>
                                <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Status Aktif</span>
                            </label>
                            <button type="submit"
                                class="text-black text-[10px] font-black uppercase tracking-widest py-4 px-12 rounded-md transition-all active:scale-95 shadow-sm" style="background-color: var(--primary-color);" @mouseenter="$el.style.opacity = '0.85'" @mouseleave="$el.style.opacity = '1'">
                                Simpan Data Pro
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- PRO TEAM DIRECTORY -->
        <div class="space-y-6">
            <h2 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-gray-500 mb-6">Active Pro Team Directory</h2>

            <div class="grid grid-cols-1 gap-6">
                @forelse($proTeams as $team)
                    <div class="group bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden flex flex-col lg:flex-row transition-all duration-300" @mouseenter="$el.style.borderColor = 'rgba(var(--primary-color-rgb), 0.5)'" @mouseleave="$el.style.borderColor = ''">

                        <!-- Profile Avatar Section -->
                        <div class="w-full lg:w-48 h-48 lg:h-auto bg-slate-50 dark:bg-white/[0.02] flex items-center justify-center p-6 border-r border-slate-100 dark:border-white/5">
                            <div class="relative w-full aspect-square max-w-[120px]">
                                <div class="w-full h-full rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 dark:from-white/5 dark:to-white/10 flex items-center justify-center border border-slate-200 dark:border-white/10 group-hover:scale-105 transition-transform duration-500">
                                    <span class="text-4xl font-black text-slate-300 dark:text-white/20">{{ strtoupper(substr($team->name, 0, 1)) }}</span>
                                </div>
                                <div class="absolute -bottom-2 -right-2 text-black w-6 h-6 rounded-md flex items-center justify-center text-[10px] font-bold border-2 border-white dark:border-[#0A0A0A]" style="background-color: var(--primary-color);">
                                    {{ $team->order }}
                                </div>
                            </div>
                        </div>

                        <!-- Data Form Section -->
                        <div class="flex-1 p-8">
                            <form action="{{ route('admin.cms.pro-tim.update', $team->id) }}" method="POST" id="update-form-{{ $team->id }}">
                                @csrf
                                @method('PUT')
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Nama Lengkap</label>
                                        <input type="text" name="name" value="{{ $team->name }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Asal / Kota</label>
                                        <input type="text" name="origin" value="{{ $team->origin }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm font-bold outline-none" style="color: var(--primary-color);">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Umur</label>
                                        <input type="number" name="age" value="{{ $team->age }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Sort Order</label>
                                        <input type="number" name="order" value="{{ $team->order }}"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none">
                                    </div>
                                    <div class="md:col-span-2 lg:col-span-4 space-y-2">
                                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Alamat Lengkap</label>
                                        <textarea name="address" rows="2"
                                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-2 text-sm text-slate-900 dark:text-white outline-none transition-all" @focus="$el.style.borderColor = 'var(--primary-color)'" @blur="$el.style.borderColor = ''">{{ $team->address }}</textarea>
                                    </div>

                                    <!-- Final Actions -->
                                    <div class="lg:col-span-4 flex items-center justify-between pt-6 mt-2 border-t border-slate-100 dark:border-white/5">
                                        <label class="relative inline-flex items-center cursor-pointer" x-data="{ isActive: {{ $team->is_active ? 'true' : 'false' }} }">
                                            <input type="checkbox" name="is_active" {{ $team->is_active ? 'checked' : '' }}
                                                value="1" class="sr-only peer" @change="isActive = !isActive">
                                            <div class="w-10 h-5 bg-slate-200 dark:bg-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"
                                                :style="{ backgroundColor: isActive ? 'var(--primary-color)' : '#cbd5e1' }"></div>
                                            <span class="ml-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Visible on Site</span>
                                        </label>

                                        <div class="flex items-center gap-3">
                                            <button type="submit"
                                                class="bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-2.5 px-8 rounded-md hover:bg-slate-800 dark:hover:bg-slate-200 transition-all active:scale-95 shadow-sm">
                                                Update Data
                                            </button>
                                            <button type="button"
                                                onclick="confirmDelete('{{ $team->id }}', '{{ $team->name }}')"
                                                class="bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md hover:bg-red-500 hover:text-white transition-all active:scale-95">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <form id="delete-form-{{ $team->id }}" action="{{ route('admin.cms.pro-tim.destroy', $team->id) }}"
                                method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 border-2 border-dashed border-slate-100 dark:border-white/5 rounded-xl">
                        <i class="ri-user-search-line text-4xl text-slate-300 dark:text-white/10 mb-4 block"></i>
                        <p class="text-xs font-black uppercase tracking-widest text-slate-400">Belum ada data pro tim yang terdaftar</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        // Fungsi untuk mengambil warna primer secara dinamis dari CSS Variable
        const getPrimaryColor = () => {
            return getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim() || '#fbbf24';
        };

        // Fungsi Konfirmasi Hapus Dinamis
        function confirmDelete(id, name) {
            const primaryColor = getPrimaryColor();

            Swal.fire({
                title: 'HAPUS PERSONEL?',
                text: `Anda akan menghapus ${name} secara permanen.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: primaryColor, // WARNA DINAMIS
                cancelButtonColor: '#64748b',    // Slate 500
                confirmButtonText: 'YA, HAPUS',
                cancelButtonText: 'BATAL',
                background: document.documentElement.classList.contains('dark') ? '#0A0A0A' : '#ffffff',
                color: document.documentElement.classList.contains('dark') ? '#ffffff' : '#0f172a',
                customClass: {
                    popup: 'border border-slate-200 dark:border-white/10 rounded-xl',
                    title: 'text-sm font-black uppercase tracking-widest',
                    htmlContainer: 'text-xs opacity-70',
                    confirmButton: 'text-[10px] font-black tracking-[0.2em] px-6 py-3 uppercase',
                    cancelButton: 'text-[10px] font-black tracking-[0.2em] px-6 py-3 uppercase'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        input:focus, textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px rgba(var(--primary-color-rgb), 0.1) !important;
        }

        /* Swall Custom Overrides */
        .swal2-popup {
            border-radius: 12px !important;
            border: 1px solid rgba(255,255,255,0.05);
        }
    </style>
@endsection