@extends('layouts.admin')

@section('title', 'Edit Menu Details')

@section('content')
<div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10">
    
    <!-- HEADER STANDARD -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
        <div class="space-y-1">
            <a href="{{ route('admin.menus.index') }}" class="group flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 transition-all duration-300 mb-2"
                @mouseenter="$el.style.color = 'var(--primary-color)';"
                @mouseleave="$el.style.color = '';">
                <i class="ri-arrow-left-line transition-transform group-hover:-translate-x-1"></i> Kembali ke Galeri
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Edit Masterpiece</h1>
            <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">
                Sedang menyunting: <span style="color: var(--primary-color);" class="font-bold tracking-tight">{{ $menu->name }}</span>
            </p>
        </div>
    </div>

    <!-- ERROR HANDLING (Manifesto Style) -->
    @if ($errors->any())
        <div class="mb-8 p-4 bg-red-500/5 border border-red-500/20 rounded-md">
            <div class="flex items-center gap-2 mb-2">
                <i class="ri-error-warning-fill text-red-500"></i>
                <span class="text-[10px] font-black uppercase tracking-widest text-red-500">Koreksi Diperlukan</span>
            </div>
            <ul class="list-none space-y-1">
                @foreach ($errors->all() as $error)
                    <li class="text-[11px] text-red-400/80 font-medium leading-relaxed">— {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
        @csrf
        @method('PUT')
        
        <!-- LEFT COLUMN: VISUAL IDENTITY -->
        <div class="lg:col-span-4 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Visual Identity</label>
                <div class="relative group aspect-square">
                    <input type="file" name="image" id="imgInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                    <div id="preview" class="w-full h-full rounded-lg bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 flex items-center justify-center overflow-hidden transition-all duration-500"
                        @mouseenter="$el.style.borderColor = 'var(--primary-color)';"
                        @mouseleave="$el.style.borderColor = '';">
                        <img src="{{ $menu->image_url }}" class="w-full h-full object-cover group-hover:scale-105 group-hover:blur-[2px] transition-all duration-700" alt="{{ $menu->name }}">
                        <div class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <i class="ri-camera-switch-line text-3xl text-white mb-2"></i>
                            <span class="text-[9px] font-black text-white uppercase tracking-[0.2em]">Ganti Foto</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="p-4 rounded-md border border-slate-200 dark:border-white/5 bg-slate-50/50 dark:bg-white/[0.01]">
                <p class="text-[10px] text-slate-400 dark:text-gray-500 leading-relaxed italic">
                    <span style="color: var(--primary-color);" class="font-bold">Tips:</span> Kosongkan jika tidak ingin mengubah foto. Gunakan resolusi tinggi untuk hasil maksimal di website utama.
                </p>
            </div>
        </div>

        <!-- RIGHT COLUMN: DATA CONTROL -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-8 transition-all duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Menu Name -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Nama Hidangan</label>
                        <input type="text" name="name" value="{{ old('name', $menu->name) }}" required
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none font-medium"
                               placeholder="Nama menu Anda"
                               style="focus-color: var(--primary-color);">
                    </div>

                    <!-- Category -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Kategori</label>
                        <div class="relative">
                            <select name="category_menu_id" required
                                    class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none appearance-none cursor-pointer font-bold"
                                    style="focus-color: var(--primary-color);">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (old('category_menu_id', $menu->category_menu_id) == $cat->id) ? 'selected' : '' }} class="bg-white dark:bg-[#0A0A0A]">
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i class="ri-arrow-down-s-line absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Valuation (IDR)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" id="price_display" value="{{ number_format($menu->price, 0, ',', '.') }}" required
                                   class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md pl-10 pr-4 py-3 text-sm font-bold focus:ring-0 transition-all outline-none"
                                   placeholder="0"
                                   style="color: var(--primary-color); focus-color: var(--primary-color);">
                            <input type="hidden" name="price" id="price_real" value="{{ old('price', $menu->price) }}">
                        </div>
                    </div>

                    <!-- Labels -->
                    <div class="space-y-2 md:col-span-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Labels / Tags</label>
                        <input type="text" name="labels" value="{{ old('labels', is_array($menu->labels) ? implode(', ', $menu->labels) : $menu->labels) }}"
                               class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none font-medium"
                               placeholder="Contoh: Best Seller, Pedas, Signature"
                               style="focus-color: var(--primary-color);">
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-8 space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 dark:text-gray-500">Gastronomy Description</label>
                    <textarea name="description" rows="4" required
                              class="w-full bg-slate-50 dark:bg-white/[0.02] border border-slate-200 dark:border-white/10 rounded-md px-4 py-3 text-sm text-slate-900 dark:text-white focus:ring-0 transition-all outline-none leading-relaxed font-medium"
                              placeholder="Deskripsikan cita rasa dan komposisi hidangan ini secara mendetail..."
                              style="focus-color: var(--primary-color);">{{ old('description', $menu->description) }}</textarea>
                </div>

                <!-- Actions -->
                <div class="mt-10 pt-8 border-t border-slate-200 dark:border-white/5 flex flex-col md:flex-row gap-4">
                    <a href="{{ route('admin.menus.index') }}"
                       class="flex-1 text-center py-4 rounded-md border border-slate-200 dark:border-white/10 text-[10px] font-black text-slate-500 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-white/5 transition-all duration-300">
                        Discard Changes
                    </a>
                    <button type="submit"
                            class="flex-[2] btn-primary text-black text-[10px] font-black uppercase tracking-widest py-4 rounded-md transition-all shadow-sm flex items-center justify-center gap-3 active:scale-[0.98]"
                            style="background-color: var(--primary-color);">
                        <i class="ri-refresh-line text-lg"></i>
                        Update Masterpiece
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

    /* Custom Focus State */
    input:focus, select:focus, textarea:focus {
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
                        preview.innerHTML = `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover animate-in fade-in zoom-in-95 duration-500">`;
                        preview.style.opacity = '100';
                    }, 300);
                }
            }
        }
    }
</script>
@endsection