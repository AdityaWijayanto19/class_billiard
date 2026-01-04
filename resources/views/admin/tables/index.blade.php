@extends('layouts.admin')

@section('title', 'Manajemen Meja')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-10">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Management <span style="color: var(--primary-color);">Meja</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Kelola akses barcode digital dan pemetaan
                    identitas meja operasional.</p>
            </div>

            <a href="{{ route('admin.tables.create') }}"
                class="text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md transition-all shadow-sm flex items-center gap-2 active:scale-95 btn-primary"
                style="background-color: var(--primary-color);"
                @mouseenter="$el.style.opacity = '0.85'"
                @mouseleave="$el.style.opacity = '1'">
                <i class="ri-add-circle-line text-lg"></i>
                Tambah Meja Baru
            </a>
        </div>

        <!-- ALERT SUCCESS STANDARD -->
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                class="mb-8 flex items-center justify-between bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md transition-all">
                <div class="flex items-center gap-3 text-emerald-500">
                    <i class="ri-checkbox-circle-fill text-lg"></i>
                    <span class="text-[11px] font-black uppercase tracking-widest">{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-500/50 hover:text-emerald-500"><i
                        class="ri-close-line"></i></button>
            </div>
        @endif

        <!-- TABLES GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tables as $table)
                <div
                    class="group bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg overflow-hidden transition-all duration-300 flex flex-col"
                    @mouseenter="$el.style.borderColor = 'var(--primary-color)'"
                    @mouseleave="$el.style.borderColor = ''">

                    <!-- QR CODE AREA -->
                    <div class="p-6 pb-0">
                        <div
                            class="aspect-square bg-slate-50 dark:bg-white/5 rounded-md flex items-center justify-center relative overflow-hidden transition-colors border border-slate-100 dark:border-white/5">

                            @if($table->qrcode && Storage::disk('public')->exists($table->qrcode))
                                <div class="p-4 bg-white rounded-md shadow-sm">
                                    <img src="{{ asset('storage/' . $table->qrcode) }}" alt="QR {{ $table->name }}"
                                        class="w-40 h-40 object-contain">
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center gap-4 text-center p-6">
                                    <div
                                        class="w-12 h-12 rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400">
                                        <i class="ri-qr-code-line text-2xl"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <p
                                            class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest">
                                            QR Not Found</p>
                                        <form action="{{ route('admin.tables.generate-qr', $table->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="text-[10px] font-black uppercase tracking-widest hover:underline decoration-2 underline-offset-4" style="color: var(--primary-color);">
                                                Generate Now
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <!-- HOVER QUICK ACTIONS -->
                            <div
                                class="absolute inset-0 bg-[#050505]/90 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center gap-2 z-20 backdrop-blur-sm">
                                <a href="{{ route('admin.tables.barcode', $table->id) }}"
                                    class="w-10 h-10 text-black rounded-md flex items-center justify-center hover:scale-110 transition-transform shadow-lg"
                                    style="background-color: var(--primary-color);"
                                    title="Print Poster">
                                    <i class="ri-printer-line text-lg"></i>
                                </a>

                                @if($table->qrcode && Storage::disk('public')->exists($table->qrcode))
                                    <a href="{{ asset('storage/' . $table->qrcode) }}" download="QR_{{ $table->slug }}.png"
                                        class="w-10 h-10 bg-white text-black rounded-md flex items-center justify-center hover:scale-110 transition-transform shadow-lg"
                                        title="Download PNG">
                                        <i class="ri-download-2-line text-lg"></i>
                                    </a>
                                @endif

                                @php
                                    $tableNumber = preg_replace('/[^0-9]/', '', $table->name);
                                    if (empty($tableNumber)) {
                                        $tableNumber = $table->name;
                                    }
                                @endphp
                                <a href="{{ route('orders.create') }}?table={{ urlencode($tableNumber) }}&room={{ urlencode($table->room ?? '') }}"
                                    target="_blank"
                                    class="w-10 h-10 text-white rounded-md flex items-center justify-center hover:scale-110 transition-transform shadow-lg"
                                    style="background-color: var(--primary-color);"
                                    title="Live Preview">
                                    <i class="ri-external-link-line text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- TABLE INFO -->
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-6">
                            <div class="space-y-1">
                                <h3
                                    class="text-sm font-bold text-slate-900 dark:text-white transition-colors uppercase tracking-tight"
                                    style="color: var(--primary-color);">
                                    {{ $table->name }}
                                </h3>
                                <div class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-[0.2em] space-y-0.5">
                                    <p>ID: #{{ $table->id }}</p>
                                    @if($table->room)
                                        <p>ROOM: {{ $table->room }}</p>
                                    @endif
                                </div>
                            </div>
                            <div
                                class="flex items-center gap-1.5 bg-emerald-500/10 px-2 py-1 rounded border border-emerald-500/20">
                                <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">Active</span>
                            </div>
                        </div>

                        <!-- FOOTER ACTIONS -->
                        <div class="mt-auto flex gap-2 pt-4 border-t border-slate-100 dark:border-white/5">
                            <a href="{{ route('admin.tables.barcode', $table->id) }}"
                                class="flex-1 flex items-center justify-center gap-2 py-2.5 text-black rounded-md text-[10px] font-black uppercase tracking-widest transition-all border border-slate-200 dark:border-white/10"
                                style="background-color: var(--primary-color);"
                                @mouseenter="$el.style.opacity = '0.85'"
                                @mouseleave="$el.style.opacity = '1'">
                                <i class="ri-fullscreen-line"></i> Detail QR
                            </a>

                            <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST" class="shrink-0 delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)"
                                    class="w-10 h-10 flex items-center justify-center bg-red-500/5 text-red-500 hover:bg-red-500 hover:text-white rounded-md transition-all border border-red-500/10">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <!-- EMPTY STATE STANDARD -->
                <div
                    class="col-span-full py-20 flex flex-col items-center justify-center bg-slate-50 dark:bg-white/[0.01] rounded-lg border border-dashed border-slate-200 dark:border-white/10 text-center">
                    <div
                        class="w-16 h-16 rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 mb-4">
                        <i class="ri-qr-code-line text-3xl"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-tight">Data Meja Kosong</h3>
                    <p class="text-xs text-slate-500 dark:text-gray-500 mt-1">Sistem belum mendeteksi adanya meja operasional
                        yang terdaftar.</p>
                    <a href="{{ route('admin.tables.create') }}"
                        class="mt-6 text-black px-8 py-3 rounded-md text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-sm"
                        style="background-color: var(--primary-color);"
                        @mouseenter="$el.style.opacity = '0.85'"
                        @mouseleave="$el.style.opacity = '1'">
                        Inisialisasi Meja Baru
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- SweetAlert2 Custom Styling & Script --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            const form = button.closest('.delete-form');
            const tableNameElement = form.closest('[class*="group"]').querySelector('h3');
            const tableName = tableNameElement ? tableNameElement.textContent.trim() : 'Meja';

            Swal.fire({
                title: 'KONFIRMASI HAPUS',
                text: `Meja "${tableName}" akan dihapus secara permanen beserta semua data QR Code-nya.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: getComputedStyle(document.documentElement).getPropertyValue('--primary-color').trim(),
                cancelButtonColor: '#1a1a1a',
                confirmButtonText: 'YA, HAPUS MEJA',
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

        // Success Alert after redirect
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'BERHASIL',
                text: "{{ session('success') }}",
                background: '#0a0a0a',
                color: '#ffffff',
                showConfirmButton: false,
                timer: 2000,
                borderRadius: '8px'
            });
        @endif
    </script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .swal2-popup {
            border: 1px solid rgba(255, 255, 255, 0.05);
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
    </style>
@endsection