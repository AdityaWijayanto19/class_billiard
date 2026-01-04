@extends('layouts.admin')

@section('title', 'Manajemen Order')

@section('content')
    <div class="min-h-screen bg-white dark:bg-[#050505] p-6 lg:p-10 transition-colors duration-300"
        x-data="{ currentFilter: 'all' }">

        <!-- HEADER STANDARD -->
        <div
            class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-200 dark:border-white/5 pb-8 mb-8">
            <div class="space-y-1">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white uppercase">Order <span
                        style="color: var(--primary-color);">Management</span></h1>
                <p class="text-xs text-slate-500 dark:text-gray-500 font-medium">Monitoring real-time transaksi operasional
                    dan alur kerja dapur.</p>
            </div>
            <div class="flex items-center gap-6">
                <div class="text-right hidden sm:block">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Orders</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $allOrders->count() }}</p>
                </div>
                <a href="{{ route('admin.orders.recap.index') }}"
                    class="bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-3 px-6 rounded-md hover:bg-slate-800 dark:hover:bg-slate-200 transition-all shadow-sm flex items-center gap-2">
                    <i class="ri-archive-line text-sm"></i>
                    Tutup Hari / Recap
                </a>
            </div>
        </div>

        <!-- FLASH MESSAGES -->
        @if(session('success'))
            <div
                class="mb-6 flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/20 px-4 py-3 rounded-md animate-in slide-in-from-top-4 duration-300">
                <i class="ri-checkbox-circle-fill text-emerald-500"></i>
                <span class="text-[11px] font-bold text-emerald-500 uppercase tracking-widest">{{ session('success') }}</span>
            </div>
        @endif

        <!-- TABLE FILTER (Sleek Control Panel) -->
        <div class="mb-10">
            <label class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-[0.2em] block mb-4">
                Filter By Station / Table
            </label>
            <div class="flex flex-wrap gap-2">
                <!-- All Tables -->
                <button @click="currentFilter = 'all'; filterByTable('all')"
                    :class="currentFilter === 'all' ? 'text-black border-b-2' : 'text-slate-500 dark:text-gray-400 border-b-2 border-transparent'"
                    :style="{ backgroundColor: currentFilter === 'all' ? 'var(--primary-color)' : '', borderColor: currentFilter === 'all' ? 'var(--primary-color)' : '' }"
                    class="px-5 py-2.5 rounded-md border text-[11px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95">
                    All Units
                </button>

                @foreach($tables as $table)
                    @php
                        $tableNum = preg_replace('/[^0-9]/', '', $table->name) ?: $table->name;
                    @endphp
                    <button @click="currentFilter = '{{ $tableNum }}'; filterByTable('{{ $tableNum }}')"
                        :class="currentFilter === '{{ $tableNum }}' ? 'text-black border-b-2' : 'text-slate-500 dark:text-gray-400 border-b-2 border-transparent'"
                        :style="{ backgroundColor: currentFilter === '{{ $tableNum }}' ? 'var(--primary-color)' : '', borderColor: currentFilter === '{{ $tableNum }}' ? 'var(--primary-color)' : '' }"
                        class="px-5 py-2.5 rounded-md border text-[11px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95">
                        {{ $tableNum }}
                    </button>
                @endforeach

                @if($tables->count() > 0)
                    <button @click="currentFilter = 'other'; filterByTable('other')"
                        :class="currentFilter === 'other' ? 'text-black border-b-2' : 'text-slate-500 dark:text-gray-400 border-b-2 border-transparent'"
                        :style="{ backgroundColor: currentFilter === 'other' ? 'var(--primary-color)' : '', borderColor: currentFilter === 'other' ? 'var(--primary-color)' : '' }"
                        class="px-5 py-2.5 rounded-md border text-[11px] font-black uppercase tracking-widest transition-all duration-300 active:scale-95">
                        Others
                    </button>
                @endif
            </div>
        </div>

        <!-- ORDERS GRID -->
        @if($allOrders->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="ordersContainer">
                @foreach($allOrders as $order)
                    @php
                        $tableNum = $order->table_number;
                        $numericTable = preg_replace('/[^0-9]/', '', $tableNum);
                        $tableExists = $tables->contains(function ($table) use ($tableNum, $numericTable) {
                            $tableNameNum = preg_replace('/[^0-9]/', '', $table->name);
                            return $table->name === $tableNum || $tableNameNum === $numericTable || $table->name === $numericTable;
                        });
                        $tableFilter = $tableExists && !empty($numericTable) ? $numericTable : 'other';
                    @endphp

                    <div class="order-card-base group bg-white dark:bg-[#0A0A0A] border border-slate-200 dark:border-white/5 rounded-lg p-6 hover:border-[color:var(--primary-color)] transition-all duration-300 flex flex-col {{ $order->status === 'rejected' ? 'opacity-50' : '' }}"
                        data-table="{{ $tableFilter }}"
                        style="--hover-border-color: var(--primary-color);">

                        <!-- Card Header -->
                        <div class="flex justify-between items-start mb-6">
                            <div class="space-y-1">
                                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">
                                    {{ $order->customer_name }}</h3>
                                <div class="flex items-center gap-2">
                                    <span style="color: var(--primary-color);" class="text-[10px] font-bold uppercase tracking-widest">Table
                                        {{ $order->table_number }}</span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-white/10"></span>
                                    <span
                                        class="text-[10px] font-medium text-slate-400 uppercase tracking-widest">{{ $order->room }}</span>
                                </div>
                            </div>

                            @if($order->status === 'pending')
                                <span
                                    class="bg-amber-500/10 text-amber-500 text-[9px] font-black px-2 py-1 rounded-md border border-amber-500/20 uppercase tracking-widest">Confirming</span>
                            @elseif($order->status === 'processing')
                                <span
                                    class="bg-blue-500/10 text-blue-500 text-[9px] font-black px-2 py-1 rounded-md border border-blue-500/20 uppercase tracking-widest">Cooking</span>
                            @elseif($order->status === 'completed')
                                <span
                                    class="bg-emerald-500/10 text-emerald-500 text-[9px] font-black px-2 py-1 rounded-md border border-emerald-500/20 uppercase tracking-widest">Served</span>
                            @elseif($order->status === 'rejected')
                                <span
                                    class="bg-slate-500/10 text-slate-500 text-[9px] font-black px-2 py-1 rounded-md border border-slate-500/20 uppercase tracking-widest">Void</span>
                            @endif
                        </div>

                        <!-- Items List -->
                        <div class="space-y-3 mb-6 flex-1">
                            <p class="text-[10px] font-black text-slate-400 dark:text-gray-500 uppercase tracking-widest">Order
                                Details</p>
                            @foreach($order->orderItems as $item)
                                <div
                                    class="flex items-center gap-3 bg-slate-50 dark:bg-white/[0.02] p-2 rounded-md border border-slate-100 dark:border-white/[0.05]">
                                    @if($item->image)
                                        <img src="{{ asset($item->image) }}"
                                            class="w-8 h-8 rounded-md object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200 truncate">
                                            {{ $item->quantity }}x {{ $item->menu_name }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pricing & Meta -->
                        <div class="border-t border-slate-100 dark:border-white/5 pt-4 mb-6">
                            <div class="flex justify-between items-end">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                        {{ strtoupper($order->payment_method) }}</p>
                                    <p class="text-[9px] text-slate-400 font-medium">
                                        {{ \Carbon\Carbon::parse($order->created_at)->utc()->setTimezone('Asia/Jakarta')->format('H:i') }}
                                        WIB
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- ACTIONS -->
                        <div class="space-y-2">
                            @if($order->status === 'pending')
                                <div class="grid grid-cols-2 gap-2">
                                    <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-black text-[10px] font-black uppercase tracking-widest py-3 rounded-md transition-all active:scale-95 btn-primary"
                                            style="background-color: var(--primary-color);">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.orders.reject', $order->id) }}" method="POST" class="reject-form">
                                        @csrf
                                        <button type="button"
                                            class="w-full bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 text-[10px] font-black uppercase tracking-widest py-3 rounded-md transition-all active:scale-95"
                                            onclick="confirmReject(this.closest('form'))">Void</button>
                                    </form>
                                </div>
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="mt-2 delete-form">
                                    @csrf @method('DELETE')
                                    <button type="button"
                                        class="w-full text-red-500 text-[9px] font-black uppercase tracking-[0.2em] py-2 hover:bg-red-500/5 rounded-md transition-all"
                                        onclick="confirmDelete(this.closest('form'))">Delete
                                        Permanent</button>
                                </form>
                            @endif

                            @if($order->status === 'processing')
                                <div class="bg-blue-500/5 border border-blue-500/10 p-3 rounded-md text-center">
                                    <p
                                        class="text-[10px] font-bold text-blue-500 uppercase tracking-widest flex items-center justify-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                        Cooking in Progress
                                    </p>
                                </div>
                            @endif

                            @if($order->status === 'completed')
                                <button onclick="rekapOrder({{ $order->id }})"
                                    class="w-full bg-slate-900 dark:bg-white text-white dark:text-black text-[10px] font-black uppercase tracking-widest py-3 rounded-md transition-all active:scale-95 flex items-center justify-center gap-2">
                                    <i class="ri-check-double-line"></i> Rekap to Journal
                                </button>
                            @endif

                            @if($order->status === 'rejected')
                                <div class="bg-slate-500/5 border border-slate-500/10 p-3 rounded-md text-center">
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest italic">Order Voided</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div
                class="flex flex-col items-center justify-center py-32 border border-dashed border-slate-200 dark:border-white/5 rounded-lg bg-slate-50/50 dark:bg-white/[0.01]">
                <div
                    class="w-16 h-16 rounded-md bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-400 mb-4">
                    <i class="ri-inbox-line text-3xl"></i>
                </div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">No active orders found
                </h3>
                <p class="text-xs text-slate-500 dark:text-gray-500 mt-1">Waiting for customer check-in...</p>
            </div>
        @endif
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function filterByTable(table) {
                const orderCards = document.querySelectorAll('.order-card-base');
                orderCards.forEach(card => {
                    const cardTable = card.getAttribute('data-table');
                    if (table === 'all' || cardTable === String(table)) {
                        card.style.display = 'flex';
                        card.classList.add('animate-in', 'fade-in', 'zoom-in-95', 'duration-300');
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // AUTO REFRESH LOGIC (Preserved Backend Integration)
            let lastOrderId = {{ $allOrders->max('id') ?? 0 }};
            let lastCheckTime = new Date().toISOString();
            let isRefreshing = false;
            let refreshInterval = null;

            function startAutoRefresh() {
                if (refreshInterval === null) {
                    refreshInterval = setInterval(checkForChanges, 3000);
                }
            }

            function stopAutoRefresh() {
                if (refreshInterval !== null) {
                    clearInterval(refreshInterval);
                    refreshInterval = null;
                }
            }

            async function checkForChanges() {
                if (isRefreshing) return;
                try {
                    const response = await fetch(`/admin/orders/check-new?last_order_id=${lastOrderId}&last_check_time=${encodeURIComponent(lastCheckTime)}`);
                    const data = await response.json();

                    if (data.has_active_orders === false) {
                        stopAutoRefresh();
                        setTimeout(checkForChanges, 10000);
                    } else {
                        if (refreshInterval === null) startAutoRefresh();
                    }

                    if (data.has_changes && data.has_active_orders) {
                        isRefreshing = true;
                        location.reload();
                    } else {
                        if (data.latest_order_id > lastOrderId) lastOrderId = data.latest_order_id;
                        if (data.current_time) lastCheckTime = data.current_time;
                    }
                } catch (error) {
                    console.error('Error checking orders:', error);
                    stopAutoRefresh();
                    setTimeout(checkForChanges, 10000);
                }
            }

            @if($allOrders->whereIn('status', ['pending', 'processing'])->count() > 0)
                startAutoRefresh();
            @endif

            setTimeout(checkForChanges, 1000);

            async function rekapOrder(orderId) {
                Swal.fire({
                    title: 'Rekap Order?',
                    text: 'Yakin ingin merekap order ini ke laporan harian?',
                    icon: 'question',
                    background: '#0A0A0A',
                    color: '#FFFFFF',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--primary-color)',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Rekap',
                    cancelButtonText: 'Batal'
                }).then(async (result) => {
                    if (!result.isConfirmed) return;
                    try {
                        const response = await fetch(`/admin/orders/${orderId}/rekap`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            }
                        });
                        const result = await response.json();
                        if (result.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Order berhasil di-rekap ke laporan harian',
                                icon: 'success',
                                background: '#0A0A0A',
                                color: '#FFFFFF',
                                timer: 1500
                            }).then(() => location.reload());
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: result.message,
                                icon: 'error',
                                background: '#0A0A0A',
                                color: '#FFFFFF'
                            });
                        }
                    } catch (error) {
                        console.error('Error rekap order:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Terjadi kesalahan saat rekap order',
                            icon: 'error',
                            background: '#0A0A0A',
                            color: '#FFFFFF'
                        });
                    }
                });
            }

            function confirmReject(form) {
                Swal.fire({
                    title: 'Void Order?',
                    text: 'Yakin ingin membatalkan order ini?',
                    icon: 'warning',
                    background: '#0A0A0A',
                    color: '#FFFFFF',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Void',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            }

            function confirmDelete(form) {
                Swal.fire({
                    title: 'Delete Permanent?',
                    text: 'Anda tidak bisa membatalkan aksi ini. Order akan dihapus selamanya.',
                    icon: 'error',
                    background: '#0A0A0A',
                    color: '#FFFFFF',
                    showCancelButton: true,
                    confirmButtonColor: '#DC2626',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            }
        </script>
    @endpush
@endsection