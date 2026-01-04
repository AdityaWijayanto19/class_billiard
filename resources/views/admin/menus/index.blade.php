@extends('layouts.admin')@extends('layouts.admin')



@section('title', 'Menu Gallery')@section('title', 'Menu Gallery')



@section('content')@section('content')

    <div class="space-y-4 md:space-y-8 animate-in fade-in duration-500">    <div class="space-y-6 md:space-y-8 animate-in fade-in duration-500">



        <!-- HEADER -->        <!-- HEADER: Sleek & Aligned -->

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between border-b border-slate-200 dark:border-white/5 pb-4 md:pb-8">        <div

            <div class="space-y-1">            class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between border-b border-slate-200 dark:border-white/5 pb-6 md:pb-8">

                <h1 class="text-lg md:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">            <div class="space-y-1">

                    Menu Gallery                <h1 class="text-xl md:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">

                </h1>                    Menu Gallery

                <p class="text-[10px] md:text-xs text-slate-500 dark:text-gray-500 font-medium">                </h1>

                    Manajemen katalog visual dan konfigurasi harga hidangan.                <p class="text-[10px] md:text-xs text-slate-500 dark:text-gray-500 font-medium font-['Plus_Jakarta_Sans']">

                </p>                    Manajemen katalog visual dan konfigurasi harga hidangan.

            </div>                </p>

            </div>

            <div class="flex items-center">

                <a href="{{ route('admin.menus.create') }}"            <div class="flex items-center">

                    class="inline-flex items-center gap-2 btn-primary text-black text-[9px] md:text-[10px] font-black uppercase tracking-widest py-2 md:py-2.5 px-4 md:px-6 rounded-md transition-all shadow-sm w-full md:w-auto justify-center"                <a href="{{ route('admin.menus.create') }}"

                    style="background-color: var(--primary-color);">                    class="inline-flex items-center gap-2 btn-primary text-black text-[9px] md:text-[10px] font-black uppercase tracking-widest py-2 md:py-2.5 px-4 md:px-6 rounded-md transition-all shadow-sm w-full md:w-auto justify-center"

                    <i class="ri-add-circle-line text-base md:text-lg"></i>                    style="background-color: var(--primary-color);">

                    <span>Add New Menu</span>                    <i class="ri-add-circle-line text-base md:text-lg"></i>

                </a>                    <span>Add New Menu</span>

            </div>                </a>

        </div>            </div>

        </div>

        <!-- FILTER BAR -->

        <div class="flex items-center gap-3 md:gap-6 border-b border-slate-100 dark:border-white/5 pb-2 overflow-x-auto no-scrollbar -mx-4 px-4 md:mx-0 md:px-0">        <!-- FILTER BAR: Dynamic Categories (Server-side filtering) -->

            <a href="{{ route('admin.menus.index') }}"        <div class="flex items-center gap-4 md:gap-6 border-b border-slate-100 dark:border-white/5 pb-2 overflow-x-auto no-scrollbar -mx-4 px-4 md:mx-0 md:px-0">

                class="pb-2 px-1 border-b-2 text-[10px] md:text-[11px] font-bold uppercase tracking-wider whitespace-nowrap transition-all {{ !request('category') ? 'border-[var(--primary-color)] text-[var(--primary-color)]' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}"            <a href="{{ route('admin.menus.index') }}"

                style="{{ !request('category') ? 'border-color: var(--primary-color); color: var(--primary-color);' : '' }}">                class="pb-2 px-1 border-b-2 text-[9px] md:text-[10px] font-black uppercase tracking-widest whitespace-nowrap transition-all {{ !request('category') ? 'border-[var(--primary-color)] text-[var(--primary-color)]' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}"

                All                style="{{ !request('category') ? 'border-color: var(--primary-color); color: var(--primary-color);' : '' }}">

            </a>                All Items

            @foreach($categories as $category)            </a>

                <a href="{{ route('admin.menus.index', ['category' => $category->id]) }}"            @foreach($categories as $category)

                    class="pb-2 px-1 border-b-2 text-[10px] md:text-[11px] font-bold uppercase tracking-wider transition-all whitespace-nowrap {{ request('category') == $category->id ? 'border-[var(--primary-color)] text-[var(--primary-color)]' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}"                <a href="{{ route('admin.menus.index', ['category' => $category->id]) }}"

                    style="{{ request('category') == $category->id ? 'border-color: var(--primary-color); color: var(--primary-color);' : '' }}">                    class="pb-2 px-1 border-b-2 text-[9px] md:text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap {{ request('category') == $category->id ? 'border-[var(--primary-color)] text-[var(--primary-color)]' : 'border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200' }}"

                    {{ $category->name }}                    style="{{ request('category') == $category->id ? 'border-color: var(--primary-color); color: var(--primary-color);' : '' }}">

                </a>                    {{ $category->name }}

            @endforeach                </a>

        </div>            @endforeach

        </div>

        <!-- MAIN GRID - Style seperti public menu -->

        <div class="grid grid-cols-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 md:gap-4 pt-8 md:pt-12">        <!-- MAIN GRID: Professional Sleek Cards -->

            @foreach($menus as $menu)        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6">

                <div class="menu-item group relative bg-slate-100 dark:bg-white/5 rounded-xl pt-10 md:pt-14 pb-3 md:pb-4 px-2 md:px-3 text-center transition-all duration-300 hover:shadow-lg" data-menu-category="{{ $menu->category_menu_id }}">            @foreach($menus as $menu)

                                    <div class="group flex flex-col transition-all duration-300 block">

                    <!-- Circular Image -->

                    <img src="{{ $menu->image_url ?: 'https://via.placeholder.com/150' }}"                    <!-- Image Area: Precise Radius -->

                        alt="{{ $menu->name }}"                    <div

                        class="w-16 h-16 md:w-24 md:h-24 rounded-full mx-auto -mt-14 md:-mt-20 object-cover border-2 border-slate-200 dark:border-white/10 group-hover:scale-105 transition-transform duration-300 shadow-md">                        class="relative aspect-square overflow-hidden rounded-lg bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5">

                                            <img src="{{ $menu->image_url }}"

                    <!-- Menu Name -->                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"

                    <h3 class="text-[10px] md:text-xs font-semibold mt-2 md:mt-3 px-1 leading-tight line-clamp-2 text-slate-900 dark:text-white">                            alt="{{ $menu->name }}">

                        {{ $menu->name }}

                    </h3>                        <!-- Floating Price -->

                                            <div class="absolute bottom-2 right-2 md:bottom-3 md:right-3">

                    <!-- Short Description (hidden on mobile) -->                            <div

                    <p class="text-[8px] md:text-[10px] text-slate-500 dark:text-gray-500 mt-1 px-1 line-clamp-2 hidden md:block">                                class="text-black px-1.5 md:px-2.5 py-0.5 md:py-1 rounded text-[8px] md:text-[10px] font-black shadow-lg uppercase tracking-tighter"

                        {{ Str::limit($menu->short_description, 40) }}                                style="background-color: var(--primary-color);">

                    </p>                                IDR {{ number_format($menu->price, 0, ',', '.') }}

                                                </div>

                    <!-- Price -->                        </div>

                    <p class="text-[10px] md:text-xs font-bold mt-1 md:mt-2 text-slate-900 dark:text-white">

                        Rp{{ number_format($menu->price, 0, ',', '.') }}                        <!-- Category Label -->

                    </p>                        <div class="absolute top-2 left-2 md:top-3 md:left-3">

                                                <span

                    <!-- Label Badge -->                                class="px-1.5 md:px-2 py-0.5 bg-black/60 backdrop-blur-md text-white text-[6px] md:text-[8px] font-bold uppercase tracking-[0.15em] md:tracking-[0.2em] rounded border border-white/10">

                    @if($menu->labels && is_array($menu->labels) && count($menu->labels) > 0)                                {{ $menu->categoryMenu->name }}

                        @php                            </span>

                            $labelText = $menu->labels[0];                        </div>

                            $label = strtolower((string)$labelText);                    </div>

                            if(strpos($label, 'best seller') !== false || strpos($label, 'rekomendasi') !== false) {

                                $badgeStyle = 'background-color: var(--primary-color); color: #000;';                    <!-- Content Area: Text Focused -->

                            } elseif(strpos($label, 'new') !== false) {                    <div class="py-2 md:py-4 space-y-1 md:space-y-2 flex-1 flex flex-col">

                                $badgeStyle = 'background-color: #10b981; color: #fff;';

                            } else {                        <h3 class="text-xs md:text-sm font-bold text-slate-900 dark:text-white transition-colors leading-tight line-clamp-2"

                                $badgeStyle = 'background-color: #ef4444; color: #fff;';                            style="color: inherit;"

                            }                            @mouseenter="$el.style.color = 'var(--primary-color)'"

                        @endphp                            @mouseleave="$el.style.color = ''">

                        <div class="mt-2">                            {{ $menu->name }}

                            <span class="text-[7px] md:text-[9px] px-2 py-0.5 rounded-full font-bold" style="{{ $badgeStyle }}">                        </h3>

                                {{ $labelText }}

                            </span>                        <p class="text-[9px] md:text-[11px] text-slate-500 dark:text-gray-500 font-medium leading-relaxed line-clamp-2 hidden md:block">

                        </div>                            {{ $menu->short_description }}

                    @endif                        </p>

                    

                    <!-- Action Buttons -->                        @if($menu->labels && is_array($menu->labels) && count($menu->labels) > 0)

                    <div class="flex items-center justify-center gap-1 mt-2 md:mt-3">                            @php

                        <a href="{{ route('admin.menus.edit', $menu) }}"                                $labelText = is_array($menu->labels) ? $menu->labels[0] : $menu->labels;

                            class="w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 hover:bg-[var(--primary-color)] hover:text-black transition-all text-[10px] md:text-xs">                                $label = strtolower((string)$labelText);

                            <i class="ri-pencil-line"></i>                                if(strpos($label, 'best seller') !== false || strpos($label, 'rekomendasi') !== false) {

                        </a>                                    $bgClass = 'bg-yellow-500/20';

                        <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline delete-form">                                    $textClass = 'text-yellow-600 dark:text-yellow-400';

                            @csrf @method('DELETE')                                } elseif(strpos($label, 'new') !== false) {

                            <button type="button" onclick="confirmDelete(this)"                                    $bgClass = 'bg-emerald-500/20';

                                class="w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 hover:bg-red-500 hover:text-white transition-all text-[10px] md:text-xs">                                    $textClass = 'text-emerald-600 dark:text-emerald-400';

                                <i class="ri-delete-bin-line"></i>                                } else {

                            </button>                                    $bgClass = 'bg-red-500/20';

                        </form>                                    $textClass = 'text-red-600 dark:text-red-400';

                    </div>                                }

                </div>                            @endphp

            @endforeach                            <div class="flex gap-1 md:gap-2 mt-1 md:mt-2">

        </div>                                <span class="text-[8px] md:text-xs px-1.5 md:px-2 py-0.5 rounded {{ $bgClass }} {{ $textClass }} font-medium">{{ $labelText }}</span>

                            </div>

        <!-- Empty State -->                        @endif

        @if($menus->isEmpty())

            <div class="text-center py-12">                        <!-- Actions: Discrete -->

                <i class="ri-restaurant-line text-4xl text-slate-300 dark:text-gray-600 mb-4"></i>                        <div class="pt-2 md:pt-4 flex items-center justify-between mt-auto">

                <p class="text-sm text-slate-500 dark:text-gray-500">Belum ada menu dalam kategori ini.</p>                            <span class="text-[7px] md:text-[9px] font-bold text-slate-400 dark:text-gray-600 uppercase tracking-[0.1em] md:tracking-[0.2em] hidden sm:inline">Menu

            </div>                                #{{ $menu->id }}</span>

        @endif

                            <div class="flex items-center gap-1 w-full sm:w-auto justify-end">

        <!-- PAGINATION -->                                <a href="{{ route('admin.menus.edit', $menu) }}"

        <div class="pt-6 md:pt-10 flex justify-center border-t border-slate-100 dark:border-white/5">                                    class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded border border-slate-200 dark:border-white/10 text-slate-400 transition-all text-sm md:text-base"

            {{ $menus->links() }}                                    style="--hover-color: var(--primary-color);"

        </div>                                    @mouseenter="$el.style.borderColor = 'var(--primary-color)'; $el.style.color = 'var(--primary-color)';"

    </div>                                    @mouseleave="$el.style.borderColor = ''; $el.style.color = '';">

                                    <i class="ri-pencil-line"></i>

    {{-- SweetAlert2 --}}                                </a>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>                                <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline delete-form">

        function confirmDelete(button) {                                    @csrf @method('DELETE')

            const form = button.closest('.delete-form');                                    <button type="button" onclick="confirmDelete(this)"

            const menuItem = button.closest('.menu-item');                                        class="w-7 h-7 md:w-8 md:h-8 flex items-center justify-center rounded border border-slate-200 dark:border-white/10 text-slate-400 hover:border-red-500 hover:text-red-500 transition-all text-sm md:text-base">

            const menuName = menuItem ? menuItem.querySelector('h3').textContent.trim() : 'Menu';                                        <i class="ri-delete-bin-line"></i>

                                    </button>

            Swal.fire({                                </form>

                title: 'KONFIRMASI HAPUS',                            </div>

                text: `Menu "${menuName}" akan dihapus secara permanen.`,                        </div>

                icon: 'warning',                    </div>

                showCancelButton: true,                </div>

                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),            @endforeach

                cancelButtonColor: '#1a1a1a',        </div>

                confirmButtonText: 'YA, HAPUS',

                cancelButtonText: 'BATAL',        <!-- PAGINATION -->

                background: '#0a0a0a',        <div class="pt-6 md:pt-10 flex justify-center border-t border-slate-100 dark:border-white/5">

                color: '#ffffff',            {{ $menus->links() }}

                customClass: {        </div>

                    title: 'text-sm font-black tracking-widest',    </div>

                    htmlContainer: 'text-xs text-gray-400 font-medium',

                    confirmButton: 'text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-md',    {{-- SweetAlert2 Custom Styling & Script --}}

                    cancelButton: 'text-[10px] font-black uppercase tracking-widest px-6 py-2.5 rounded-md'    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                }    <script>

            }).then((result) => {        function confirmDelete(button) {

                if (result.isConfirmed) {            const form = button.closest('.delete-form');

                    form.submit();            if (!form) {

                }                console.error('Delete form not found');

            });                return;

        }            }

    </script>            

            const categoryElement = form.closest('[data-menu-category]');

    <style>            if (!categoryElement) {

        .swal2-popup {                console.error('Category element not found');

            border: 1px solid rgba(255, 255, 255, 0.05);                return;

            font-family: 'Plus Jakarta Sans', sans-serif !important;            }

        }            

    </style>            const menuNameElement = categoryElement.querySelector('h3');

@endsection            const menuName = menuNameElement ? menuNameElement.textContent.trim() : 'Menu';


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