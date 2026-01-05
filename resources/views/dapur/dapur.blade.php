@extends('layouts.dapur')

@section('title', 'Dapur - Billiard Class')

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

{{-- Include sidebar & main content styles --}}
@include('dapur.partials.sidebar-main-styles')

{{-- Include order card styles --}}
@include('dapur.partials.order-card-styles')

{{-- Include notification styles --}}
@include('dapur.partials.notification-styles')

@push('styles')
<style>
    /* Reports specific responsive styles */
    @media (max-width: 768px) {
        /* Reports summary mobile */
        #reportsSummary {
            flex-direction: column;
        }

        #reportsSummary > div {
            width: 100%;
            min-width: auto;
        }

        /* Export buttons mobile */
        #exportExcelContainer {
            flex-direction: column;
        }

        #exportExcelContainer button {
            width: 100%;
        }

        /* Table responsive */
        .reports-table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            min-width: 600px;
        }
    }

    @media (min-width: 769px) and (max-width: 1024px) {
        .filter-input-group {
            flex-wrap: wrap;
        }

        #reportsSummary {
            flex-wrap: wrap;
        }

        #reportsSummary > div {
            flex: 1 1 calc(50% - 0.5rem);
            min-width: 200px;
        }
    }
</style>
@endpush

@section('content')
    {{-- Logout Form --}}
    @include('dapur.partials.logout-form')

    {{-- Audio element for notification sound --}}
    <audio id="notificationSound" preload="auto" style="display: none;">
        <source id="notificationSoundSource" src="" type="audio/mpeg">
    </audio>

    {{-- Notification Overlay (Mobile Blur) --}}
    <div id="notificationOverlay" class="notification-overlay"></div>

    {{-- Notification Container --}}
    <div id="notificationContainer" class="notification-container"></div>

    <div class="max-w-7xl mx-auto">
        <div id="ordersSection">
            <!-- Client will render orders into this grid. Server will not pre-render order cards to avoid duplication -->
            <div class="grid grid-cols-[repeat(auto-fill,minmax(380px,1fr))] gap-6 max-md:grid-cols-1 max-md:gap-4"></div>
        </div>
    </div>

    @push('scripts')
    <script>
    // Mark inline dapur script as active to prevent duplicate external execution
    try { window.__DapurInlineInitialized = true; } catch (e) {}

    // Sidebar Toggle


        // Notification and Sound Functions
        const notificationSound = document.getElementById('notificationSound');
        const notificationContainer = document.getElementById('notificationContainer');
        const notificationOverlay = document.getElementById('notificationOverlay');
        let currentOrderIds = new Set();
        let isFirstLoad = true;
        let activeNotifications = new Set();

        // Load active notification sound from localStorage (set by dapur audio settings)
        async function loadActiveNotificationSound() {
            try {
                const savedAudio = localStorage.getItem('kitchenNotificationAudio');
                const audioType = localStorage.getItem('kitchenNotificationAudioType');
                
                const source = notificationSound ? notificationSound.querySelector('#notificationSoundSource') : null;
                
                if (!savedAudio || !source) {
                    // No audio selected, clear source
                    if (source) {
                        source.src = '';
                        if (notificationSound) {
                            notificationSound.pause();
                            notificationSound.currentTime = 0;
                        }
                    }
                    return;
                }
                
                if (audioType === 'database') {
                    // Load from database
                    const response = await fetch('/notification-sounds');
                    const sounds = await response.json();
                    const sound = sounds.find(s => s.filename === savedAudio);
                    if (sound) {
                        if (sound.file_path.startsWith('sounds/')) {
                            source.src = '{{ asset("storage") }}/' + sound.file_path;
                        } else {
                            source.src = '{{ asset("assets/sounds") }}/' + sound.filename;
                        }
                        notificationSound.load();
                    } else {
                        // Sound not found in database, clear
                        source.src = '';
                        if (notificationSound) {
                            notificationSound.pause();
            // Initialization is handled earlier (render initialOrders client-side, reconcile, then connect SSE)
                    localStorage.removeItem('kitchenNotificationAudio');
                    localStorage.removeItem('kitchenNotificationAudioType');
                }
            } catch (error) {
                console.error('Error loading active notification sound:', error);
                // On error, clear audio source
                const source = notificationSound ? notificationSound.querySelector('#notificationSoundSource') : null;
                if (source) {
                    source.src = '';
                }
            }
        }

        // Load active sound on page load
        loadActiveNotificationSound();

        // Sound unlock state (browsers block autoplay) and pending play flag
        let isSoundUnlocked = false;
        let pendingNotificationPlay = false;

        // Try to unlock audio on first user interaction
        function setupSoundUnlock() {
            const tryUnlock = async () => {
                if (isSoundUnlocked) return;
                if (!notificationSound || !notificationSound.querySelector('#notificationSoundSource') || !notificationSound.querySelector('#notificationSoundSource').src) return;

                try {
                    notificationSound.muted = true;
                    await notificationSound.play();
                    notificationSound.pause();
                    notificationSound.currentTime = 0;
                    notificationSound.muted = false;
                    isSoundUnlocked = true;

                    // If there was a pending play requested earlier, play now
                    if (pendingNotificationPlay) {
                        pendingNotificationPlay = false;
                        notificationSound.play().catch(() => {});
                    }
                } catch (e) {
                    // ignore - will try again on next interaction
                }
            };

            ['click', 'touchstart', 'keydown'].forEach(evt => {
                document.addEventListener(evt, tryUnlock, { once: true, capture: true });
            });
        }

        // Function to update overlay visibility
        function updateNotificationOverlay() {
            if (notificationOverlay && notificationContainer) {
                // Check if there are any visible notifications
                const visibleNotifications = notificationContainer.querySelectorAll('.notification:not(.hide)');
                
                if (visibleNotifications.length > 0) {
                    notificationOverlay.classList.add('show');
                } else {
                    notificationOverlay.classList.remove('show');
                    // Force hide with visibility
                    notificationOverlay.style.visibility = 'hidden';
                }
            }
        }

        // Function to show notification
        function showNotification(order) {
            const notification = document.createElement('div');
            notification.className = 'notification bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-hover)] text-white p-5 rounded-xl shadow-[0_8px_24px_rgba(var(--primary-color-rgb),0.4)] mb-4 flex items-center gap-4 min-w-[300px] relative z-[9999] animate-[slideInRight_0.5s_ease-out] sm:min-w-0 sm:w-full sm:p-4';
            notification.id = `notification-${order.id}`;
            
            const itemsText = order.order_items.map(item => 
                `${item.quantity}x ${item.menu_name}`
            ).join(', ');
            
            notification.innerHTML = `
                <div class="notification-icon">
                    <i class="ri-notification-3-line"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">Pesanan Baru!</div>
                    <div class="notification-message">
                        <strong>${order.customer_name}</strong> - Meja ${order.table_number} (${order.room})<br>
                        ${itemsText}<br>
                        <strong>Total: Rp${parseInt(order.total_price).toLocaleString('id-ID')}</strong>
                    </div>
                </div>
                <button class="notification-close" onclick="closeNotification(${order.id})">
                    <i class="ri-close-line"></i>
                </button>
            `;
            
            if (notificationContainer) {
                notificationContainer.appendChild(notification);
            }
            activeNotifications.add(order.id);
            
            // Small delay to ensure DOM is updated
            setTimeout(() => {
                updateNotificationOverlay();
            }, 10);
            
            // Auto close after 8 seconds
            setTimeout(() => {
                closeNotification(order.id);
            }, 8000);
            
            // Play sound only if audio is selected
            if (notificationSound) {
                const source = notificationSound.querySelector('#notificationSoundSource');
                // Only play if source has a valid src
                if (source && source.src && source.src !== '' && source.src !== window.location.href) {
                    // If unlocked, play immediately; otherwise mark pending and prompt unlock via next interaction
                    if (isSoundUnlocked) {
                        notificationSound.currentTime = 0;
                        notificationSound.play().catch(() => {});
                    } else {
                        pendingNotificationPlay = true;
                    }
                }
            }
        }

        // Function to close notification (global scope)
        window.closeNotification = function(orderId) {
            const notification = document.getElementById(`notification-${orderId}`);
            if (notification) {
                notification.classList.add('hide');
                activeNotifications.delete(orderId);
                
                // Update overlay immediately
                updateNotificationOverlay();
                
                setTimeout(() => {
                    notification.remove();
                    
                    // Final check - ensure overlay is hidden if no notifications
                    const remainingNotifications = notificationContainer.querySelectorAll('.notification:not(.hide)');
                    if (remainingNotifications.length === 0 && notificationOverlay) {
                        notificationOverlay.classList.remove('show');
                        notificationOverlay.style.visibility = 'hidden';
                        notificationOverlay.style.opacity = '0';
                    }
                }, 500);
            }
        }

        // Close overlay when clicked (mobile) - but prevent event bubbling
        if (notificationOverlay) {
            notificationOverlay.addEventListener('click', function(e) {
                // Only close if clicking directly on overlay, not on notification
                if (e.target === notificationOverlay) {
                    // Close all notifications
                    const notificationsToClose = Array.from(activeNotifications);
                    notificationsToClose.forEach(orderId => {
                        window.closeNotification(orderId);
                    });
                }
            });
        }
        
        // Prevent notification clicks from closing overlay
        if (notificationContainer) {
            notificationContainer.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // Function to render order card
        function renderOrderCard(order) {
            const items = order.order_items || [];
            const orderDate = new Date(order.created_at);
            const orderTime = orderDate.toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            // Calculate minutes elapsed
            const now = new Date();
            const minutesElapsed = Math.floor((now - orderDate) / (1000 * 60));
            const isWarning = minutesElapsed >= 15;
            
            const previewItems = items.slice(0, 4);
            const remainingCount = items.length > 4 ? items.length - 4 : 0;
            
            return `
                <div class="order-card-modern group relative bg-gradient-to-br from-[var(--primary-color)] to-[var(--primary-hover)] rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-[rgba(var(--primary-color-rgb),0.2)]" data-order-id="${order.id}">
                    <!-- Decorative Pattern -->
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white rounded-full -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white rounded-full -ml-12 -mb-12"></div>
                    </div>
                    
                    <!-- Card Header -->
                    <div class="relative px-6 pt-6 pb-4 border-b border-white/20">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center border border-white/30">
                                    <i class="ri-restaurant-line text-white text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-white font-bold text-sm">Order #${order.id}</p>
                                    <p class="text-white/80 text-xs">${orderTime} WIB</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                ${order.status === 'pending' ? `
                                    <div class="px-3 py-1.5 bg-yellow-500/30 backdrop-blur-sm rounded-lg border border-yellow-500/50">
                                        <span class="text-yellow-200 text-xs font-bold uppercase tracking-wider">⏳ Belum Selesai</span>
                                    </div>
                                ` : order.status === 'processing' ? `
                                    <div class="px-3 py-1.5 bg-blue-500/30 backdrop-blur-sm rounded-lg border border-blue-500/50">
                                        <span class="text-blue-200 text-xs font-bold uppercase tracking-wider">🟡 Sedang Diproses</span>
                                    </div>
                                ` : ''}
                                <div class="px-3 py-1.5 bg-white/20 backdrop-blur-sm rounded-lg border border-white/30">
                                    <span class="text-white text-xs font-bold uppercase tracking-wider">${order.room}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Time Indicator -->
                        ${(() => {
                            const progressPercent = Math.min(100, (minutesElapsed / 30) * 100);
                            const totalSeconds = Math.floor((now - orderDate) / 1000);
                            const stopwatchMinutes = Math.floor(totalSeconds / 60);
                            const stopwatchSeconds = totalSeconds % 60;
                            const startTimestamp = Math.floor(orderDate.getTime() / 1000);
                            return `
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="flex-1 h-1.5 rounded-full ${isWarning ? 'bg-red-500/50' : 'bg-white/20'}">
                                        <div class="h-full rounded-full ${isWarning ? 'bg-red-500' : 'bg-white/40'}" style="width: ${progressPercent}%"></div>
                                    </div>
                                    <span class="text-white/70 text-xs font-medium ${isWarning ? 'text-red-300 font-bold' : ''} stopwatch-timer" data-order-id="${order.id}" data-start-time="${startTimestamp}">
                                        ⏱ <span class="stopwatch-display">${String(stopwatchMinutes).padStart(2, '0')}:${String(stopwatchSeconds).padStart(2, '0')}</span>
                                    </span>
                                </div>
                            `;
                        })()}
                        
                        <!-- Menu Items Preview -->
                        <div class="flex flex-wrap gap-2">
                            ${previewItems.map(item => `
                                <div class="flex items-center gap-2 bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 border border-white/30">
                            <img src="${item.image ? (item.image.startsWith('http') ? item.image : '/' + item.image) : '/assets/img/default.png'}" 
                                 alt="${item.menu_name}" 
                                         class="w-6 h-6 rounded-full object-cover border border-white/50"
                                 onerror="this.src='/assets/img/default.png'">
                                    <span class="text-white text-xs font-semibold">${item.quantity}x</span>
                                </div>
                        `).join('')}
                            ${remainingCount > 0 ? `
                                <div class="flex items-center bg-white/20 backdrop-blur-sm rounded-lg px-2 py-1 border border-white/30">
                                    <span class="text-white text-xs font-semibold">+${remainingCount}</span>
                    </div>
                            ` : ''}
                    </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="relative px-6 py-5 bg-white/5 backdrop-blur-sm">
                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                    <i class="ri-user-line text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/70 text-xs font-medium mb-0.5">Nama Pemesan</p>
                                    <p class="text-white font-bold text-sm">${order.customer_name}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                    <i class="ri-table-line text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/70 text-xs font-medium mb-0.5">Meja</p>
                                    <p class="text-white font-bold text-sm">Meja ${order.table_number}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0 border border-white/30">
                                    <i class="ri-shopping-bag-line text-white text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white/70 text-xs font-medium mb-1">Pesanan</p>
                                    <div class="flex flex-wrap gap-1.5">
                                        ${items.map(item => `
                                            <span class="inline-block bg-white/20 backdrop-blur-sm px-2 py-1 rounded-md border border-white/30 text-white text-xs font-medium">
                                                ${item.quantity}x ${item.menu_name}
                                            </span>
                                        `).join('')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Footer -->
                    <div class="relative px-6 py-4 bg-white/10 backdrop-blur-sm border-t border-white/20 flex items-center justify-between">
                        <div>
                            <p class="text-white/70 text-xs font-medium mb-0.5">Total Harga</p>
                            <p class="text-white font-bold text-lg">Rp${parseInt(order.total_price).toLocaleString('id-ID')}</p>
                        </div>
                        ${order.status === 'pending' ? `
                            <button class="start-cooking-btn bg-blue-500 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 group/btn" data-order-id="${order.id}">
                                <i class="ri-play-circle-line text-base group-hover/btn:scale-110 transition-transform"></i>
                                <span>Mulai Masak</span>
                            </button>
                        ` : order.status === 'processing' ? `
                            <button class="complete-order-btn bg-white text-[var(--primary-color)] px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2 group/btn" data-order-id="${order.id}">
                                <i class="ri-checkbox-circle-line text-base group-hover/btn:rotate-12 transition-transform"></i>
                                <span>Selesai</span>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        // Variable untuk SSE connection
        let eventSource = null;
        let reconnectTimeout = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 10;

        // Function untuk update orders display
        function updateOrdersDisplay(orders) {
            const ordersSection = document.getElementById('ordersSection');
            if (!ordersSection) return;
            
            if (orders.length === 0) {
                ordersSection.innerHTML = '<div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg"><p>Belum ada pesanan</p></div>';
            } else {
                // Ensure there's only one grid container to prevent duplicate sets of cards
                const existingGrids = Array.from(ordersSection.querySelectorAll('.grid'));
                if (existingGrids.length > 1) {
                    // keep the first grid, remove the rest
                    existingGrids.slice(1).forEach(g => g.remove());
                }

                const ordersGrid = ordersSection.querySelector('.grid');
                if (ordersGrid) {
                    ordersGrid.innerHTML = orders.map(order => renderOrderCard(order)).join('');
                } else {
                    ordersSection.innerHTML = `
                        <div class="grid grid-cols-[repeat(auto-fill,minmax(380px,1fr))] gap-6 max-md:grid-cols-1 max-md:gap-4">
                            ${orders.map(order => renderOrderCard(order)).join('')}
                        </div>
                    `;
                }
                // Update stopwatches immediately after rendering
                updateStopwatches();
            }
        }

        // Function untuk handle new orders dari SSE
        function handleNewOrders(newOrders) {
            if (!newOrders || newOrders.length === 0) return;
            try { console.debug('handleNewOrders received IDs:', newOrders.map(o => Number(o.id))); } catch(e) {}
            const ordersSection = document.getElementById('ordersSection');
            if (!ordersSection) return;

            let ordersGrid = ordersSection.querySelector('.grid');
            if (!ordersGrid) {
                ordersSection.innerHTML = '<div class="grid grid-cols-[repeat(auto-fill,minmax(380px,1fr))] gap-6 max-md:grid-cols-1 max-md:gap-4"></div>';
                ordersGrid = ordersSection.querySelector('.grid');
            }

            // Jika ordersGrid masih null setelah update, hentikan operasi
            if (!ordersGrid) return;

            // Normalize and deduplicate incoming orders by id (prevent duplicates)
            const seenIds = new Set();
            newOrders.forEach(rawOrder => {
                const order = Object.assign({}, rawOrder);
                order.id = Number(order.id);

                if (seenIds.has(order.id)) return; // duplicate within payload
                seenIds.add(order.id);

                // If DOM anywhere already contains this order, track and skip insertion
                const existingDomGlobal = document.querySelector(`[data-order-id="${order.id}"]`);
                if (existingDomGlobal) {
                    try { console.debug(`handleNewOrders: skipping id ${order.id} because DOM already present`); } catch(e) {}
                    currentOrderIds.add(order.id);
                    return;
                }

                // Skip if already tracked in memory
                if (currentOrderIds.has(order.id)) return;

                // During the first load we avoid inserting cards coming from SSE/push
                // to prevent duplicates when server-rendered HTML already contains them.
                if (isFirstLoad) {
                    try { console.debug(`handleNewOrders: first load, tracking id ${order.id} but not inserting`); } catch(e) {}
                    currentOrderIds.add(order.id);
                    return;
                }

                // New order detected (normal runtime)
                currentOrderIds.add(order.id);

                // Insert new order at the beginning
                try { console.info(`handleNewOrders: inserting id ${order.id}`); } catch(e) {}
                const newOrderCard = renderOrderCard(order);
                ordersGrid.insertAdjacentHTML('afterbegin', newOrderCard);

                // Show notification once per new order (avoid duplicates)
                if (!activeNotifications.has(order.id)) {
                    showNotification(order);
                }
            });

            updateStopwatches();
        }

        // Remove duplicate DOM nodes that share the same data-order-id
        function dedupeDOMOrders() {
            try {
                const seen = new Set();
                const nodes = Array.from(document.querySelectorAll('[data-order-id]'));
                nodes.forEach(node => {
                    const id = String(node.getAttribute('data-order-id'));
                    if (! id) return;
                    if (seen.has(id)) {
                        try { console.debug(`dedupeDOMOrders: removing duplicate node for id ${id}`); } catch(e) {}
                        node.remove();
                    } else {
                        seen.add(id);
                    }
                });
            } catch (e) {
                // ignore
            }
        }

        // Function untuk fetch initial orders
        async function fetchInitialOrders() {
            try {
                const response = await fetch('/orders/active');
                const data = await response.json();
                
                    if (response.ok && data.orders) {
                    // Normalize IDs to numbers to avoid string/number mismatch
                    const orderIds = new Set(data.orders.map(o => Number(o.id)));

                    try { console.debug('fetchInitialOrders fetched IDs:', Array.from(orderIds)); } catch(e) {}

                    // If the server already rendered the same set of orders (server-side blade),
                    // skip re-rendering to avoid duplicate cards on refresh. We compare the
                    // currently tracked IDs (populated from server-rendered `initialOrders`)
                    // with the freshly fetched IDs. If they're identical, do nothing.
                    const areSetsEqual = (a, b) => {
                        if (a.size !== b.size) return false;
                        for (const v of a) if (!b.has(v)) return false;
                        return true;
                    };

                    if (areSetsEqual(currentOrderIds, orderIds)) {
                        // No change in orders; just ensure internal tracking and mark first load done
                        try { console.debug('fetchInitialOrders: sets equal, skipping re-render'); } catch(e) {}
                        currentOrderIds = orderIds;
                        isFirstLoad = false;
                        // Clean any accidental duplicate DOM nodes after skipping re-render
                        dedupeDOMOrders();
                    } else {
                        try { console.debug('fetchInitialOrders: sets differ, re-rendering orders'); } catch(e) {}
                        currentOrderIds = orderIds;
                        updateOrdersDisplay(data.orders);
                        // Ensure no duplicates after rendering
                        dedupeDOMOrders();
                        isFirstLoad = false;
                    }
                }
            } catch (error) {
                console.error('Error fetching initial orders:', error);
            }
        }

        // Function untuk fetch dan update orders (untuk refresh setelah action)
        async function fetchAndUpdateOrders() {
            try {
                const response = await fetch('/orders/active');
                const data = await response.json();
                
                if (response.ok && data.orders) {
                    const orderIds = new Set(data.orders.map(o => o.id));
                    currentOrderIds = orderIds;
                    updateOrdersDisplay(data.orders);
                }
            } catch (error) {
                console.error('Error fetching orders:', error);
            }
        }

        // Function untuk connect ke SSE
        function connectSSE() {
            // Close existing connection if any
            if (eventSource) {
                eventSource.close();
            }

            try {
                // Connect to the dapur-specific SSE endpoint
                eventSource = new EventSource('/dapur/orders/stream');
                
                eventSource.onmessage = function(event) {
                    try {
                        // Log raw SSE payload for debugging
                        try { console.debug('SSE message raw:', event.data); } catch(e) {}
                        const data = JSON.parse(event.data);

                        if (data.type === 'new_orders' && data.orders) {
                            handleNewOrders(data.orders);
                        } else {
                            try { console.debug('SSE message ignored, type:', data.type); } catch(e) {}
                        }
                    } catch (error) {
                        console.error('Error parsing SSE data:', error, event.data);
                    }
                };
                
                eventSource.onerror = function(error) {
                    console.error('SSE connection error:', error);
                    try { console.debug('EventSource readyState:', eventSource && eventSource.readyState); } catch(e) {}
                    eventSource.close();
                    
                    // Reconnect dengan exponential backoff
                    reconnectAttempts++;
                    if (reconnectAttempts < maxReconnectAttempts) {
                        const delay = Math.min(1000 * Math.pow(2, reconnectAttempts), 30000); // Max 30 seconds
                        try { console.debug(`Reconnecting SSE in ${delay}ms (attempt ${reconnectAttempts})`); } catch(e) {}
                        reconnectTimeout = setTimeout(() => {
                            console.log('Reconnecting to SSE...');
                            connectSSE();
                        }, delay);
                    } else {
                        console.error('Max reconnect attempts reached. Falling back to polling.');
                        // Fallback ke polling jika SSE gagal
                        startPollingFallback();
                    }
                };
                
                eventSource.onopen = function() {
                    console.info('SSE connection established');
                    reconnectAttempts = 0; // Reset reconnect attempts on successful connection
                };
                
            } catch (error) {
                console.error('Error creating SSE connection:', error);
                // Fallback ke polling jika SSE tidak didukung
                startPollingFallback();
            }
        }

        // Fallback polling jika SSE tidak tersedia
        let pollingInterval = null;
        function startPollingFallback() {
            if (pollingInterval) return; // Already polling
            
            console.log('Using polling fallback');
            pollingInterval = setInterval(async () => {
                try {
                    const response = await fetch('/orders/active');
                    const data = await response.json();
                    
                    if (response.ok && data.orders) {
                        // Normalize IDs
                        const newOrderIds = new Set(data.orders.map(o => Number(o.id)));
                        let hasNewOrder = false;

                        data.orders.forEach(rawOrder => {
                            const order = Object.assign({}, rawOrder);
                            order.id = Number(order.id);
                            if (!currentOrderIds.has(order.id)) {
                                hasNewOrder = true;
                                // Avoid duplicate notifications
                                if (!activeNotifications.has(order.id)) {
                                    showNotification(order);
                                }
                            }
                        });

                        currentOrderIds = newOrderIds;
                        updateOrdersDisplay(data.orders);
                }
            } catch (error) {
                    console.error('Polling error:', error);
            }
            }, 2000); // Poll every 2 seconds as fallback
        }

        // Stopwatch timer function
        function updateStopwatches() {
            const now = Math.floor(Date.now() / 1000);
            document.querySelectorAll('.stopwatch-timer').forEach(timer => {
                const startTime = parseInt(timer.getAttribute('data-start-time'));
                const elapsedSeconds = now - startTime;
                const minutes = Math.floor(elapsedSeconds / 60);
                const seconds = elapsedSeconds % 60;
                
                const display = timer.querySelector('.stopwatch-display');
                if (display) {
                    display.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    
                    // Update warning color if >= 15 minutes
                    if (minutes >= 15) {
                        timer.classList.add('text-red-300', 'font-bold');
                        timer.classList.remove('text-white/70');
                    } else {
                        timer.classList.remove('text-red-300', 'font-bold');
                        timer.classList.add('text-white/70');
                    }
                    
                    // Update progress bar
                    const progressBar = timer.closest('.flex.items-center')?.querySelector('.h-full.rounded-full');
                    if (progressBar) {
                        const progressPercent = Math.min(100, (minutes / 30) * 100);
                        progressBar.style.width = `${progressPercent}%`;
                        
                        const progressContainer = progressBar.parentElement;
                        if (minutes >= 15) {
                            progressContainer.classList.remove('bg-white/20');
                            progressContainer.classList.add('bg-red-500/50');
                            progressBar.classList.remove('bg-white/40');
                            progressBar.classList.add('bg-red-500');
                        } else {
                            progressContainer.classList.remove('bg-red-500/50');
                            progressContainer.classList.add('bg-white/20');
                            progressBar.classList.remove('bg-red-500');
                            progressBar.classList.add('bg-white/40');
                        }
                    }
                }
            });
        }

        // Start stopwatch updates every second
        setInterval(updateStopwatches, 1000);
        
        
        {{-- Include shift check script --}}
        @include('dapur.partials.shift-check-script')

        // Initialize order IDs on page load
        (function() {
            const initialOrders = @json($orders);

            // Render initial orders client-side to ensure single source-of-truth
            if (initialOrders && initialOrders.length > 0) {
                const ids = new Set(initialOrders.map(o => Number(o.id)));
                currentOrderIds = ids;
                updateOrdersDisplay(initialOrders);
            } else {
                // Ensure 'no orders' placeholder is shown
                updateOrdersDisplay([]);
            }

            // Still fetch server state to reconcile any differences, then connect SSE
            // Keep `isFirstLoad` true until the reconciliation completes — this
            // prevents races where SSE messages and the reconciliation both
            // insert the same cards.
            fetchInitialOrders().then(() => {
                // Mark first load complete only after we've reconciled server state
                isFirstLoad = false;
                connectSSE();
            });
        })();

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (eventSource) {
                eventSource.close();
            }
            if (reconnectTimeout) {
                clearTimeout(reconnectTimeout);
            }
            if (pollingInterval) {
                clearInterval(pollingInterval);
            }
        });

        // Handle start cooking button
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.start-cooking-btn')) {
                const button = e.target.closest('.start-cooking-btn');
                const orderId = button.getAttribute('data-order-id');
                
                if (!confirm('Mulai memproses pesanan ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`/orders/${orderId}/start-cooking`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        // Refresh orders to update status
                        fetchAndUpdateOrders();
                    } else {
                        alert(result.message || 'Gagal memulai proses pesanan');
                    }
                } catch (error) {
                    console.error('Error starting cooking:', error);
                    alert('Terjadi kesalahan saat memulai proses pesanan');
                }
            }
        });

        // Handle complete order
        document.addEventListener('click', async (e) => {
            if (e.target.closest('.complete-order-btn')) {
                const button = e.target.closest('.complete-order-btn');
                const orderId = button.getAttribute('data-order-id');
                
                if (!confirm('Apakah pesanan ini sudah selesai?')) {
                    return;
                }

                try {
                    const response = await fetch(`/orders/${orderId}/complete`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        // Remove order ID from tracking
                        currentOrderIds.delete(parseInt(orderId));
                        
                        // Remove order from orders section dengan animasi
                        const orderCard = button.closest('[data-order-id]');
                        if (orderCard) {
                            // Tambahkan animasi fade out dan slide
                            orderCard.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out, margin 0.3s ease-out';
                            orderCard.style.opacity = '0';
                            orderCard.style.transform = 'translateX(-20px) scale(0.95)';
                            orderCard.style.marginBottom = '0';
                            
                            setTimeout(() => {
                                orderCard.remove();
                                
                                // Check if no more orders
                                const ordersSection = document.getElementById('ordersSection');
                                if (ordersSection) {
                                    const orderCards = ordersSection.querySelectorAll('[data-order-id]');
                                    if (orderCards.length === 0) {
                                        // Jika tidak ada orders lagi, tampilkan pesan kosong
                                        const grid = ordersSection.querySelector('.grid');
                                        if (grid) {
                                            ordersSection.innerHTML = '<div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg"><p>Belum ada pesanan</p></div>';
                                        } else {
                                            ordersSection.innerHTML = '<div class="text-center py-16 px-8 text-gray-600 dark:text-gray-500 text-lg"><p>Belum ada pesanan</p></div>';
                                        }
                                    }
                                }
                            }, 300);
                        } else {
                            // Jika orderCard tidak ditemukan, refresh dari server
                            fetchAndUpdateOrders();
                        }
                    } else {
                        alert(result.message || 'Gagal menyelesaikan pesanan');
                    }
                } catch (error) {
                    console.error('Error completing order:', error);
                    alert('Terjadi kesalahan saat menyelesaikan pesanan');
                }
            }
        });

    </script>

    {{-- Include theme manager script --}}
    @include('dapur.partials.theme-manager')

    @endpush
@endsection

