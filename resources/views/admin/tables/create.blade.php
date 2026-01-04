@extends('layouts.admin')

@section('title', 'Tambah Meja Baru')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10">

        <!-- HEADER STANDARD -->
        <div
            class="max-w-2xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.tables.index') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2" style="hover:color: var(--primary-color);">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Kembali ke Daftar
                    Meja
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Registrasi <span style="color: var(--primary-color);">Meja Baru</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Inisialisasi identitas meja untuk pembuatan
                    QR Code otomatis.</p>
            </div>
        </div>

        <!-- FORM CONTAINER -->
        <div class="max-w-2xl mx-auto">
            <div
                class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 shadow-sm transition-all duration-300">

                <form action="{{ route('admin.tables.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- ICON INDICATOR -->
                    <div class="flex justify-center pb-4">
                        <div
                            class="w-16 h-16 bg-slate-50 dark:bg-white/[0.02] rounded-md border border-slate-200 dark:border-white/10 flex items-center justify-center">
                            <i class="ri-qr-scan-2-line text-3xl" style="color: var(--primary-color);"></i>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Input Nama Meja -->
                        <div class="space-y-2">
                            <label for="name"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">
                                Nama / Nomor Unit Meja
                            </label>
                            <input type="text" name="name" id="name"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 p-4 rounded-md focus:outline-none transition-all text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-gray-600 text-base font-bold"
                                placeholder="Contoh: Meja 01 atau VIP 05" required autofocus value="{{ old('name') }}">
                            @error('name')
                                <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 ml-1">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Input Ruangan -->
                        <div class="space-y-2">
                            <label for="room"
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500 ml-1">
                                Alokasi Ruangan / Area
                            </label>
                            <input type="text" name="room" id="room"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 p-4 rounded-md focus:outline-none transition-all text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-gray-600 text-base font-bold"
                                placeholder="Contoh: Ruang A, VIP Room, Outdoor" required value="{{ old('room') }}">
                            @error('room')
                                <p class="text-[10px] text-red-500 font-bold uppercase tracking-tight mt-1 ml-1">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- INFO BOX STANDARD -->
                        <div class="bg-blue-500/5 border border-blue-500/10 p-5 rounded-md flex gap-4 items-start">
                            <i class="ri-information-line text-blue-500 text-xl shrink-0"></i>
                            <div class="space-y-1">
                                <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Sistem Otomasi</p>
                                <p class="text-[11px] text-slate-500 dark:text-gray-500 leading-relaxed">
                                    Setelah disimpan, sistem akan men-generate <span
                                        class="text-slate-900 dark:text-white font-bold">Slug Unique</span> dan <span
                                        class="text-slate-900 dark:text-white font-bold">QR Code</span> yang mengarah ke
                                    link pesanan digital meja ini.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-slate-100 dark:border-white/5">
                        <button type="reset"
                            class="flex-1 py-4 rounded-md border border-slate-200 dark:border-white/10 text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-white/5 hover:text-slate-900 dark:hover:text-white transition-all duration-300">
                            Reset Form
                        </button>
                        <button type="submit"
                            class="flex-[2] py-4 rounded-md text-black text-[10px] font-black uppercase tracking-widest transition-all shadow-sm active:scale-95 flex items-center justify-center gap-2 btn-primary" style="background-color: var(--primary-color);">
                            <i class="ri-qr-code-line text-lg"></i>
                            Generate & Simpan Meja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Focus State for Sleek Inputs */
        input:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px var(--primary-color) !important;
        }
    </style>
@endsection