@extends('layouts.dapur')

@section('title', 'Dashboard Dapur - Billiard Class')

{{-- Include shift calculation PHP block --}}
@include('dapur.partials.shift-calculation')

{{-- Include shift meta tags --}}
@include('dapur.partials.shift-meta')

{{-- Include theme initialization script --}}
@include('dapur.partials.theme-manager')

{{-- Include dynamic color variables --}}
@include('dapur.partials.color-variables')

{{-- Include common styles --}}
@include('dapur.partials.common-styles')

@push('styles')
<style>
    .order-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
        border-radius: 16px;
        padding: 24px;
        color: white;
        box-shadow: 0 10px 30px rgba(var(--primary-color-rgb), 0.3);
    }
    
    .order-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .order-status {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: bold;
    }
    
    .order-timer {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 16px 0;
    }
    
    .timer-dot {
        width: 8px;
        height: 8px;
        background: var(--primary-color);
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .order-detail {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 12px 0;
        font-size: 14px;
    }
    
    .order-detail i {
        font-size: 18px;
        opacity: 0.9;
    }
    
    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .btn-complete {
        background: white;
        color: var(--primary-color);
        padding: 10px 24px;
        border-radius: 12px;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
    }
    
    .btn-complete:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
    }
</style>
@endpush

{{-- Include sidebar & main content styles --}}
@include('dapur.partials.sidebar-main-styles')

@section('content')
    {{-- Logout Form --}}
    @include('dapur.partials.logout-form')

    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Dashboard Dapur</h1>
            <p class="text-slate-500 dark:text-gray-400">Kelola pesanan yang sedang diproses</p>
        </div>
            
            <div id="ordersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @if($orders->count() > 0)
                    @foreach($orders as $order)
                        <div class="order-card" data-order-id="{{ $order->id }}">
                            <div class="order-card-header">
                                <div>
                                    <h3 class="text-lg font-bold">Order #{{ $order->id }}</h3>
                                    <p class="text-sm opacity-90">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                                </div>
                                <span class="order-status">SEDANG DIPROSES</span>
                            </div>
                            
                            <div class="order-timer">
                                <span class="timer-dot"></span>
                                <span class="font-bold" id="timer-{{ $order->id }}">00:00</span>
                            </div>
                            
                            <div class="order-detail">
                                <i class="ri-user-line"></i>
                                <span>{{ $order->customer_name }}</span>
                            </div>
                            
                            <div class="order-detail">
                                <i class="ri-table-line"></i>
                                <span>Meja {{ $order->table_number }}</span>
                            </div>
                            
                            <div class="order-detail">
                                <i class="ri-building-line"></i>
                                <span>{{ $order->room }}</span>
                            </div>
                            
                            <div class="order-detail">
                                <i class="ri-shopping-cart-line"></i>
                                <span>
                                    @foreach($order->orderItems as $item)
                                        {{ $item->quantity }}x {{ $item->menu_name }}@if(!$loop->last), @endif
                                    @endforeach
                                </span>
                            </div>
                            
                            <div class="order-footer">
                                <div>
                                    <p class="text-sm opacity-90">Total Item</p>
                                    <p class="text-2xl font-bold">{{ number_format($order->orderItems->sum('quantity'), 0, ',', '.') }}</p>
                                </div>
                                <button onclick="completeOrder({{ $order->id }})" class="btn-complete">
                                    <i class="ri-checkbox-circle-line mr-2"></i>
                                    Selesai
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-16">
                        <i class="ri-inbox-line text-6xl text-slate-400 dark:text-gray-600 mb-4"></i>
                        <p class="text-slate-500 dark:text-gray-400 text-lg">Tidak ada pesanan yang sedang diproses</p>
                    </div>
                @endif
            </div>
        </div>
            </main>
        </div>
    </div>

@push('scripts')
<script>
    let eventSource = null;
    let orderTimers = {};
    let notificationAudio = null;
    let notificationAudioUrl = '';
    let isSoundUnlocked = false;
    let pendingNotificationPlay = false;
    let isUnlockingSound = false;
    let renderedOrderIds = new Set(); // Track orders already rendered to prevent duplicates
    let lastSSEEventId = null; // Track last SSE event for reconnection
    let sseReconnectDelay = 3000; // Start with 3 seconds, exponential backoff
    let sseReconnectAttempts = 0;
    
    // Initialize timers for existing orders
    @foreach($orders as $order)
        startTimer({{ $order->id }}, '{{ $order->created_at->toIso8601String() }}');
    @endforeach
    
    // Function to start timer
    function startTimer(orderId, createdAt) {
        const startTime = new Date(createdAt);
        
        function updateTimer() {
            const now = new Date();
            const diff = Math.floor((now - startTime) / 1000);
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;
            const timerEl = document.getElementById(`timer-${orderId}`);
            if (timerEl) {
                timerEl.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }
        }
        
        updateTimer();
        orderTimers[orderId] = setInterval(updateTimer, 1000);
    }
    
    // Function to fetch active orders
    async function fetchActiveOrders() {
        try {
            const response = await fetch('/dapur/orders/active');
            const data = await response.json();
            
            if (response.ok && data.orders) {
                updateOrdersDisplay(data.orders);
            }
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    }
    
    // Function to update orders display
    function updateOrdersDisplay(orders) {
        const container = document.getElementById('ordersContainer');
        if (!container) return;
        
        if (orders.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-16">
                    <i class="ri-inbox-line text-6xl text-slate-400 dark:text-gray-600 mb-4"></i>
                    <p class="text-slate-500 dark:text-gray-400 text-lg">Tidak ada pesanan yang sedang diproses</p>
                </div>
            `;
            renderedOrderIds.clear();
            return;
        }
        
        let html = '';
        const currentOrderIds = new Set(orders.map(o => o.id));
        
        orders.forEach(order => {
            // Skip if already rendered (prevent duplicates on jaringan jelek)
            if (renderedOrderIds.has(order.id)) {
                return;
            }
            
            const createdAt = new Date(order.created_at);
            const orderItems = order.order_items.map(item => `${item.quantity}x ${item.menu_name}`).join(', ');
            const totalItems = order.order_items.reduce((sum, item) => sum + (Number(item.quantity) || 0), 0);
            
            html += `
                <div class="order-card" data-order-id="${order.id}">
                    <div class="order-card-header">
                        <div>
                            <h3 class="text-lg font-bold">Order #${order.id}</h3>
                            <p class="text-sm opacity-90">${createdAt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })} ${createdAt.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB</p>
                        </div>
                        <span class="order-status">SEDANG DIPROSES</span>
                    </div>
                    
                    <div class="order-timer">
                        <span class="timer-dot"></span>
                        <span class="font-bold" id="timer-${order.id}">00:00</span>
                    </div>
                    
                    <div class="order-detail">
                        <i class="ri-user-line"></i>
                        <span>${order.customer_name}</span>
                    </div>
                    
                    <div class="order-detail">
                        <i class="ri-table-line"></i>
                        <span>Meja ${order.table_number}</span>
                    </div>
                    
                    <div class="order-detail">
                        <i class="ri-building-line"></i>
                        <span>${order.room}</span>
                    </div>
                    
                    <div class="order-detail">
                        <i class="ri-shopping-cart-line"></i>
                        <span>${orderItems}</span>
                    </div>
                    
                    <div class="order-footer">
                        <div>
                            <p class="text-sm opacity-90">Total Item</p>
                            <p class="text-2xl font-bold">${totalItems.toLocaleString('id-ID')}</p>
                        </div>
                        <button onclick="completeOrder(${order.id})" class="btn-complete">
                            <i class="ri-checkbox-circle-line mr-2"></i>
                            Selesai
                        </button>
                    </div>
                </div>
            `;
            
            renderedOrderIds.add(order.id);
            
            // Start timer for this order
            if (!orderTimers[order.id]) {
                startTimer(order.id, order.created_at);
            }
        });
        
        // Remove orders that are no longer in the list
        renderedOrderIds.forEach(orderId => {
            if (!currentOrderIds.has(orderId)) {
                renderedOrderIds.delete(orderId);
            }
        });
        
        // If there are new orders to add, append them
        if (html) {
            container.innerHTML += html;
        }
    }
    
    // Function to complete order
    async function completeOrder(orderId) {
        if (!confirm('Apakah pesanan ini sudah selesai?')) {
            return;
        }
        
        try {
            const response = await fetch(`/dapur/orders/${orderId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Remove order from display
                const orderCard = document.querySelector(`[data-order-id="${orderId}"]`);
                if (orderCard) {
                    orderCard.remove();
                }
                
                // Clear timer
                if (orderTimers[orderId]) {
                    clearInterval(orderTimers[orderId]);
                    delete orderTimers[orderId];
                }
                
                // Remove from rendered set
                renderedOrderIds.delete(orderId);
                
                // Refresh orders
                fetchActiveOrders();
            } else {
                alert(data.message || 'Gagal menyelesaikan pesanan');
            }
        } catch (error) {
            console.error('Error completing order:', error);
            alert('Terjadi kesalahan saat menyelesaikan pesanan');
        }
    }
    
    // Connect to SSE
    function connectSSE() {
        if (eventSource) {
            eventSource.close();
        }
        
        eventSource = new EventSource('/dapur/orders/stream');
        
        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                lastSSEEventId = event.lastEventId || null;
                
                if (data.type === 'new_orders' && data.orders) {
                    if (data.orders.length > 0) {
                        playNotificationSound();
                    }
                    updateOrdersDisplay(data.orders);
                    sseReconnectAttempts = 0; // Reset pada koneksi sukses
                    sseReconnectDelay = 3000; // Reset delay
                }
            } catch (error) {
                console.error('Error parsing SSE data:', error);
            }
        };
        
        eventSource.onerror = function(error) {
            console.error('SSE connection error:', error);
            eventSource.close();
            
            // Exponential backoff: 3s, 6s, 12s, 30s (max)
            sseReconnectAttempts++;
            const delay = Math.min(3000 * Math.pow(2, Math.min(sseReconnectAttempts - 1, 3)), 30000);
            sseReconnectDelay = delay;
            
            console.log(`Reconnecting SSE in ${delay}ms (attempt ${sseReconnectAttempts})`);
            setTimeout(connectSSE, delay);
        };
    }

    async function loadNotificationAudioFromSettings() {
        const savedAudio = localStorage.getItem('kitchenNotificationAudio') || '';
        const audioType = localStorage.getItem('kitchenNotificationAudioType') || '';

        if (!savedAudio || audioType !== 'database') {
            notificationAudioUrl = '';
            notificationAudio = null;
            return;
        }

        try {
            const response = await fetch('/notification-sounds');
            if (!response.ok) {
                notificationAudioUrl = '';
                notificationAudio = null;
                return;
            }

            const sounds = await response.json();
            const sound = sounds.find(s => s.filename === savedAudio);
            if (!sound) {
                notificationAudioUrl = '';
                notificationAudio = null;
                return;
            }

            const url = sound.file_path && sound.file_path.startsWith('sounds/')
                ? '{{ url("/notification-sounds") }}/' + sound.id + '/file'
                : '{{ asset("assets/sounds") }}/' + sound.filename;

            if (notificationAudioUrl !== url) {
                notificationAudioUrl = url;
                notificationAudio = new Audio(notificationAudioUrl);
                notificationAudio.preload = 'auto';
                notificationAudio.load();
            }
        } catch (error) {
            console.error('Error loading notification audio:', error);
            notificationAudioUrl = '';
            notificationAudio = null;
        }
    }

    function setupSoundUnlock() {
        const unlock = async () => {
            if (isSoundUnlocked) return;
            if (!notificationAudioUrl) return;
            if (isUnlockingSound) return;
            isUnlockingSound = true;

            try {
                if (!notificationAudio) {
                    notificationAudio = new Audio(notificationAudioUrl);
                    notificationAudio.preload = 'auto';
                }
                notificationAudio.muted = true;
                notificationAudio.currentTime = 0;
                await notificationAudio.play();
                notificationAudio.pause();
                notificationAudio.currentTime = 0;
                notificationAudio.muted = false;
                isSoundUnlocked = true;
                isUnlockingSound = false;

                document.removeEventListener('click', unlock, true);
                document.removeEventListener('touchstart', unlock, true);
                document.removeEventListener('keydown', unlock, true);

                if (pendingNotificationPlay) {
                    pendingNotificationPlay = false;
                    playNotificationSound();
                }
            } catch (e) {
                try {
                    if (notificationAudio) notificationAudio.muted = false;
                } catch (err) {}
                isUnlockingSound = false;
            }
        };

        document.addEventListener('click', unlock, true);
        document.addEventListener('touchstart', unlock, true);
        document.addEventListener('keydown', unlock, true);
    }

    function playNotificationSound() {
        if (!notificationAudioUrl) return;
        if (!notificationAudio) {
            notificationAudio = new Audio(notificationAudioUrl);
            notificationAudio.preload = 'auto';
        }

        try {
            notificationAudio.currentTime = 0;
            const playPromise = notificationAudio.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(() => {
                    pendingNotificationPlay = true;
                });
            }
        } catch (e) {}
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        (async () => {
            fetchActiveOrders();
            await loadNotificationAudioFromSettings();
            setupSoundUnlock();
            connectSSE();
        })();
        
        // Update clock
        function updateClock() {
            const now = new Date();
            const clockEl = document.getElementById('sidebar-clock');
            if (clockEl) {
                clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
        }
        updateClock();
        setInterval(updateClock, 1000);
    });
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (eventSource) {
            eventSource.close();
        }
        Object.values(orderTimers).forEach(timer => clearInterval(timer));
    });
</script>
@endpush
@endsection
