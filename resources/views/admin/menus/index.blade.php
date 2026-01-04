@extends('layouts.admin')

@section('title', 'Menu Gallery')

@section('content')
    <div class="space-y-8 animate-in fade-in duration-500">

        <!-- HEADER: Sleek & Aligned -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    Menu Gallery
                </h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium font-['Plus_Jakarta_Sans']">
                    Manajemen katalog visual dan konfigurasi harga hidangan.
                </p>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('admin.menus.create') }}"
                    class="inline-flex items-center gap-2 btn-primary text-black text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-md transition-all shadow-sm"
                    style="background-color: var(--primary-color);">
                    <i class="ri-add-circle-line text-lg"></i>
                    <span>Add New Menu</span>
                </a>
            </div>
        </div>

        <!-- FILTER BAR: Dynamic Categories -->
        <div class="flex items-center gap-6 border-b border-slate-100 dark:border-white/5 pb-2 overflow-x-auto no-scrollbar"
            x-data="{
                selectedCategory: 'all',
                filterItems(category) {
                    this.selectedCategory = category;
                    const items = document.querySelectorAll('[data-menu-category]');
                    items.forEach(item => {
                        if (category === 'all' || item.dataset.menuCategory === category) {
                            item.classList.remove('hidden');
                            item.classList.add('block');
                        } else {
                            item.classList.add('hidden');
                            item.classList.remove('block');
                        }
                    });
                }
            }">
            <button @click="filterItems('all')"
                class="pb-2 px-1 border-b-2 text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all"
                :style="selectedCategory === 'all' ? { borderColor: 'var(--primary-color)', color: 'var(--primary-color)' } : { borderColor: 'transparent', color: '' }"
                :class="selectedCategory === 'all' ? '' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'">
                All Items
            </button>
            @foreach($categories as $category)
                <button @click="filterItems('{{ $category->id }}')"
                    class="pb-2 px-1 border-b-2 text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap"
                    :style="selectedCategory === '{{ $category->id }}' ? { borderColor: 'var(--primary-color)', color: 'var(--primary-color)' } : { borderColor: 'transparent', color: '' }"
                    :class="selectedCategory === '{{ $category->id }}' ? '' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-200'">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <!-- MAIN GRID: Professional Sleek Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($menus as $menu)
                <div class="group flex flex-col transition-all duration-300 block" data-menu-category="{{ $menu->category_menu_id }}">

                    <!-- Image Area: Precise Radius -->
                    <div
                        class="relative aspect-square overflow-hidden rounded-lg bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5">
                        <img src="{{ $menu->image_url }}"
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            alt="{{ $menu->name }}">

                        <!-- Floating Price -->
                        <div class="absolute bottom-3 right-3">
                            <div
                                class="text-black px-2.5 py-1 rounded text-[10px] font-black shadow-lg uppercase tracking-tighter"
                                style="background-color: var(--primary-color);">
                                IDR {{ number_format($menu->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <!-- Category Label -->
                        <div class="absolute top-3 left-3">
                            <span
                                class="px-2 py-0.5 bg-black/60 backdrop-blur-md text-white text-[8px] font-bold uppercase tracking-[0.2em] rounded border border-white/10">
                                {{ $menu->categoryMenu->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Content Area: Text Focused -->
                    <div class="py-4 space-y-2 flex-1 flex flex-col">

                        <h3 class="text-sm font-bold text-slate-900 dark:text-white transition-colors leading-tight"
                            style="color: inherit;"
                            @mouseenter="$el.style.color = 'var(--primary-color)'"
                            @mouseleave="$el.style.color = ''">
                            {{ $menu->name }}
                        </h3>

                        <p class="text-[11px] text-slate-500 dark:text-gray-500 font-medium leading-relaxed line-clamp-2">
                            {{ $menu->short_description }}
                        </p>

                        @if($menu->labels)
                            @php
                                $labelText = $menu->labels;
                                if (str_starts_with($labelText, '[')) {
                                    $decoded = json_decode($labelText, true);
                                    $labelText = is_array($decoded) && count($decoded) > 0 ? $decoded[0] : $labelText;
                                }
                                $label = strtolower((string)$labelText);
                                if(strpos($label, 'best seller') !== false || strpos($label, 'rekomendasi') !== false) {
                                    $bgClass = 'bg-yellow-500/20';
                                    $textClass = 'text-yellow-600 dark:text-yellow-400';
                                } elseif(strpos($label, 'new') !== false) {
                                    $bgClass = 'bg-emerald-500/20';
                                    $textClass = 'text-emerald-600 dark:text-emerald-400';
                                } else {
                                    $bgClass = 'bg-red-500/20';
                                    $textClass = 'text-red-600 dark:text-red-400';
                                }
                            @endphp
                            <div class="flex gap-2 mt-2">
                                <span class="text-xs px-2 py-0.5 rounded {{ $bgClass }} {{ $textClass }} font-medium">{{ $labelText }}</span>
                            </div>
                        @endif

                        <!-- Actions: Discrete -->
                        <div class="pt-4 flex items-center justify-between mt-auto">
                            <span class="text-[9px] font-bold text-slate-400 dark:text-gray-600 uppercase tracking-[0.2em]">Menu
                                Item #{{ $menu->id }}</span>

                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.menus.edit', $menu) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded border border-slate-200 dark:border-white/10 text-slate-400 transition-all"
                                    style="--hover-color: var(--primary-color);"
                                    @mouseenter="$el.style.borderColor = 'var(--primary-color)'; $el.style.color = 'var(--primary-color)';"
                                    @mouseleave="$el.style.borderColor = ''; $el.style.color = '';">
                                    <i class="ri-pencil-line"></i>
                                </a>

                                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)"
                                        class="w-8 h-8 flex items-center justify-center rounded border border-slate-200 dark:border-white/10 text-slate-400 hover:border-red-500 hover:text-red-500 transition-all">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- PAGINATION -->
        <div class="pt-10 flex justify-center border-t border-slate-100 dark:border-white/5">
            {{ $menus->links() }}
        </div>
    </div>

    {{-- SweetAlert2 Custom Styling & Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            const form = button.closest('.delete-form');
            if (!form) {
                console.error('Delete form not found');
                return;
            }
            
            const categoryElement = form.closest('[data-menu-category]');
            if (!categoryElement) {
                console.error('Category element not found');
                return;
            }
            
            const menuNameElement = categoryElement.querySelector('h3');
            const menuName = menuNameElement ? menuNameElement.textContent.trim() : 'Menu';

            Swal.fire({
                title: 'KONFIRMASI HAPUS',
                text: `Menu "${menuName}" akan dihapus secara permanen dari katalog.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                cancelButtonColor: '#1a1a1a',
                confirmButtonText: 'YA, HAPUS MENU',
                cancelButtonText: 'BATAL',
                background: '#0a0a0a',
                color: '#ffffff',
                borderRadius: '8px',
                customClass: {
                    title: 'text-sm font-black tracking-widest',
                    htmlContainer: 'text-xs text-gray-400 font-medium',
                    confirmButton: 'text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-md',
                    cancelButton: 'text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-md'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

    <style>
        .swal2-popup {
            border: 1px solid rgba(255, 255, 255, 0.05);
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>
@endsection