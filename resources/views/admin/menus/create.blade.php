@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10">
        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <a href="{{ route('admin.menus.index') }}"
                    class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2"
                    @mouseenter="$el.style.color = 'var(--primary-color)'"
                    @mouseleave="$el.style.color = ''">
                    <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Kembali ke Galeri
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Tambah Hidangan Baru
                </h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Lengkapi detail informasi untuk publikasi
                    menu operasional baru.</p>
            </div>
        </div>

        <!-- ERROR HANDLING -->
        @if ($errors->any())
            <div class="mb-8 p-4 bg-red-500/5 border border-red-500/20 rounded-md">
                <div class="flex items-center gap-2 mb-2">
                    <i class="ri-error-warning-fill text-red-500"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Terjadi Kesalahan Input</span>
                </div>
                <ul class="list-none space-y-1">
                    @foreach ($errors->all() as $error)
                        <li class="text-[11px] text-red-400/80 font-medium leading-relaxed">— {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            @csrf

            <!-- LEFT COLUMN: MEDIA UPLOAD -->
            <div class="lg:col-span-4 space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Visual
                        Produk</label>
                    <div class="relative group aspect-square">
                        <input type="file" name="image" id="imgInput"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" required>
                        <div id="preview"
                            class="w-full h-full rounded-lg bg-slate-50 dark:bg-white/[0.02] border border-dashed border-slate-200 dark:border-white/10 flex flex-col items-center justify-center text-slate-400 transition-all duration-300 overflow-hidden"
                            @mouseenter="$el.style.borderColor = 'var(--primary-color)'"
                            @mouseleave="$el.style.borderColor = ''">
                            <i class="ri-image-add-line text-3xl mb-3 group-hover:scale-110 transition-transform"></i>
                            <p class="text-[10px] font-black uppercase tracking-tighter">Klik atau Drop Gambar</p>
                            <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-tighter">JPG, PNG (Max 2MB)</p>
                        </div>
                    </div>
                </div>

                <p class="text-[10px] text-slate-400 dark:text-gray-500 leading-relaxed italic">
                    *Pastikan gambar memiliki pencahayaan yang baik untuk menjaga standar estetika menu.
                </p>
            </div>

            <!-- RIGHT COLUMN: SPECIFICATIONS -->
            <div class="lg:col-span-8 space-y-8">
                <div
                    class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        <!-- Nama Menu -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Nama
                                Hidangan</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none"
                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                @blur="$el.style.borderColor = ''"
                                placeholder="Contoh: Wagyu Steak Premium">
                        </div>

                        <!-- Kategori -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Klasifikasi
                                Kategori</label>
                            <div class="relative">
                                <select name="category_menu_id" required
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none appearance-none cursor-pointer"
                                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                                    @blur="$el.style.borderColor = ''">
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_menu_id') == $cat->id ? 'selected' : '' }}
                                            class="bg-white dark:bg-[#0A0A0A]">
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <i
                                    class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Nilai
                                Jual (IDR)</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" id="price_display" required
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md pl-10 pr-4 py-3 text-sm font-bold focus:ring-0 transition-all outline-none"
                                    style="color: var(--primary-color);"
                                    @focus="$el.style.borderColor = 'var(--primary-color)'"
                                    @blur="$el.style.borderColor = ''"
                                    placeholder="0">
                                <input type="hidden" name="price" id="price_real" value="{{ old('price') }}">
                            </div>
                        </div>

                        <!-- Labels -->
                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Tagging/Labels</label>
                            <input type="text" name="labels" value="{{ old('labels') }}"
                                class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none"
                                @focus="$el.style.borderColor = 'var(--primary-color)'"
                                @blur="$el.style.borderColor = ''"
                                placeholder="Best Seller, Pedas, New">
                        </div>
                    </div>

                    <!-- Deskripsi -->
                    <div class="mt-8 space-y-2">
                        <label
                            class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Narasi
                            Deskripsi</label>
                        <textarea name="description" rows="4" required
                            class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none leading-relaxed"
                            @focus="$el.style.borderColor = 'var(--primary-color)'"
                            @blur="$el.style.borderColor = ''"
                            placeholder="Deskripsikan komposisi, rasa, dan keunikan hidangan ini secara profesional...">{{ old('description') }}</textarea>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-10 pt-8 border-t border-slate-200 dark:border-white/5">
                        <button type="submit"
                            class="w-full text-black text-[10px] font-black uppercase tracking-widest py-4 rounded-md transition-all shadow-sm flex items-center justify-center gap-3 active:scale-[0.98]"
                            style="background-color: var(--primary-color);"
                            @mouseenter="$el.style.opacity = '0.85'"
                            @mouseleave="$el.style.opacity = '1'">
                            <i class="ri-save-3-line text-lg"></i>
                            Simpan ke Database Sistem
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

        /* Custom Focus Ring for Accent Color */
        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 1px var(--primary-color) !important;
        }
    </style>

    <script>
        const priceDisplay = document.getElementById('price_display');
        const priceReal = document.getElementById('price_real');

        // Safety check: if price elements are missing, abort script
        if (priceDisplay && priceReal) {
            priceDisplay.addEventListener('keyup', function (e) {
                let value = this.value.replace(/[^,\d]/g, '').toString();
                let split = value.split(',');
                let sisa = split[0].length % 3;
                let rupiah = split[0].substr(0, sisa);
                let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

                if (ribuan) {
                    let separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                this.value = rupiah;
                priceReal.value = value.replace(/\./g, '');
            });
        }

        const imgInput = document.getElementById('imgInput');
        if (imgInput) {
            imgInput.onchange = evt => {
                const [file] = evt.target.files
                if (file) {
                    const preview = document.getElementById('preview');
                    if (preview) {
                        preview.style.opacity = '0';
                        setTimeout(() => {
                            preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover">`;
                            preview.style.opacity = '100';
                        }, 300);
                    }
                }
            }
        }
    </script>
@endsection